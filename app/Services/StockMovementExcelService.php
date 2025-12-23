<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Carbon\Carbon;

class StockMovementExcelService
{
    public function export($movements, $stock, $filters = [])
    {
        // 1. Load template
        $templatePath = storage_path('app/templates/template-stock-report-movement-detail.xlsx');

        if (!file_exists($templatePath)) {
            throw new \Exception('Template file not found: ' . $templatePath);
        }

        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Gunakan getCollection() jika $movements adalah paginator
        $movementData = ($movements instanceof \Illuminate\Pagination\LengthAwarePaginator)
            ? $movements->getCollection()
            : $movements;

        $count = $movementData->count();
        $startDataRow = 5; // Baris tempat data pertama kali ditulis (row 4 = header, row 5 = data)
        $numCols = 5; // Jumlah kolom yang diisi (A, B, C, D, E)

        // 2. Sisipkan baris kosong untuk menampung data (hanya jika > 1 baris)
        if ($count > 1) {
            $sheet->insertNewRowBefore($startDataRow + 1, $count - 1);
        }

        // 3. Isi data dan mempertahankan formatting dari baris template
        $row = $startDataRow;
        foreach ($movementData as $item) {
            // Ambil kuantitas mentah (selalu positif)
            $rawQty = $item->quantity;
            
            // Tentukan arah berdasarkan tipe di Model StockMovement
            $isOut = optional($item->movement)->type === 'out';
            $displayQty = abs($rawQty);

            // Konversi ke display unit
            $conversion = optional($item->stock->displayUnit)->base_unit_conversion_value ?? 1;
            $displayQty = $displayQty / $conversion;

            $qtySign = $isOut ? '-' : '+';
            
            $sheet->setCellValue('A' . $row, optional($item->movement->created_at)->format('d M Y'));
            $sheet->setCellValue('B' . $row, $isOut ? 'OUT' : 'IN');
            $categoryFormatted = ucwords(str_replace('_', ' ', $item->movement->category ?? ''));
            $sheet->setCellValue('C' . $row, $categoryFormatted);
            $sheet->setCellValue('D' . $row, $qtySign . number_format($displayQty, 2, '.', ''));
            $sheet->setCellValue('E' . $row, optional($item->stock->displayUnit)->unit_name ?? 'N/A');

            $row++;
        }

        // 4. Update Rentang Tabel Excel
        $tableName = 'Table1';
        $finalRow = $startDataRow + $count - 1;
        $finalCell = 'E' . $finalRow;

        try {
            $excelTable = $sheet->getTableByName($tableName);

            if ($excelTable) {
                $excelTable->setRange("A4:{$finalCell}");
            }
        } catch (\Exception $e) {
            // Jika tabel tidak ditemukan, data tetap tersimpan, hanya formatting yang hilang
        }

        // 5. Tambahkan informasi stock di bagian atas (Row 1-3)
        $sheet->setCellValue('A1', 'Stock Name: ' . $stock->stock_name);
        $sheet->setCellValue('A2', 'Stock Code: ' . $stock->stock_code);
        
        // Tambahkan informasi filter jika ada
        if (!empty($filters['month'])) {
            $monthFormatted = Carbon::parse($filters['month'])->translatedFormat('F Y');
            $sheet->setCellValue('C1', 'Period: ' . $monthFormatted);
        }
        
        if (!empty($filters['partner_id'])) {
            $partnerName = ($filters['partner_id'] === 'owner' || $filters['partner_id'] === '0') 
                ? 'Gudang Owner' 
                : ($stock->partner->name ?? 'N/A');
            $sheet->setCellValue('C2', 'Location: ' . $partnerName);
        }

        // 6. Generate filename dan Save
        $filename = $this->generateFilename($stock, $filters);
        $tempFile = storage_path('app/temp/' . $filename);

        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($tempFile);

        return $tempFile;
    }

    /**
     * Generate filename based on stock
     */
    private function generateFilename($stock, $filters)
    {
        $timestamp = Carbon::now()->format('YmdHis');
        $parts = ['Stock_Movement', $stock->stock_code];

        return implode('_', $parts) . '.xlsx';
    }
}