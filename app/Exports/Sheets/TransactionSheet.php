<?php

namespace App\Exports\Sheets;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
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

class TransactionSheet implements FromArray, WithHeadings, WithEvents, WithColumnWidths, WithStyles, WithTitle
{
    protected $exportFilters;
    protected $dataToExport;
    protected $rowCounts;

    public function __construct(array $filters)
    {
        $this->exportFilters = $filters;
    }

    public function title(): string
    {
        return 'Detail Transaksi';
    }

    public function array(): array
    {
        $items = $this->fetchTransactionData();
        $groupedData = $this->groupTransactionsByDate($items);
        $exportData = $this->formatDataForExport($groupedData);

        $this->dataToExport = $exportData;
        return $this->dataToExport;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Booking Order',
            'Menu',
            'Quantity',
            'Harga',
            'Total Harga',
            'Total Harga Booking Order'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 20,
            'C' => 35,
            'D' => 12,
            'E' => 15,
            'F' => 18,
            'G' => 25,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->dataToExport) + 1;

        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'],
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

        $sheet->getStyle("A2:G{$lastRow}")->applyFromArray([
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
        $sheet->getStyle("B2:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("D2:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("E2:G{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

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

                $this->mergeDateCells($sheet);
                $this->mergeOrderCells($sheet);
                $this->applyCurrencyFormat($sheet);

                $sheet->setSelectedCells('A1');
            },
        ];
    }

    private function fetchTransactionData()
    {
        $filters = $this->exportFilters;
        $ownerId = auth('owner')->id();

        $query = DB::table('booking_orders')
            ->join('order_details', 'booking_orders.id', '=', 'order_details.booking_order_id')
            ->join('partner_products', 'order_details.partner_product_id', '=', 'partner_products.id')
            ->whereIn('booking_orders.order_status', ['PAID', 'PROCESSED', 'SERVED']);

        // CRITICAL FIX: Apply partner filter
        if (!empty($filters['partner_id'])) {
            // Specific partner selected
            $query->where('booking_orders.partner_id', $filters['partner_id']);
        } else {
            // All owner's partners
            $partnerIds = User::where('owner_id', $ownerId)
                ->where('role', 'partner')
                ->pluck('id');

            $query->whereIn('booking_orders.partner_id', $partnerIds);
        }

        // Apply date filters
        switch ($filters['period']) {
            case 'yearly':
                $query->whereYear('booking_orders.created_at', '>=', $filters['year_from'])
                    ->whereYear('booking_orders.created_at', '<=', $filters['year_to']);
                break;

            case 'monthly':
                $year = $filters['month_year'];
                $monthFrom = $filters['month_from'];
                $monthTo = $filters['month_to'];

                $startDate = Carbon::createFromDate($year, $monthFrom, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($year, $monthTo, 1)->endOfMonth();

                $query->whereBetween('booking_orders.created_at', [$startDate, $endDate]);
                break;

            default:
                $query->whereDate('booking_orders.created_at', '>=', $filters['from'])
                    ->whereDate('booking_orders.created_at', '<=', $filters['to']);
                break;
        }

        return $query
            ->select(
                'booking_orders.id as booking_order_id',
                'booking_orders.created_at',
                'booking_orders.booking_order_code',
                'booking_orders.total_order_value',
                'partner_products.name as product_name',
                'order_details.quantity',
                'order_details.base_price',
                'order_details.options_price'
            )
            ->orderBy('booking_orders.created_at', 'desc')
            ->orderBy('booking_orders.id', 'asc')
            ->get();
    }

    private function groupTransactionsByDate($items): array
    {
        $groupedData = [];

        foreach ($items as $item) {
            $date = Carbon::parse($item->created_at)->format('d/m/y');
            $orderId = $item->booking_order_id;

            if (!isset($groupedData[$date])) {
                $groupedData[$date] = [];
            }

            if (!isset($groupedData[$date][$orderId])) {
                $groupedData[$date][$orderId] = [
                    'order_code' => $item->booking_order_code,
                    'total_order_value' => $item->total_order_value,
                    'items' => []
                ];
            }

            $itemPrice = $item->base_price + $item->options_price;
            $totalPrice = $itemPrice * $item->quantity;

            $groupedData[$date][$orderId]['items'][] = [
                'menu' => $item->product_name,
                'quantity' => $item->quantity,
                'harga' => $itemPrice,
                'total_harga' => $totalPrice,
            ];
        }

        return $groupedData;
    }

    private function formatDataForExport(array $groupedData): array
    {
        $exportData = [];
        $this->rowCounts = ['dates' => [], 'orders' => []];

        foreach ($groupedData as $date => $orders) {
            $isFirstOrderInDate = true;
            $dateOrderCount = 0;

            foreach ($orders as $orderData) {
                $isFirstItemInOrder = true;
                $itemCountInOrder = count($orderData['items']);
                $dateOrderCount += $itemCountInOrder;

                foreach ($orderData['items'] as $itemData) {
                    $row = [
                        'tanggal' => $isFirstOrderInDate ? $date : '',
                        'booking_order' => $isFirstItemInOrder ? $orderData['order_code'] : '',
                        'menu' => $itemData['menu'],
                        'quantity' => $itemData['quantity'],
                        'harga' => $itemData['harga'],
                        'total_harga' => $itemData['total_harga'],
                        'total_harga_booking_order' => $isFirstItemInOrder ? $orderData['total_order_value'] : '',
                    ];

                    $exportData[] = $row;
                    $isFirstOrderInDate = false;
                    $isFirstItemInOrder = false;
                }

                $this->rowCounts['orders'][] = $itemCountInOrder;
            }

            $this->rowCounts['dates'][] = $dateOrderCount;
        }

        return $exportData;
    }

    private function mergeDateCells(Worksheet $sheet): void
    {
        $currentRow = 2;

        foreach ($this->rowCounts['dates'] ?? [] as $count) {
            if ($count > 1) {
                $endRow = $currentRow + $count - 1;
                $sheet->mergeCells("A{$currentRow}:A{$endRow}");
                $sheet->getStyle("A{$currentRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            }
            $currentRow += $count;
        }
    }

    private function mergeOrderCells(Worksheet $sheet): void
    {
        $currentRow = 2;

        foreach ($this->rowCounts['orders'] ?? [] as $count) {
            if ($count > 1) {
                $endRow = $currentRow + $count - 1;

                $sheet->mergeCells("B{$currentRow}:B{$endRow}");
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->mergeCells("G{$currentRow}:G{$endRow}");
                $sheet->getStyle("G{$currentRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            }
            $currentRow += $count;
        }
    }

    private function applyCurrencyFormat(Worksheet $sheet): void
    {
        $lastRow = count($this->dataToExport) + 1;

        $sheet->getStyle("E2:G{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');

        $sheet->getStyle("G2:G{$lastRow}")->applyFromArray([
            'font' => ['bold' => true],
        ]);
    }
}
