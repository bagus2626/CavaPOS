<?php

namespace App\Http\Controllers\Employee\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Store\Stock;
use App\Models\Store\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction\BookingOrder;
use App\Models\User;
use App\Services\StockRecalculationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class KitchenDashboardController extends Controller
{

    protected $recalculationService;

    public function __construct(StockRecalculationService $recalculationService)
    {
        $this->recalculationService = $recalculationService;
    }

    public function index()
    {
        return view('pages.employee.kitchen.dashboard.index');
    }

    public function getOrderQueue(Request $request)
    {
        try {
            $employee = Auth::guard('employee')->user();
            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $perPage = 10;
            $page = (int) $request->input('page', 1);
            $offset = ($page - 1) * $perPage;


            \Illuminate\Support\Facades\Log::info('Queue Request', [
                'page' => $page,
                'offset' => $offset,
                'per_page' => $perPage
            ]);

            $query = BookingOrder::with(['order_details.order_detail_options', 'table'])
                ->where('partner_id', $employee->partner_id)
                ->where('order_status', 'PAID')
                ->whereNull('cashier_process_id')
                ->whereNull('kitchen_process_id')
                ->orderBy('created_at', 'asc');

            $total = $query->count();
            $orders = $query->skip($offset)->take($perPage)->get();

            \Illuminate\Support\Facades\Log::info('Queue Response', [
                'total' => $total,
                'fetched' => $orders->count(),
                'has_more' => ($offset + $perPage) < $total
            ]);

            $queueOrders = $orders->map(function ($order, $index) use ($offset) {
                return $this->formatOrderData($order, $offset + $index + 1);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'queue_orders' => $queueOrders,
                    'total_waiting' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => (int) ceil($total / $perPage),
                    'has_more' => ($offset + $perPage) < $total,
                    'offset' => $offset
                ]
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Queue Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order queue',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getActiveOrders()
    {
        try {
            $employee = Auth::guard('employee')->user();
            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            // Ambil orders dan urutkan berdasarkan updated_at
            $orders = BookingOrder::with(['order_details.order_detail_options', 'table'])
                ->where('partner_id', $employee->partner_id)
                ->where('order_status', 'PROCESSED')
                ->whereNotNull('kitchen_process_id')
                ->whereNull('cashier_process_id')
                ->orderBy('updated_at', 'asc')
                ->get();

            // nomor urut otomatis 
            $activeOrders = $orders->map(function ($order, $index) {
                return $this->formatOrderData($order, null, $index + 1);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'active_orders' => $activeOrders,
                    'total_cooking' => $orders->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch active orders'
            ], 500);
        }
    }

    public function getServedOrders(Request $request)
    {
        try {
            $employee = Auth::guard('employee')->user();
            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }


            $perPage = 10;
            $page = (int) $request->input('page', 1);
            $offset = ($page - 1) * $perPage;

            \Illuminate\Support\Facades\Log::info('Served Orders Request', [
                'page' => $page,
                'offset' => $offset,
                'per_page' => $perPage
            ]);

            $query = BookingOrder::with(['order_details.order_detail_options', 'table'])
                ->where('partner_id', $employee->partner_id)
                ->where('order_status', 'SERVED')
                ->whereNotNull('kitchen_process_id')
                ->whereNull('cashier_process_id');


            if ($request->date && $request->date !== 'all') {
                $query->whereDate('created_at', $request->date);
            }


            $total = $query->count();
            $orders = $query->orderBy('created_at', 'desc')
                ->skip($offset)
                ->take($perPage)
                ->get();

            \Illuminate\Support\Facades\Log::info('Served Orders Response', [
                'total' => $total,
                'fetched' => $orders->count(),
                'has_more' => ($offset + $perPage) < $total
            ]);

            $servedOrders = $orders->map(function ($order) {
                return $this->formatServedOrderData($order);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'served_orders' => $servedOrders,
                    'total_served' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => (int) ceil($total / $perPage),
                    'has_more' => ($offset + $perPage) < $total,
                    'offset' => $offset
                ]
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Served Orders Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch served orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllServedOrders()
    {
        try {
            $employee = Auth::guard('employee')->user();
            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $orders = BookingOrder::with(['order_details.order_detail_options', 'table'])
                ->where('partner_id', $employee->partner_id)
                ->where('order_status', 'SERVED')
                ->orderBy('updated_at', 'desc')
                ->limit(500)
                ->get();

            $servedOrders = $orders->map(function ($order) {
                return $this->formatServedOrderData($order);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'served_orders' => $servedOrders,
                    'total_served' => $orders->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch all served orders'
            ], 500);
        }
    }

    public function pickUpOrder($orderId)
    {
        DB::beginTransaction();
        try {
            $employee = Auth::guard('employee')->user();
            $order = BookingOrder::where('id', $orderId)
                ->where('partner_id', $employee->partner_id)
                ->where('order_status', 'PAID')
                ->whereNull('kitchen_process_id')
                ->whereNull('cashier_process_id')
                ->first();

            if (!$order) {
                return response()->json(['success' => false, 'message' => 'Order not found'], 404);
            }


            $order->update([
                'order_status' => 'PROCESSED',
                'kitchen_process_id' => $employee->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order successfully picked up'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to pick up order'], 500);
        }
    }

    public function cancelOrder($orderId)
    {
        DB::beginTransaction();
        try {
            $employee = Auth::guard('employee')->user();

            $order = BookingOrder::where('id', $orderId)
                ->where('partner_id', $employee->partner_id)
                ->where('order_status', 'PROCESSED')
                ->where('kitchen_process_id', $employee->id)
                ->whereNull('cashier_process_id')
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found or cannot be cancelled'
                ], 404);
            }

            $order->update([
                'order_status' => 'PAID',
                'kitchen_process_id' => null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order successfully cancelled and returned to queue'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order'
            ], 500);
        }
    }


public function markAsServed($orderId)
{
    $cashier = Auth::user();
    $partner = User::findOrFail($cashier->partner_id);

    DB::beginTransaction();
    try {
        $order = BookingOrder::with([
            'order_details.partnerProduct.stock',
            'order_details.partnerProduct.recipes',
            'order_details.order_detail_options.option.stock',
            'order_details.order_detail_options.option.recipes',
        ])
            ->where('id', $orderId)
            ->where('partner_id', $cashier->partner_id)
            ->where('order_status', 'PROCESSED')
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found or not in PROCESSED status'], 404);
        }

        // Buat MASTER STOCK MOVEMENT untuk seluruh transaksi
        $masterMovement = StockMovement::create([
            'owner_id'   => $partner->owner_id,
            'partner_id' => $partner->id,
            'type'       => 'out',
            'category'   => 'sale',
        ]);

        // PENGURANGAN FISIK & PENCATATAN MOVEMENT ITEM
        foreach ($order->order_details as $detail) {
            $qty = $detail->quantity;
            $product = $detail->partnerProduct;

            if ($product) {
                // Pengurangan Produk Utama
                if ($product->stock_type === 'direct' && $product->always_available_flag === 0 && $product->stock) {
                    $this->processStockConsumption($product->stock, $qty, $masterMovement);
                } elseif ($product->stock_type === 'linked') {
                    $this->processRecipeConsumption($product->recipes, $qty, $masterMovement);
                }

                // Pengurangan Opsi Produk
                foreach ($detail->order_detail_options as $detailOption) {
                    $opt = $detailOption->option;
                    if (!$opt) continue;

                    if ($opt->stock_type === 'direct' && $opt->always_available_flag === 0 && $opt->stock) {
                        $this->processStockConsumption($opt->stock, $qty, $masterMovement);
                    } elseif ($opt->stock_type === 'linked') {
                        $this->processRecipeConsumption($opt->recipes, $qty, $masterMovement);
                    }
                }
            }
        }

        // Update Status Order
        $order->update([
            'order_status' => 'SERVED',
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Order successfully marked as served and stock consumed.'
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        // Logika Error Handling
        return response()->json(['success' => false, 'message' => 'Failed to mark as served: ' . $e->getMessage()], 500);
    }
}

    private function formatOrderData($order, $queueNumber = null, $activeQueueNumber = null)
{
    $orderTime = $order->order_status === 'PROCESSED'
        ? ($order->updated_at ? $order->updated_at->format('H:i') : '00:00')
        : ($order->created_at ? $order->created_at->format('H:i') : '00:00');

    $processDate = $order->order_status === 'PROCESSED' && $order->updated_at
        ? $order->updated_at->format('d M Y')
        : ($order->created_at ? $order->created_at->format('d M Y') : '');

    $orderDetails = $order->order_details ?? collect();
    $totalItems = $orderDetails->sum('quantity');

    $tableClass = $order->table->table_class ?? 'Indoor';
    $tableConfig = $this->getTableConfig($tableClass);

    return [
        'id' => $order->id,
        'queue_number' => $queueNumber,
        'active_queue_number' => $activeQueueNumber,
        'booking_order_code' => $order->booking_order_code ?? 'N/A',
        'customer_name' => $this->cleanCustomerName($order->customer_name),
        'order_status' => $order->order_status,
        'order_time' => $orderTime,
        'order_date' => $processDate,
        'total_items' => $totalItems,
        'table' => $order->table,
        'table_class' => $tableClass,
        'table_type_badge' => $tableConfig['badge'],
        'table_type_color' => $tableConfig['color'],
        'customer_order_note' => $order->customer_order_note ?? '',
        'cashier_process_id' => $order->cashier_process_id, // ✅ TAMBAHKAN INI
        'order_details' => $orderDetails->map(function ($detail) {
            return [
                'id' => $detail->id,
                'product_name' => $detail->product_name ?? 'Product',
                'quantity' => $detail->quantity ?? 1,
                'customer_note' => $detail->customer_note ?? '',
                'options' => ($detail->order_detail_options ?? collect())->map(function ($option) {
                    return [
                        'name' => $option->partner_product_option_name ?? 'Option'
                    ];
                })
            ];
        })
    ];
}

    private function formatServedOrderData($order)
    {
        $orderTime = $order->created_at ? $order->created_at->format('H:i') : '00:00';
        $servedTime = $order->updated_at ? $order->updated_at->format('H:i') : '00:00';
        $servedDate = $order->updated_at ? $order->updated_at->format('d M Y') : '';
        $orderDetails = $order->order_details ?? collect();
        $totalItems = $orderDetails->sum('quantity');

        $tableClass = $order->table->table_class ?? 'Indoor';
        $tableConfig = $this->getTableConfig($tableClass);

        return [
            'id' => $order->id,
            'booking_order_code' => $order->booking_order_code ?? 'N/A',
            'customer_name' => $this->cleanCustomerName($order->customer_name),
            'order_status' => $order->order_status,
            'order_time' => $orderTime,
            'served_time' => $servedTime,
            'order_date' => $servedDate,
            'total_items' => $totalItems,
            'table' => $order->table,
            'table_class' => $tableClass,
            'table_type_badge' => $tableConfig['badge'],
            'table_type_color' => $tableConfig['color'],
            'order_details' => $orderDetails->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'product_name' => $detail->product_name ?? 'Product',
                    'quantity' => $detail->quantity ?? 1,
                    'customer_note' => $detail->customer_note ?? '',
                    'options' => ($detail->order_detail_options ?? collect())->map(function ($option) {
                        return [
                            'name' => $option->partner_product_option_name ?? 'Option'
                        ];
                    })
                ];
            })
        ];
    }

    private function getTableConfig($tableClass)
    {
        $configs = [
            'Indoor' => ['badge' => 'Indoor', 'color' => 'blue'],
            'Outdoor' => ['badge' => 'Outdoor', 'color' => 'green'],
            'REGULER' => ['badge' => 'Reguler', 'color' => 'gray']
        ];

        return $configs[$tableClass] ?? ['badge' => $tableClass, 'color' => 'gray'];
    }

    private function cleanCustomerName($name)
    {
        if (empty($name)) return 'Customer';

        $cleaned = preg_replace('/\bguest\b/i', '', $name);
        $cleaned = str_replace('-', ' ', $cleaned);
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        $cleaned = trim($cleaned);

        return $cleaned ?: 'Customer';
    }

    private function processStockConsumption(Stock $stock, int $qty, StockMovement $masterMovement): void
    {
        $reservedColumnExists = Schema::hasColumn('stocks', 'quantity_reserved');

        $updateData = [
            // Mengurangi kolom quantity fisik
            'quantity' => DB::raw('quantity - ' . $qty)
        ];

        if ($reservedColumnExists) {
            // Mengurangi kolom quantity_reserved (membersihkan reservasi)
            $updateData['quantity_reserved'] = DB::raw('quantity_reserved - ' . $qty);
        }

        $stock->update($updateData);

        $masterMovement->items()->create([
            'stock_id' => $stock->id,
            'quantity' => $qty,
            'unit_price' => $stock->last_price_per_unit ?? 0,
        ]);
    }

    private function processRecipeConsumption($recipes, int $orderedQuantity, StockMovement $masterMovement): void
    {
        foreach ($recipes as $recipe) {
            $ingredientStock = Stock::find($recipe->stock_id);

            if (!$ingredientStock) {
                continue;
            }

            $quantityPerUnit = $recipe->quantity_used;
            $totalQuantityToConsume = $quantityPerUnit * $orderedQuantity;

            // 1. Pengurangan Stok Fisik (quantity)
            $ingredientStock->decrement('quantity', $totalQuantityToConsume);

            // 2. Pengurangan Reservasi (quantity_reserved)
            if (Schema::hasColumn('stocks', 'quantity_reserved')) {
                $ingredientStock->decrement('quantity_reserved', $totalQuantityToConsume);
            }

            // 3. Pencatatan Movement Item
            $masterMovement->items()->create([
                'stock_id' => $ingredientStock->id,
                'quantity' => $totalQuantityToConsume,
                'unit_price' => $ingredientStock->last_price_per_unit ?? 0,
            ]);

            // PANGGIL RECALCULATION SERVICE
            $this->recalculationService->recalculateLinkedProducts($ingredientStock);
        }
    }
}
