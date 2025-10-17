<?php

namespace App\Http\Controllers\Employee\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction\BookingOrder;
use App\Models\Transaction\OrderDetail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KitchenDashboardController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.employee.kitchen.dashboard.index');
    }

    /**
     * GET ORDER QUEUE (PAID + WAITING)
     */
    public function getOrderQueue(Request $request)
    {
        try {
            $employee = Auth::guard('employee')->user();
            
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            Log::debug('🔍 [QUEUE DEBUG] Fetching order queue', [
                'partner_id' => $employee->partner_id,
                'employee_id' => $employee->id
            ]);

            // Query dengan kondisi yang benar - hanya berdasarkan order_status
            $orders = BookingOrder::with([
                    'order_details' => function($query) {
                        $query->select([
                            'id', 'booking_order_id', 'partner_product_id',
                            'product_name', 'quantity', 'base_price', 
                            'options_price', 'customer_note'
                        ]);
                    },
                    'order_details.order_detail_options' => function($query) {
                        $query->select([
                            'id', 'order_detail_id', 'partner_product_option_name', 'price'
                        ]);
                    }
                ])
                ->where('partner_id', $employee->partner_id)
                ->where('order_status', 'PAID') // Hanya order dengan status PAID
                ->orderBy('created_at', 'asc')
                ->get();

            Log::debug('✅ [QUEUE DEBUG] Query results', [
                'total_orders' => $orders->count(),
                'order_ids' => $orders->pluck('id')->toArray()
            ]);

            // Transform data
            $queueOrders = $orders->map(function($order, $index) {
                return $this->transformOrderData($order, $index + 1);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'queue_orders' => $queueOrders,
                    'total_waiting' => $orders->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ [QUEUE DEBUG] Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order queue: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET ACTIVE ORDERS - Order dengan order_status PROCESSED
     */
    public function getActiveOrders(Request $request)
    {
        try {
            $employee = Auth::guard('employee')->user();
            
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            Log::info('🔥 [ACTIVE ORDERS DEBUG] Fetching active orders', [
                'partner_id' => $employee->partner_id,
                'employee_id' => $employee->id
            ]);

            // Query dengan kondisi PROCESSED saja
            $orders = BookingOrder::with([
                    'order_details' => function($query) {
                        $query->select([
                            'id', 'booking_order_id', 'partner_product_id',
                            'product_name', 'quantity', 'base_price', 
                            'options_price', 'customer_note',
                            'status', 'done_flag'
                        ]);
                    },
                    'order_details.order_detail_options' => function($query) {
                        $query->select([
                            'id', 'order_detail_id', 'partner_product_option_name', 'price'
                        ]);
                    }
                ])
                ->where('partner_id', $employee->partner_id)
                ->where('order_status', 'PROCESSED') // ORDER STATUS: PROCESSED
                ->orderBy('updated_at', 'asc')
                ->get();

            Log::info('✅ [ACTIVE ORDERS DEBUG] Final query results', [
                'total_orders_loaded' => $orders->count(),
                'order_ids_loaded' => $orders->pluck('id')->toArray(),
                'order_details' => $orders->map(function($order) {
                    return [
                        'id' => $order->id,
                        'code' => $order->booking_order_code,
                        'order_status' => $order->order_status,
                        'order_details_count' => $order->order_details ? $order->order_details->count() : 0
                    ];
                })
            ]);

            $activeOrders = $orders->map(function($order) {
                return $this->transformOrderData($order);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'active_orders' => $activeOrders,
                    'total_cooking' => $orders->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ [ACTIVE ORDERS DEBUG] Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch active orders: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET SERVED ORDERS - Order dengan order_status SERVED
     */
    public function getServedOrders(Request $request)
    {
        try {
            $employee = Auth::guard('employee')->user();
            
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $query = BookingOrder::with([
                    'order_details' => function($query) {
                        $query->select([
                            'id', 'booking_order_id', 'partner_product_id',
                            'product_name', 'quantity', 'base_price', 
                            'options_price', 'customer_note'
                        ]);
                    },
                    'order_details.order_detail_options' => function($query) {
                        $query->select([
                            'id', 'order_detail_id', 'partner_product_option_name', 'price'
                        ]);
                    }
                ])
                ->where('partner_id', $employee->partner_id)
                ->where('order_status', 'SERVED'); // ORDER STATUS: SERVED

            // Filter by date
            if ($request->has('date') && $request->get('date') !== 'all') {
                $date = $request->get('date');
                try {
                    $parsedDate = Carbon::parse($date)->format('Y-m-d');
                    $query->whereDate('updated_at', $parsedDate);
                } catch (\Exception $e) {
                    // Jika date invalid, tampilkan semua
                }
            }

            $orders = $query->orderBy('updated_at', 'desc')
                ->limit(200)
                ->get();

            Log::debug('✅ SERVED ORDERS RESULTS', [
                'total_orders' => $orders->count(),
                'date_filter' => $request->get('date', 'all')
            ]);

            $servedOrders = $orders->map(function($order) {
                return $this->transformOrderData($order);
            });

            $appliedDate = $request->get('date', 'all');
            $displayDate = $appliedDate === 'all' ? 'All Dates' : Carbon::parse($appliedDate)->format('d/m/Y');

            return response()->json([
                'success' => true,
                'data' => [
                    'served_orders' => $servedOrders,
                    'total_served' => $orders->count(),
                    'applied_date' => $appliedDate,
                    'display_date' => $displayDate
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ SERVED ORDERS ERROR: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch served orders: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * PICK UP ORDER - Ambil order dari antrian (PAID → PROCESSED)
     */
    public function pickUpOrder(Request $request, $orderId)
    {
        DB::beginTransaction();
        try {
            $employee = Auth::guard('employee')->user();
            

            Log::info('🔍 [PICKUP FIXED] Starting pickup process', [
                'order_id' => $orderId,
                'employee_id' => $employee->id,
                'partner_id' => $employee->partner_id
            ]);

            // Cari order dengan kondisi PAID saja
            $order = BookingOrder::where('id', $orderId)
                ->where('partner_id', $employee->partner_id)
                ->where('order_status', 'PAID')
                ->lockForUpdate()
                ->first();

            if (!$order) {
                DB::rollBack();
                
                Log::warning('❌ [PICKUP FIXED] Order not found or invalid', [
                    'order_id' => $orderId,
                    'expected_conditions' => [
                        'partner_id' => $employee->partner_id,
                        'order_status' => 'PAID'
                    ]
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }
            
            if ($order->kitchen_process_id !== null || $order->cashier_process_id !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order already taken by another chef'
                ], 404);
            }


            Log::info('✅ [PICKUP FIXED] Order found, updating to PROCESSED', [
                'order_id' => $order->id,
                'current_order_status' => $order->order_status
            ]);

            // UPDATE: order_status ke PROCESSED
            $order->update([
                'order_status' => 'PROCESSED',
                'kitchen_process_id' => $employee->id,
                'updated_at' => now()
            ]);

            $order->refresh();

            Log::info('🎯 [PICKUP FIXED] Order successfully picked up', [
                'order_id' => $order->id,
                'order_code' => $order->booking_order_code,
                'new_order_status' => $order->order_status, // Sekarang PROCESSED
                'chef_id' => $employee->id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order successfully picked up',
                'data' => [
                    'order_id' => $order->id,
                    'order_code' => $order->booking_order_code,
                    'order_status' => $order->order_status, // Sekarang PROCESSED
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ [PICKUP FIXED] Pick up order error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to pick up order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * MARK AS SERVED - Ubah status dari PROCESSED ke SERVED
     */
    public function markAsServed(Request $request, $orderId)
    {
        DB::beginTransaction();
        try {
            $employee = Auth::guard('employee')->user();

            Log::info('✅ [SERVE FIXED] Starting mark as served process', [
                'order_id' => $orderId,
                'employee_id' => $employee->id,
                'partner_id' => $employee->partner_id
            ]);

            // Cari order dengan kondisi PROCESSED
            $order = BookingOrder::where('id', $orderId)
                ->where('partner_id', $employee->partner_id)
                ->where('order_status', 'PROCESSED') // Pastikan order_status PROCESSED
                ->first();

            if (!$order) {
                DB::rollBack();
                
                Log::error('❌ [SERVE FIXED] Order tidak ditemukan atau tidak dalam status PROCESSED', [
                    'order_id' => $orderId,
                    'employee_id' => $employee->id
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Order tidak ditemukan atau tidak dalam status PROCESSED'
                ], 404);
            }

            Log::info('📊 [SERVE FIXED] Order ditemukan, updating to SERVED', [
                'order_id' => $order->id,
                'order_code' => $order->booking_order_code,
                'current_order_status' => $order->order_status
            ]);

            // UPDATE: order_status diubah ke SERVED
            $order->update([
                'order_status' => 'SERVED', // DIUBAH dari PROCESSED ke SERVED
                'updated_at' => now()
            ]);

            DB::commit();

            $order->refresh();

            Log::info('🎉 [SERVE FIXED] Order successfully marked as served', [
                'order_id' => $order->id,
                'order_code' => $order->booking_order_code,
                'new_order_status' => $order->order_status, // Sekarang SERVED
                'served_by' => $employee->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil ditandai sebagai served',
                'data' => [
                    'order_id' => $order->id,
                    'order_code' => $order->booking_order_code,
                    'order_status' => $order->order_status, // SEKARANG 'SERVED'
                    'served_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ [SERVE FIXED] Mark as served error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai order sebagai served: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper function untuk transform order data
     */
    private function transformOrderData($order, $queueNumber = null)
    {
        $orderTime = $order->created_at ? Carbon::parse($order->created_at)->format('H:i') : '00:00';
        $updatedTime = $order->updated_at ? Carbon::parse($order->updated_at)->format('H:i') : null;
        
        $orderDetails = $order->order_details ?? collect();
        $totalItems = $orderDetails->sum('quantity');
        
        $productNames = $orderDetails->map(function($detail) {
            if (!$detail) return 'Unknown Product';
            
            $productName = $detail->product_name ?: 'Product';
            $optionsText = '';
            
            $options = $detail->order_detail_options ?? collect();
            if ($options->isNotEmpty()) {
                $optionNames = $options->pluck('partner_product_option_name')->filter()->toArray();
                if (!empty($optionNames)) {
                    $optionsText = ' (' . implode(', ', $optionNames) . ')';
                }
            }
            
            return "{$productName} x{$detail->quantity}{$optionsText}";
        })->implode(', ');

        $customerName = $this->cleanCustomerName($order->customer_name);
        $customerOrderNote = $order->customer_order_note ?? '';

        $orderDetailsNotes = $orderDetails->filter(function($detail) {
            return !empty($detail->customer_note);
        })->map(function($detail) {
            return [
                'product_name' => $detail->product_name ?? 'Product',
                'note' => $detail->customer_note
            ];
        })->values();

        $statusConfig = $this->getStatusConfig($order->order_status);

        return [
            'id' => $order->id,
            'queue_number' => $queueNumber,
            'booking_order_code' => $order->booking_order_code ?? 'N/A',
            'customer_name' => $customerName,
            'order_status' => $order->order_status,
            'status_badge' => $statusConfig['badge'],
            'status_color' => $statusConfig['color'],
            'order_time' => $orderTime,
            'updated_time' => $updatedTime,
            'total_items' => $totalItems,
            'product_names' => $productNames ?: 'No items',
            'total_order_value' => number_format($order->total_order_value ?? 0, 0, ',', '.'),
            'table_id' => $order->table_id ?? 'T',
            'customer_order_note' => $customerOrderNote,
            'order_details_notes' => $orderDetailsNotes,
            'has_special_notes' => !empty($customerOrderNote) || $orderDetailsNotes->isNotEmpty(),
            'created_at' => $order->created_at ? $order->created_at->toISOString() : now()->toISOString(),
            'updated_at' => $order->updated_at ? $order->updated_at->toISOString() : now()->toISOString(),
            'order_details' => $orderDetails->map(function($detail) {
                $options = $detail->order_detail_options ?? collect();
                
                return [
                    'id' => $detail->id,
                    'product_name' => $detail->product_name ?? 'Product',
                    'quantity' => $detail->quantity ?? 1,
                    'base_price' => $detail->base_price ?? 0,
                    'options_price' => $detail->options_price ?? 0,
                    'customer_note' => $detail->customer_note ?? '',
                    'status' => $detail->status ?? 'pending',
                    'done_flag' => $detail->done_flag ?? false,
                    'options' => $options->map(function($option) {
                        return [
                            'name' => $option->partner_product_option_name ?? 'Option',
                            'price' => $option->price ?? 0
                        ];
                    })
                ];
            })
        ];
    }

    /**
     * Helper untuk membersihkan nama customer dari kata "Guest"
     */
    private function cleanCustomerName($name)
    {
        if (empty($name)) {
            return 'Customer';
        }

        $cleaned = preg_replace('/\bguest\b/i', '', $name);
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        $cleaned = preg_replace('/[^\w\s]/', '', $cleaned);
        $cleaned = trim($cleaned);
        
        if (empty($cleaned)) {
            return 'Customer';
        }
        
        return $cleaned;
    }

    /**
     * Status configuration berdasarkan order_status saja
     */
    private function getStatusConfig($orderStatus)
    {
        $configs = [
            'PAID' => [
                'badge' => 'Waiting',
                'color' => 'orange'
            ],
            'PROCESSED' => [
                'badge' => 'Cooking', 
                'color' => 'blue'
            ],
            'SERVED' => [
                'badge' => 'Served',
                'color' => 'green'
            ],
            'UNPAID' => [
                'badge' => 'Unpaid',
                'color' => 'gray'
            ]
        ];

        return $configs[$orderStatus] ?? [
            'badge' => $orderStatus ?? 'Unknown',
            'color' => 'gray'
        ];
    }
}