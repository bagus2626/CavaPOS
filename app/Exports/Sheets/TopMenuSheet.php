<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TopMenuSheet implements FromArray, WithHeadings, WithEvents, WithColumnWidths, WithStyles, WithTitle
{
    protected $exportFilters;
    protected $dataToExport;

    public function __construct(array $filters)
    {
        $this->exportFilters = $filters;
    }

    public function title(): string
    {
        return 'Menu Terlaris';
    }

    public function array(): array
    {
        $menuData = $this->fetchTopMenuData();
        $exportData = $this->formatMenuData($menuData);

        $this->dataToExport = $exportData;
        return $this->dataToExport;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Menu',
            'Total Terjual',
            'Total Pendapatan'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 40,
            'C' => 18,
            'D' => 25,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->dataToExport) + 1;

        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '059669'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle("A2:D{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C2:C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("D2:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->getRowDimension(1)->setRowHeight(25);
        for ($i = 2; $i <= $lastRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(20);
        }

        return $sheet;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = count($this->dataToExport) + 1;

                $sheet->getStyle("D2:D{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');

                $this->addGrandTotalRow($sheet, $lastRow);

                $sheet->setSelectedCells('A1');
            },
        ];
    }

    private function fetchTopMenuData()
    {
        $filters = $this->exportFilters;

        $query = DB::table('booking_orders')
            ->join('order_details', 'booking_orders.id', '=', 'order_details.booking_order_id')
            ->join('partner_products', 'order_details.partner_product_id', '=', 'partner_products.id')
            ->whereIn('booking_orders.order_status', ['PAID', 'PROCESSED', 'SERVED']);

        switch ($filters['period']) {
            case 'yearly':
                $query->whereYear('booking_orders.created_at', '>=', $filters['year_from'])
                    ->whereYear('booking_orders.created_at', '<=', $filters['year_to']);
                break;

            case 'monthly':
                $query->whereYear('booking_orders.created_at', $filters['month_year']);
                break;

            default:
                $query->whereDate('booking_orders.created_at', '>=', $filters['from'])
                    ->whereDate('booking_orders.created_at', '<=', $filters['to']);
                break;
        }

        return $query
            ->select(
                'partner_products.name as menu_name',
                DB::raw('SUM(order_details.quantity) as total_terjual'),
                DB::raw('SUM((order_details.base_price + order_details.options_price) * order_details.quantity) as total_pendapatan')
            )
            ->groupBy('partner_products.name')
            ->orderBy('total_terjual', 'desc')
            ->get();
    }

    private function formatMenuData($menuData): array
    {
        $exportData = [];
        $no = 1;

        foreach ($menuData as $menu) {
            $exportData[] = [
                'no' => $no++,
                'nama_menu' => $menu->menu_name,
                'total_terjual' => $menu->total_terjual,
                'total_pendapatan' => $menu->total_pendapatan,
            ];
        }

        return $exportData;
    }

    private function addGrandTotalRow(Worksheet $sheet, int $lastRow): void
    {
        $totalRow = $lastRow + 1;

        $sheet->setCellValue("A{$totalRow}", '');
        $sheet->setCellValue("B{$totalRow}", 'GRAND TOTAL');
        $sheet->setCellValue("C{$totalRow}", "=SUM(C2:C{$lastRow})");
        $sheet->setCellValue("D{$totalRow}", "=SUM(D2:D{$lastRow})");

        $sheet->getStyle("A{$totalRow}:D{$totalRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FEF3C7'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle("D{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("D{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getRowDimension($totalRow)->setRowHeight(25);
    }
}
