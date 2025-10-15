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
     * GET ORDER QUEUE (PAID)
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

            // Get only PAID orders untuk queue
            $orders = BookingOrder::with([
                'order_details' => function($query) {
                    $query->select([
                        'id', 
                        'booking_order_id', 
                        'partner_product_id',
                        'product_name',
                        'quantity', 
                        'base_price', 
                        'options_price',
                        'customer_note'
                    ]);
                },
                'order_details.order_detail_options' => function($query) {
                    $query->select([
                        'id',
                        'order_detail_id', 
                        'partner_product_option_name',
                        'price'
                    ]);
                }
            ])
            ->where('partner_id', $employee->partner_id)
            ->where('order_status', 'PAID') 
            ->orderBy('created_at', 'asc') 
            ->get();

            // Transform data untuk queue
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
            Log::error('Order Queue Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order queue: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET ACTIVE ORDERS - Order dengan status PROCESSED (Cooking)
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

            // Get PROCESSED orders untuk active orders (Cooking)
            $orders = BookingOrder::with([
                'order_details' => function($query) {
                    $query->select([
                        'id', 
                        'booking_order_id', 
                        'partner_product_id',
                        'product_name',
                        'quantity', 
                        'base_price', 
                        'options_price',
                        'customer_note',
                        'status',
                        'done_flag'
                    ]);
                },
                'order_details.order_detail_options' => function($query) {
                    $query->select([
                        'id',
                        'order_detail_id', 
                        'partner_product_option_name',
                        'price'
                    ]);
                }
            ])
            ->where('partner_id', $employee->partner_id)
            ->where('order_status', 'PROCESSED') 
            ->orderBy('updated_at', 'desc') 
            ->get();

            // Transform data untuk active orders
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
            Log::error('Active Orders Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch active orders: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET SERVED ORDERS - Order dengan status SERVED (sudah disajikan)
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
                            'id', 
                            'booking_order_id', 
                            'partner_product_id',
                            'product_name',
                            'quantity', 
                            'base_price', 
                            'options_price',
                            'customer_note'
                        ]);
                    },
                    'order_details.order_detail_options' => function($query) {
                        $query->select([
                            'id',
                            'order_detail_id', 
                            'partner_product_option_name',
                            'price'
                        ]);
                    }
                ])
                ->where('partner_id', $employee->partner_id)
                ->where('order_status', 'SERVED');

            // Filter by date jika ada
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
            Log::error('Served Orders Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch served orders: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * PICK UP ORDER - Ubah status dari PAID ke PROCESSED (Cooking)
     */
    public function pickUpOrder(Request $request, $orderId)
    {
        try {
            $employee = Auth::guard('employee')->user();

            $order = BookingOrder::where('id', $orderId)
                ->where('partner_id', $employee->partner_id)
                ->where('order_status', 'PAID')
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found or already processed'
                ], 404);
            }

            // Update status ke PROCESSED (Cooking)
            $order->update([
                'order_status' => 'PROCESSED',
                'updated_at' => now()
            ]);

            Log::info('Order picked up to active orders', [
                'order_id' => $order->id,
                'order_code' => $order->booking_order_code,
                'updated_by' => $employee->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order moved to active orders (Cooking)',
                'data' => [
                    'order_id' => $order->id,
                    'order_code' => $order->booking_order_code,
                    'new_status' => 'PROCESSED'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Pick Up Order Error: ' . $e->getMessage());
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
        try {
            $employee = Auth::guard('employee')->user();

            $order = BookingOrder::where('id', $orderId)
                ->where('partner_id', $employee->partner_id)
                ->where('order_status', 'PROCESSED')
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found or already served'
                ], 404);
            }

            // Update status ke SERVED
            $order->update([
                'order_status' => 'SERVED',
                'updated_at' => now()
            ]);

            Log::info('Order marked as served', [
                'order_id' => $order->id,
                'order_code' => $order->booking_order_code,
                'updated_by' => $employee->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order marked as served',
                'data' => [
                    'order_id' => $order->id,
                    'order_code' => $order->booking_order_code,
                    'new_status' => 'SERVED'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Mark as Served Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark order as served: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper function untuk transform order data
     */
    private function transformOrderData($order, $queueNumber = null)
    {
        // Pastikan created_at adalah Carbon instance
        $orderTime = $order->created_at ? Carbon::parse($order->created_at)->format('H:i') : '00:00';
        $servedTime = ($order->order_status === 'SERVED' && $order->updated_at) 
            ? Carbon::parse($order->updated_at)->format('H:i') 
            : null;
        
        $orderDetails = $order->order_details ?? collect();
        $totalItems = $orderDetails->sum('quantity');
        
        // Format product names untuk display
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

        // HAPUS KATA "GUEST" DARI NAMA CUSTOMER
        $customerName = $this->cleanCustomerName($order->customer_name);

        // Ambil catatan khusus dari order (jika ada)
        $customerOrderNote = $order->customer_order_note ?? '';

        // Ambil catatan khusus dari order details (jika ada)
        $orderDetailsNotes = $orderDetails->filter(function($detail) {
            return !empty($detail->customer_note);
        })->map(function($detail) {
            return [
                'product_name' => $detail->product_name ?? 'Product',
                'note' => $detail->customer_note
            ];
        })->values();

        // Determine status config
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
            'served_time' => $servedTime,
            'total_items' => $totalItems,
            'product_names' => $productNames ?: 'No items',
            'total_order_value' => number_format($order->total_order_value ?? 0, 0, ',', '.'),
            'table_id' => $order->table_id ?? 'T',
            'order_type' => $order->order_type ?? 'Dine-in',
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

        // Hapus semua kemunculan kata "Guest" (case insensitive)
        $cleaned = preg_replace('/\bguest\b/i', '', $name);
        
        // Hapus karakter khusus dan spasi berlebih
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        $cleaned = preg_replace('/[^\w\s]/', '', $cleaned);
        $cleaned = trim($cleaned);
        
        if (empty($cleaned)) {
            return 'Customer';
        }
        
        return $cleaned;
    }

    /**
     * Status configuration
     */
    private function getStatusConfig($status)
    {
        $configs = [
            'PAID' => [
                'badge' => 'Waiting',
                'color' => 'red'
            ],
            'PROCESSED' => [
                'badge' => 'Cooking', 
                'color' => 'yellow'
            ],
            'SERVED' => [
                'badge' => 'Served',
                'color' => 'green'
            ]
        ];

        return $configs[$status] ?? [
            'badge' => $status,
            'color' => 'gray'
        ];
    }
}