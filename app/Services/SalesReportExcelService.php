<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class SalesReportExcelService
{
    /**
     * Export data ke Excel dengan streaming approach.
     */
    public function export(array $filters, $controller)
    {
        set_time_limit(1800);
        ini_set('memory_limit', '1G');

        $fileName = $this->generateFileName($filters);
        $periodText = $this->getPeriodTextForExport($filters);

        $templatePath = storage_path('app/templates/template-excel.xlsx');
        $tempFilePath = storage_path('app/temp/' . uniqid() . '_' . $fileName);

        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        try {
            $this->streamDataToExcelTemplate(
                $templatePath,
                $filters,
                'Sheet1',
                $tempFilePath,
                $periodText,
                $controller
            );

            return response()->download($tempFilePath, $fileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            if (isset($tempFilePath) && file_exists($tempFilePath)) {
                unlink($tempFilePath);
            }
            throw $e;
        }
    }

    /**
     * Streaming data langsung tanpa load semua ke memory
     * Menggunakan Generator dan cursor untuk efisiensi maksimal
     */
    private function streamDataToExcelTemplate(
        string $templatePath,
        array $filters,
        string $sheetName,
        string $outputFilePath,
        string $periodText,
        $controller
    ): void {
        if (!file_exists($templatePath)) {
            throw new \Exception("File template tidak ditemukan di: {$templatePath}");
        }

        copy($templatePath, $outputFilePath);

        $zip = new \ZipArchive();
        if ($zip->open($outputFilePath) !== TRUE) {
            throw new \Exception('Gagal membuka file template yang disalin.');
        }

        $this->setCellValueInNamedSheet($zip, 'data', 'A5', $periodText);
        $this->setCellValueInNamedSheet($zip, 'grafik', 'I6', $periodText);

        $dateStyleId = $this->getStyleIdForDateFormat($zip);

        $sheetFileName = $this->findSheetFileName($zip, $sheetName);
        if (empty($sheetFileName)) {
            $zip->close();
            throw new \Exception("Worksheet dengan nama '{$sheetName}' tidak ditemukan.");
        }

        $zip->close();

        $tempDir = storage_path('app/temp/excel_' . uniqid());
        mkdir($tempDir, 0755, true);

        $zip->open($outputFilePath);
        $zip->extractTo($tempDir);
        $zip->close();

        $sheetFilePath = $tempDir . '/' . $sheetFileName;
        $this->streamDataToSheet($sheetFilePath, $filters, $dateStyleId, $controller);

        $this->updateSheetDimensionAndTable($tempDir, $sheetFileName, $filters, $controller);

        $this->repackZip($tempDir, $outputFilePath);

        $this->deleteDirectory($tempDir);
    }

    /**
     * Stream data langsung ke sheet file dengan cursor
     */
    private function streamDataToSheet(
        string $sheetFilePath,
        array $filters,
        int $dateStyleId,
        $controller
    ): void {
        $dom = new \DOMDocument();
        $dom->load($sheetFilePath);
        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        $sheetDataNode = $xpath->query('//main:sheetData')->item(0);
        if (!$sheetDataNode) {
            throw new \Exception("Struktur XML tidak valid");
        }

        $rowsToRemove = [];
        foreach ($xpath->query('./main:row[@r > 1]', $sheetDataNode) as $row) {
            $rowsToRemove[] = $row;
        }
        foreach ($rowsToRemove as $row) {
            $sheetDataNode->removeChild($row);
        }

        $currentRowNum = 2;
        $query = $controller->buildRawQueryForExport($filters);

        foreach ($query->cursor() as $item) {
            $rowData = [
                Carbon::parse($item->tanggal)->format('Y-m-d'),
                $item->booking_order,
                $item->menu,
                $item->kategori,
                (int) $item->jumlah,
                (float) $item->harga_satuan,
                $item->pembayaran,
                (float) ((float) $item->harga_satuan * (int) $item->jumlah),
            ];

            $newRow = $dom->createElement('row');
            $newRow->setAttribute('r', (string)$currentRowNum);

            foreach (array_values($rowData) as $colIndex => $cellData) {
                $cellRef = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1) . $currentRowNum;
                $newCell = $dom->createElement('c');
                $newCell->setAttribute('r', $cellRef);

                if ($colIndex === 0) {
                    try {
                        $excelDateValue = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(new \DateTime($cellData));
                        $newCell->setAttribute('s', (string)$dateStyleId);
                        $newCell->appendChild($dom->createElement('v', $excelDateValue));
                    } catch (\Exception $e) {
                        $newCell->setAttribute('t', 'inlineStr');
                        $is = $dom->createElement('is');
                        $is->appendChild($dom->createElement('t', htmlspecialchars($cellData ?? '')));
                        $newCell->appendChild($is);
                    }
                } else if (is_numeric($cellData) && !is_string($cellData)) {
                    $newCell->appendChild($dom->createElement('v', $cellData));
                } else {
                    $newCell->setAttribute('t', 'inlineStr');
                    $is = $dom->createElement('is');
                    $t = $dom->createElement('t', htmlspecialchars($cellData ?? ''));
                    $is->appendChild($t);
                    $newCell->appendChild($is);
                }
                $newRow->appendChild($newCell);
            }

            $sheetDataNode->appendChild($newRow);
            $currentRowNum++;

            if ($currentRowNum % 5000 === 0) {
                gc_collect_cycles();
            }
        }

        $dom->save($sheetFilePath);
    }

    /**
     * Update dimension dan table reference
     */
    private function updateSheetDimensionAndTable(
        string $tempDir,
        string $sheetFileName,
        array $filters,
        $controller
    ): void {
        $sheetFilePath = $tempDir . '/' . $sheetFileName;

        $totalDataRows = $controller->buildRawQueryForExport($filters)->count();
        $totalRows = $totalDataRows > 0 ? $totalDataRows + 1 : 1;
        $lastColumnIndex = 8;
        $lastColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColumnIndex);

        $dom = new \DOMDocument();
        $dom->load($sheetFilePath);
        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        $dimensionNode = $xpath->query('//main:dimension')->item(0);
        if ($dimensionNode instanceof \DOMElement) {
            $dimensionNode->setAttribute('ref', 'A1:' . $lastColumnLetter . $totalRows);
        }

        $dom->save($sheetFilePath);

        $tableXmlPath = $this->findTableXmlPathInDir($tempDir, $sheetFileName);
        if ($tableXmlPath) {
            $tableDom = new \DOMDocument();
            $tableDom->load($tableXmlPath);
            $tableNode = $tableDom->getElementsByTagName('table')->item(0);

            if ($tableNode instanceof \DOMElement) {
                $newRef = 'A1:' . $lastColumnLetter . $totalRows;
                $tableNode->setAttribute('ref', $newRef);

                $autoFilterNode = $tableDom->getElementsByTagName('autoFilter')->item(0);
                if ($autoFilterNode instanceof \DOMElement) {
                    $autoFilterNode->setAttribute('ref', $newRef);
                }
            }
            $tableDom->save($tableXmlPath);
        }
    }

    /**
     * Find table XML path in extracted directory
     */
    private function findTableXmlPathInDir(string $tempDir, string $sheetFileName): ?string
    {
        $relsPath = $tempDir . '/xl/worksheets/_rels/' . basename($sheetFileName) . '.rels';
        if (!file_exists($relsPath)) {
            return null;
        }

        $relsDom = new \DOMDocument();
        $relsDom->load($relsPath);

        foreach ($relsDom->getElementsByTagName('Relationship') as $rel) {
            if (str_contains($rel->getAttribute('Type'), '/table')) {
                return $tempDir . '/xl/tables/' . basename($rel->getAttribute('Target'));
            }
        }

        return null;
    }

    /**
     * Repack directory ke ZIP
     */
    private function repackZip(string $sourceDir, string $zipFilePath): void
    {
        $zip = new \ZipArchive();
        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            throw new \Exception('Gagal membuat ZIP file');
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceDir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($sourceDir) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
    }

    /**
     * Delete directory recursively
     */
    private function deleteDirectory(string $dir): bool
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }

    /**
     * HELPER: Cari sheet file name dari workbook
     */
    private function findSheetFileName(\ZipArchive $zip, string $sheetName): ?string
    {
        $workbookXml = $zip->getFromName('xl/workbook.xml');
        if (!$workbookXml) return null;

        $workbookDom = new \DOMDocument();
        $workbookDom->loadXML($workbookXml);

        foreach ($workbookDom->getElementsByTagName('sheet') as $sheet) {
            if ($sheet->getAttribute('name') == $sheetName) {
                $rId = $sheet->getAttribute('r:id');
                $relsXml = $zip->getFromName('xl/_rels/workbook.xml.rels');
                if ($relsXml) {
                    $relsDom = new \DOMDocument();
                    $relsDom->loadXML($relsXml);
                    foreach ($relsDom->getElementsByTagName('Relationship') as $rel) {
                        if ($rel->getAttribute('Id') == $rId) {
                            return 'xl/' . $rel->getAttribute('Target');
                        }
                    }
                }
            }
        }
        return null;
    }

    private function getStyleIdForDateFormat(\ZipArchive $zip, string $formatCode = 'yyyy-mm-dd'): int
    {
        $stylesXmlPath = 'xl/styles.xml';
        $stylesXml = $zip->getFromName($stylesXmlPath);
        if (!$stylesXml) return 0;

        $dom = new \DOMDocument();
        $dom->loadXML($stylesXml);
        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        $numFmtId = -1;
        foreach ($xpath->query('//main:numFmts/main:numFmt') as $numFmt) {
            if ($numFmt instanceof \DOMElement && $numFmt->getAttribute('formatCode') === $formatCode) {
                $numFmtId = (int)$numFmt->getAttribute('numFmtId');
                break;
            }
        }

        if ($numFmtId === -1) {
            $numFmtsNode = $xpath->query('//main:numFmts')->item(0);
            if ($numFmtsNode instanceof \DOMElement) {
                $nextId = 164;
                foreach ($xpath->query('./main:numFmt', $numFmtsNode) as $node) {
                    if ($node instanceof \DOMElement) {
                        $nextId = max($nextId, (int)$node->getAttribute('numFmtId') + 1);
                    }
                }
                $numFmtId = $nextId;

                $newNumFmt = $dom->createElement('numFmt');
                $newNumFmt->setAttribute('numFmtId', (string)$numFmtId);
                $newNumFmt->setAttribute('formatCode', $formatCode);
                $numFmtsNode->appendChild($newNumFmt);
                $numFmtsNode->setAttribute('count', (string)($numFmtsNode->getElementsByTagName('numFmt')->length));
            } else {
                return 0;
            }
        }

        $cellXfsNode = $xpath->query('//main:cellXfs')->item(0);
        if (!($cellXfsNode instanceof \DOMElement)) {
            return 0;
        }

        foreach ($xpath->query('./main:xf', $cellXfsNode) as $index => $xf) {
            if ($xf instanceof \DOMElement && $xf->hasAttribute('applyNumberFormat') && $xf->getAttribute('applyNumberFormat') == '1' && (int)$xf->getAttribute('numFmtId') === $numFmtId) {
                $zip->addFromString($stylesXmlPath, $dom->saveXML());
                return $index;
            }
        }

        $firstXf = $xpath->query('./main:xf', $cellXfsNode)->item(0);
        if ($firstXf instanceof \DOMElement) {
            $newXf = $firstXf->cloneNode(true);
            if ($newXf instanceof \DOMElement) {
                $newXf->setAttribute('numFmtId', (string)$numFmtId);
                $newXf->setAttribute('applyNumberFormat', '1');
                $cellXfsNode->appendChild($newXf);

                $xfIndex = (int)$cellXfsNode->getAttribute('count');
                $cellXfsNode->setAttribute('count', (string)($xfIndex + 1));

                $zip->addFromString($stylesXmlPath, $dom->saveXML());
                return $xfIndex;
            }
        }

        return 0;
    }

    private function setCellValueInNamedSheet(\ZipArchive $zip, string $sheetName, string $cellAddress, string $value): void
    {
        $sheetFileName = null;
        $workbookXml = $zip->getFromName('xl/workbook.xml');
        if (!$workbookXml) return;

        $workbookDom = new \DOMDocument();
        $workbookDom->loadXML($workbookXml);
        foreach ($workbookDom->getElementsByTagName('sheet') as $sheet) {
            if ($sheet->getAttribute('name') === $sheetName) {
                $rId = $sheet->getAttribute('r:id');
                $relsXml = $zip->getFromName('xl/_rels/workbook.xml.rels');
                if ($relsXml) {
                    $relsDom = new \DOMDocument();
                    $relsDom->loadXML($relsXml);
                    foreach ($relsDom->getElementsByTagName('Relationship') as $rel) {
                        if ($rel->getAttribute('Id') === $rId) {
                            $sheetFileName = 'xl/' . $rel->getAttribute('Target');
                            break 2;
                        }
                    }
                }
            }
        }

        if (!$sheetFileName || !$zip->locateName($sheetFileName)) {
            return;
        }

        $sheetXml = $zip->getFromName($sheetFileName);
        if (!$sheetXml) return;

        $sheetDom = new \DOMDocument();
        $sheetDom->loadXML($sheetXml);
        $xpath = new \DOMXPath($sheetDom);
        $xpath->registerNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        [$col, $rowNum] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($cellAddress);

        $rowNode = $xpath->query("//main:sheetData/main:row[@r='{$rowNum}']")->item(0);
        if (!$rowNode) {
            $sheetDataNode = $xpath->query('//main:sheetData')->item(0);
            if ($sheetDataNode instanceof \DOMElement) {
                $rowNode = $sheetDom->createElement('row');
                $rowNode->setAttribute('r', (string)$rowNum);
                $sheetDataNode->appendChild($rowNode);
            } else {
                return;
            }
        }

        $cellNode = $xpath->query("./main:c[@r='{$cellAddress}']", $rowNode)->item(0);
        if (!$cellNode) {
            if ($rowNode instanceof \DOMElement) {
                $cellNode = $sheetDom->createElement('c');
                $cellNode->setAttribute('r', $cellAddress);
                $rowNode->appendChild($cellNode);
            } else {
                return;
            }
        }

        if (!($cellNode instanceof \DOMElement)) return;

        while ($cellNode->hasChildNodes()) {
            $cellNode->removeChild($cellNode->firstChild);
        }

        $cellNode->setAttribute('t', 'inlineStr');
        $isNode = $sheetDom->createElement('is');
        $tNode = $sheetDom->createElement('t', htmlspecialchars($value ?? ''));
        $isNode->appendChild($tNode);
        $cellNode->appendChild($isNode);

        $zip->addFromString($sheetFileName, $sheetDom->saveXML());
    }


    private function generateFileName(array $filters): string
    {
        $baseName = 'laporan-penjualan';

        if (!empty($filters['partner_id'])) {
            $partner = User::find($filters['partner_id']);
            if ($partner) {
                $baseName .= '-' . strtolower(str_replace(' ', '-', $partner->name));
            }
        }

        switch ($filters['period']) {
            case 'yearly':
                if ($filters['year_from'] == $filters['year_to']) {
                    return "{$baseName}-tahunan-{$filters['year_from']}.xlsx";
                }
                return "{$baseName}-tahunan-{$filters['year_from']}-{$filters['year_to']}.xlsx";

            case 'monthly':
                // Parse dari format "YYYY-MM"
                $fromParts = explode('-', $filters['month_from']);
                $toParts = explode('-', $filters['month_to']);

                $yearFrom = $fromParts[0] ?? date('Y');
                $monthFrom = $fromParts[1] ?? '01';
                $yearTo = $toParts[0] ?? date('Y');
                $monthTo = $toParts[1] ?? '12';

                // Jika bulan dan tahun sama
                if ($filters['month_from'] == $filters['month_to']) {
                    return "{$baseName}-bulanan-{$yearFrom}-{$monthFrom}.xlsx";
                }

                // Jika tahun sama
                if ($yearFrom == $yearTo) {
                    return "{$baseName}-bulanan-{$yearFrom}-{$monthFrom}_sampai_{$monthTo}.xlsx";
                }

                // Jika tahun berbeda
                return "{$baseName}-bulanan-{$yearFrom}-{$monthFrom}_sampai_{$yearTo}-{$monthTo}.xlsx";

            default:
                $from = Carbon::parse($filters['from'])->format('d-m-Y');
                $to = Carbon::parse($filters['to'])->format('d-m-Y');

                if ($filters['from'] == $filters['to']) {
                    return "{$baseName}-harian-{$from}.xlsx";
                }
                return "{$baseName}-harian-{$from}_sampai_{$to}.xlsx";
        }
    }

    private function getPeriodTextForExport(array $filters): string
    {
        $periodText = "Periode: ";

        switch ($filters['period']) {
            case 'yearly':
                $periodText .= $filters['year_from'] == $filters['year_to']
                    ? $filters['year_from']
                    : "{$filters['year_from']} - {$filters['year_to']}";
                break;

            case 'monthly':
                $monthNames = [
                    '01' => 'Januari',
                    '02' => 'Februari',
                    '03' => 'Maret',
                    '04' => 'April',
                    '05' => 'Mei',
                    '06' => 'Juni',
                    '07' => 'Juli',
                    '08' => 'Agustus',
                    '09' => 'September',
                    '10' => 'Oktober',
                    '11' => 'November',
                    '12' => 'Desember'
                ];

                // Parse dari format "YYYY-MM"
                $fromParts = explode('-', $filters['month_from']);
                $toParts = explode('-', $filters['month_to']);

                $yearFrom = $fromParts[0] ?? date('Y');
                $monthFrom = $monthNames[$fromParts[1]] ?? 'Januari';

                $yearTo = $toParts[0] ?? date('Y');
                $monthTo = $monthNames[$toParts[1]] ?? 'Desember';

                // Jika bulan dan tahun sama
                if ($filters['month_from'] === $filters['month_to']) {
                    $periodText .= "{$monthFrom} {$yearFrom}";
                }
                // Jika tahun sama tapi bulan berbeda
                else if ($yearFrom === $yearTo) {
                    $periodText .= "{$monthFrom} - {$monthTo} {$yearFrom}";
                }
                // Jika tahun berbeda
                else {
                    $periodText .= "{$monthFrom} {$yearFrom} - {$monthTo} {$yearTo}";
                }
                break;

            default:
                $from = Carbon::parse($filters['from'])->format('d M Y');
                $to = Carbon::parse($filters['to'])->format('d M Y');
                $periodText .= $from == $to ? $from : "{$from} - {$to}";
                break;
        }

        return $periodText;
    }
}
