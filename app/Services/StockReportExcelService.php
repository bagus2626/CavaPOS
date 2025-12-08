<?php

namespace App\Services; // Sesuaikan namespace Anda

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Worksheet\Table; // Digunakan untuk type hinting/referensi

class StockReportExcelService
{
    /**
     * Export stock report to Excel using template
     */
    public function export($stocks, $filters = [])
    {
        // 1. Load template
        $templatePath = storage_path('app/templates/template-stock-report.xlsx');

        if (!file_exists($templatePath)) {
            throw new \Exception('Template file not found: ' . $templatePath);
        }

        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        $count = $stocks->count();
        $startDataRow = 2; // Baris tempat data pertama kali ditulis (asumsi header di baris 1)
        $numCols = 5; // Jumlah kolom yang diisi (A, B, C, D, E)

        // 2. Sisipkan baris kosong untuk menampung data (hanya jika > 1 baris)
        if ($count > 1) {
            // Sisipkan (count - 1) baris baru tepat sebelum baris template (row 2)
            $sheet->insertNewRowBefore($startDataRow + 1, $count - 1);
        }

        // 3. Isi data dan mempertahankan formatting dari baris template
        $row = $startDataRow;
        foreach ($stocks as $stock) {
            // Kolom A: Stock Code
            $sheet->setCellValue('A' . $row, $stock->stock_code);
            // Kolom B: Stock Name
            $sheet->setCellValue('B' . $row, $stock->stock_name);
            // Kolom C: Total Masuk (IN) - Menggunakan data terkonversi baru
            $sheet->setCellValue('C' . $row, $stock->lifetime_in ?? 0);
            // Kolom D: Total Keluar (OUT) - Menggunakan data terkonversi baru
            $sheet->setCellValue('D' . $row, $stock->lifetime_out ?? 0);
            // Kolom E: Unit Name
            $sheet->setCellValue('E' . $row, $stock->displayUnit->unit_name ?? 'N/A');

            $row++;
        }

        // 4. Update Rentang Tabel Excel (PENTING untuk format "Format as Table")
        // Asumsi nama tabel di template Anda adalah 'Table1' dan dimulai dari A1
        $tableName = 'Table1';
        $finalRow = $startDataRow + $count - 1;
        $finalCell = 'E' . $finalRow;

        $tableName = 'Table1';
        $finalRow = $startDataRow + $count - 1;
        $finalCell = 'E' . $finalRow;

        try {
            // PERBAIKAN: Gunakan getTableByName() untuk mengakses objek tabel
            $excelTable = $sheet->getTableByName($tableName);

            if ($excelTable) {
                // Update rentang tabel untuk mencakup semua data baru (A1 hingga E[finalRow])
                $excelTable->setRange("A1:{$finalCell}");
            }
        } catch (\Exception $e) {
            // Jika tabel tidak ditemukan, data tetap tersimpan, hanya formatting yang hilang
        }

        // 5. Generate filename dan Save
        $filename = $this->generateFilename($filters);
        $tempFile = storage_path('app/temp/' . $filename);

        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($tempFile);

        return $tempFile;
    }

    /**
     * Generate filename based on filters
     */
    private function generateFilename($filters)
    {
        $timestamp = Carbon::now()->format('YmdHis');
        $parts = ['Stock_Report'];

        if (!empty($filters['stock_type'])) {
            $parts[] = ucfirst($filters['stock_type']);
        }

        if (!empty($filters['partner_id'])) {
            $parts[] = 'Partner_' . $filters['partner_id'];
        }

        if (!empty($filters['month'])) {
            $parts[] = Carbon::parse($filters['month'])->format('Y_m');
        }

        $parts[] = $timestamp;

        return implode('_', $parts) . '.xlsx';
    }
}
