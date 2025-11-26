<?php

namespace App\Http\Controllers\Admin\OwnerManagement;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Partner\Products\PartnerProduct;
use App\Models\User;
use App\Models\Partner\HumanResource\Employee;
use App\Models\Transaction\BookingOrder;
use Illuminate\Http\Request;

class OwnerListController extends Controller
{
    /**
     * Display listing of owners with statistics and filters
     */
    public function index(Request $request)
    {
        $statistics = $this->getOwnerStatistics();
        $owners = $this->getFilteredOwners($request);

        return view('pages.admin.owner-management.owner-list', array_merge(
            $statistics,
            ['owners' => $owners]
        ));
    }

    /**
     * Show outlets for a specific owner
     */
    public function showOutlets(Request $request, $ownerId)
    {
        $owner = Owner::findOrFail($ownerId);
        $outlets = $this->getFilteredOutlets($request, $ownerId);
        $statistics = $this->getOutletStatistics($ownerId);

        if ($this->isAjaxRequest($request)) {
            return $this->buildOutletsAjaxResponse($outlets, $owner);
        }

        return view('pages.admin.owner-management.owner-outlets', array_merge(
            ['owner' => $owner, 'outlets' => $outlets],
            $statistics
        ));
    }

    /**
     * Show outlet data (products, employees, booking orders)
     */
    public function showOutletData(Request $request, $ownerId, $outletId)
    {
        $outlet = $this->getOutletWithOwner($ownerId, $outletId);
        $owner = $outlet->owner;

        $products = $this->getFilteredProducts($request, $outletId);
        $employees = $this->getFilteredEmployees($request, $outletId);
        $bookingOrders = $this->getFilteredBookingOrders($request, $outletId);

        $productsStats = $this->getProductsStatistics($outletId);
        $employeesStats = $this->getEmployeesStatistics($outletId);
        $ordersStats = $this->getOrdersStatistics($outletId);

        // Handle AJAX requests for specific tables
        if ($request->ajax()) {
            return $this->handleAjaxTableRequest(
                $request,
                $products,
                $employees,
                $bookingOrders
            );
        }

        return view('pages.admin.owner-management.outlet-data', compact(
            'owner',
            'outlet',
            'products',
            'employees',
            'bookingOrders'
        ) + $productsStats + $employeesStats + $ordersStats);
    }

    /**
     * Get owner statistics
     */
    private function getOwnerStatistics(): array
    {
        return [
            'totalOwners' => Owner::count(),
            'activeOwners' => Owner::where('is_active', 1)->count(),
            'inactiveOwners' => Owner::where('is_active', 0)->count(),
        ];
    }

    /**
     * Get filtered owners with pagination
     */
    private function getFilteredOwners(Request $request)
    {
        $query = Owner::withCount([
            'users' => fn($q) => $q->where('role', 'partner')
        ]);

        $this->applyStatusFilter($query, $request);
        $this->applyOwnerSearch($query, $request);

        return $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Apply status filter to owner query
     */
    private function applyStatusFilter($query, Request $request): void
    {
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active' ? 1 : 0);
        }
    }

    /**
     * Apply search filter to owner query
     */
    private function applyOwnerSearch($query, Request $request): void
    {
        if (!$request->filled('search')) {
            return;
        }

        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone_number', 'like', "%{$search}%")
                ->orWhereHas('users', function ($q2) use ($search) {
                    $q2->where('email', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%");
                });
        });
    }

    /**
     * Get filtered outlets with pagination
     */
    private function getFilteredOutlets(Request $request, $ownerId)
    {
        $query = User::where('role', 'partner')
            ->where('owner_id', $ownerId);

        $this->applyOutletSearch($query, $request);

        return $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Apply search filter to outlet query
     */
    private function applyOutletSearch($query, Request $request): void
    {
        if (!$request->filled('search')) {
            return;
        }

        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('username', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%")
                ->orWhere('subdistrict', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%");
        });
    }

    /**
     * Get outlet statistics
     */
    private function getOutletStatistics($ownerId): array
    {
        $totalOutlets = User::where('role', 'partner')
            ->where('owner_id', $ownerId)
            ->count();

        $activeOutlets = User::where('role', 'partner')
            ->where('owner_id', $ownerId)
            ->where('is_active', 1)
            ->where('is_active_admin', 1)
            ->count();

        return [
            'totalOutlets' => $totalOutlets,
            'activeOutlets' => $activeOutlets,
            'inactiveOutlets' => $totalOutlets - $activeOutlets,
        ];
    }

    /**
     * Get outlet with owner relationship
     */
    private function getOutletWithOwner($ownerId, $outletId)
    {
        return User::with(['owner'])
            ->where('id', $outletId)
            ->where('owner_id', $ownerId)
            ->where('role', 'partner')
            ->firstOrFail();
    }

    /**
     * Build AJAX response for outlets
     */
    private function buildOutletsAjaxResponse($outlets, $owner): \Illuminate\Http\JsonResponse
    {
        $tableHtml = $this->renderOutletsTable($outlets, $owner);
        $paginationHtml = $this->buildPaginationIfNeeded($outlets);

        return response()->json([
            'table' => $tableHtml,
            'pagination' => $paginationHtml
        ]);
    }

    /**
     * Render outlets table HTML
     */
    private function renderOutletsTable($outlets, $owner): string
    {
        if ($outlets->count() === 0) {
            return '<tr><td colspan="8" class="text-center text-muted">No outlets found for this owner</td></tr>';
        }

        $html = '';
        foreach ($outlets as $index => $outlet) {
            $html .= view('pages.admin.owner-management.partials.outlet-row', 
                compact('outlet', 'index', 'outlets', 'owner')
            )->render();
        }

        return $html;
    }

    /**
     * Get filtered products with pagination
     */
    private function getFilteredProducts(Request $request, $outletId)
    {
        $query = PartnerProduct::with(['category', 'parent_options.options'])
            ->where('partner_id', $outletId);

        $this->applyProductSearch($query, $request);

        return $query->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'products_page')
            ->withQueryString();
    }

    /**
     * Apply search filter to product query
     */
    private function applyProductSearch($query, Request $request): void
    {
        if (!$request->filled('search_products')) {
            return;
        }

        $search = $request->search_products;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('product_code', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhereHas('category', function ($q2) use ($search) {
                    $q2->where('category_name', 'like', "%{$search}%");
                });
        });
    }

    /**
     * Get products statistics
     */
    private function getProductsStatistics($outletId): array
    {
        return [
            'totalProducts' => PartnerProduct::where('partner_id', $outletId)->count(),
            'activeProducts' => PartnerProduct::where('partner_id', $outletId)
                ->where('is_active', 1)
                ->count(),
            'outOfStockProducts' => PartnerProduct::where('partner_id', $outletId)
                ->where('quantity', 0)
                ->where('always_available_flag', 0)
                ->count(),
            'totalCategories' => PartnerProduct::where('partner_id', $outletId)
                ->distinct('category_id')
                ->count('category_id'),
        ];
    }

    /**
     * Get filtered employees with pagination
     */
    private function getFilteredEmployees(Request $request, $outletId)
    {
        $query = Employee::where('partner_id', $outletId);

        $this->applyEmployeeSearch($query, $request);

        return $query->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'employees_page')
            ->withQueryString();
    }

    /**
     * Apply search filter to employee query
     */
    private function applyEmployeeSearch($query, Request $request): void
    {
        if (!$request->filled('search_employees')) {
            return;
        }

        $search = $request->search_employees;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('user_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('role', 'like', "%{$search}%");
        });
    }

    /**
     * Get employees statistics
     */
    private function getEmployeesStatistics($outletId): array
    {
        $totalEmployees = Employee::where('partner_id', $outletId)->count();
        $activeEmployees = Employee::where('partner_id', $outletId)
            ->where('is_active', 1)
            ->where('is_active_admin', 1)
            ->count();

        return [
            'totalEmployees' => $totalEmployees,
            'activeEmployees' => $activeEmployees,
            'inactiveEmployees' => $totalEmployees - $activeEmployees,
        ];
    }

    /**
     * Get filtered booking orders with pagination
     */
    private function getFilteredBookingOrders(Request $request, $outletId)
    {
        $query = BookingOrder::with([
            'table',
            'order_details.partnerProduct',
            'order_details.order_detail_options.option',
            'payment'
        ])->where('partner_id', $outletId);

        $this->applyOrderSearch($query, $request);

        return $query->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'orders_page')
            ->withQueryString();
    }

    /**
     * Apply search filter to booking order query
     */
    private function applyOrderSearch($query, Request $request): void
    {
        if (!$request->filled('search_orders')) {
            return;
        }

        $search = $request->search_orders;
        $query->where(function ($q) use ($search) {
            $q->where('booking_order_code', 'like', "%{$search}%")
                ->orWhere('customer_name', 'like', "%{$search}%")
                ->orWhere('order_by', 'like', "%{$search}%")
                ->orWhere('order_status', 'like', "%{$search}%")
                ->orWhere('payment_method', 'like', "%{$search}%")
                ->orWhereHas('table', function ($q2) use ($search) {
                    $q2->where('table_no', 'like', "%{$search}%");
                });
        });
    }

    /**
     * Get booking orders statistics
     */
    private function getOrdersStatistics($outletId): array
    {
        return [
            'totalOrders' => BookingOrder::where('partner_id', $outletId)->count(),
            'completedOrders' => BookingOrder::where('partner_id', $outletId)
                ->where('order_status', 'SERVED')
                ->count(),
            'pendingOrders' => BookingOrder::where('partner_id', $outletId)
                ->where('order_status', 'UNPAID')
                ->count(),
        ];
    }

    /**
     * Check if request is AJAX
     */
    private function isAjaxRequest(Request $request): bool
    {
        return $request->ajax() || $request->has('ajax');
    }

    /**
     * Handle AJAX request for specific tables
     */
    private function handleAjaxTableRequest(Request $request, $products, $employees, $bookingOrders)
    {
        $table = $request->get('table');

        return match ($table) {
            'products' => $this->buildProductsAjaxResponse($products),
            'employees' => $this->buildEmployeesAjaxResponse($employees),
            'orders' => $this->buildOrdersAjaxResponse($bookingOrders),
            default => response()->json(['error' => 'Invalid table'], 400),
        };
    }

    /**
     * Build AJAX response for products
     */
    private function buildProductsAjaxResponse($products): \Illuminate\Http\JsonResponse
    {
        $tableHtml = $this->renderProductsTable($products);
        $modalsHtml = $this->renderProductsModals($products);
        $paginationHtml = $this->buildPaginationIfNeeded($products);

        return response()->json([
            'table' => $tableHtml,
            'modals' => $modalsHtml,
            'pagination' => $paginationHtml
        ]);
    }

    /**
     * Render products table HTML
     */
    private function renderProductsTable($products): string
    {
        if ($products->count() === 0) {
            return '<tr><td colspan="7" class="text-center text-muted">No products found for this outlet</td></tr>';
        }

        $html = '';
        foreach ($products as $index => $product) {
            $html .= view('pages.admin.owner-management.partials.product-row',
                compact('product', 'index', 'products')
            )->render();
        }

        return $html;
    }

    /**
     * Render products modals HTML
     */
    private function renderProductsModals($products): string
    {
        $html = '';
        foreach ($products as $product) {
            $html .= view('pages.admin.owner-management.partials.product-modal',
                compact('product')
            )->render();
        }

        return $html;
    }

    /**
     * Build AJAX response for employees
     */
    private function buildEmployeesAjaxResponse($employees): \Illuminate\Http\JsonResponse
    {
        $tableHtml = $this->renderEmployeesTable($employees);
        $paginationHtml = $this->buildPaginationIfNeeded($employees);

        return response()->json([
            'table' => $tableHtml,
            'pagination' => $paginationHtml
        ]);
    }

    /**
     * Render employees table HTML
     */
    private function renderEmployeesTable($employees): string
    {
        if ($employees->count() === 0) {
            return '<tr><td colspan="7" class="text-center text-muted">No employees found for this outlet</td></tr>';
        }

        $html = '';
        foreach ($employees as $index => $employee) {
            $html .= view(
                'pages.admin.owner-management.partials.employee-row',
                compact('employee', 'index', 'employees')
            )->render();
        }

        return $html;
    }

    /**
     * Build AJAX response for booking orders
     */
    private function buildOrdersAjaxResponse($bookingOrders): \Illuminate\Http\JsonResponse
    {
        $tableHtml = $this->renderOrdersTable($bookingOrders);
        $modalsHtml = $this->renderOrdersModals($bookingOrders);
        $paginationHtml = $this->buildPaginationIfNeeded($bookingOrders);

        return response()->json([
            'table' => $tableHtml,
            'modals' => $modalsHtml,
            'pagination' => $paginationHtml
        ]);
    }

    /**
     * Render booking orders table HTML
     */
    private function renderOrdersTable($bookingOrders): string
    {
        if ($bookingOrders->count() === 0) {
            return '<tr><td colspan="7" class="text-center text-muted">No booking orders found for this outlet</td></tr>';
        }

        $html = '';
        foreach ($bookingOrders as $index => $order) {
            $html .= view('pages.admin.owner-management.partials.booking-order-row',
                compact('order', 'index', 'bookingOrders')
            )->render();
        }

        return $html;
    }

    /**
     * Render booking orders modals HTML
     */
    private function renderOrdersModals($bookingOrders): string
    {
        $html = '';
        foreach ($bookingOrders as $order) {
            $html .= view('pages.admin.owner-management.partials.booking-order-modal',
                compact('order')
            )->render();
        }

        return $html;
    }

    /**
     * Build pagination HTML if paginator has pages
     */
    private function buildPaginationIfNeeded($paginator): string
    {
        return $paginator->hasPages() 
            ? $this->buildCustomPagination($paginator) 
            : '';
    }

    /**
     * Build custom pagination HTML matching splitTransactions style
     */
    private function buildCustomPagination($paginator): string
    {
        if ($paginator->total() <= 0) {
            return '';
        }

        $summary = $this->buildPaginationSummary($paginator);
        $links = $this->buildPaginationLinks($paginator);

        return <<<HTML
        <div class="d-flex justify-content-between align-items-center mt-1">
            {$summary}
            {$links}
        </div>
        HTML;
    }

    /**
     * Build pagination summary text
     */
    private function buildPaginationSummary($paginator): string
    {
        $first = $paginator->firstItem();
        $last = $paginator->lastItem();
        $total = $paginator->total();

        return <<<HTML
        <div class="pagination-summary text-muted">
            Showing {$first} - {$last} from {$total} entries
        </div>
        HTML;
    }

    /**
     * Build pagination links HTML
     */
    private function buildPaginationLinks($paginator): string
    {
        $paginationView = view('vendor.pagination.custom-limited', [
            'paginator' => $paginator
        ])->render();

        return <<<HTML
        <div class="pagination-links">
            {$paginationView}
        </div>
        HTML;
    }

    /**
     * Toggle owner active status
     */
    public function toggleStatus(Request $request, $ownerId)
    {
        try {
            $request->validate([
                'is_active' => 'required|boolean',
                'deactivation_reason' => 'nullable|string|max:500'
            ]);

            $owner = Owner::findOrFail($ownerId); 

            $owner->is_active = $request->is_active;

            if ($request->is_active) {
                // Activating - clear reason and timestamp
                $owner->deactivation_reason = null;
                $owner->deactivated_at = null;
            } else {
                // Deactivating - save reason and timestamp
                $owner->deactivation_reason = $request->deactivation_reason;
                $owner->deactivated_at = now();
            }

            $owner->save();

            $status = $request->is_active ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Owner account has been {$status} successfully"
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update owner status'
            ], 500);
        }
    }

    /**
     * Toggle outlet active status
     */
    public function toggleOutletStatus(Request $request, $ownerId, $outletId)
    {
        try {
            $request->validate([
                'is_active_admin' => 'required|boolean',
                'deactivation_reason' => 'nullable|string|max:500'
            ]);

            $outlet = User::where('id', $outletId)
                ->where('owner_id', $ownerId)
                ->where('role', 'partner')
                ->firstOrFail();

            $outlet->is_active_admin = $request->is_active_admin;

            if ($request->is_active_admin) {
                // Activating - clear reason and timestamp
                $outlet->deactivation_reason = null;
                $outlet->deactivated_at = null;
            } else {
                // Deactivating - save reason and timestamp
                $outlet->deactivation_reason = $request->deactivation_reason;
                $outlet->deactivated_at = now();
            }

            $outlet->save();

            $status = $request->is_active_admin ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Outlet account has been {$status} successfully"
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update outlet status'
            ], 500);
        }
    }

    /**
     * Toggle employees active status
     */
    public function toggleEmployeeStatus(Request $request, $ownerId, $outletId, $employeeId)
    {
        try {
            $request->validate([
                'is_active_admin' => 'required|boolean',
                'deactivation_reason' => 'nullable|string|max:500'
            ]);

            // Verify outlet belongs to owner
            $outlet = User::where('id', $outletId)
                ->where('owner_id', $ownerId)
                ->where('role', 'partner')
                ->firstOrFail();

            // Get employee
            $employee = Employee::where('id', $employeeId)
                ->where('partner_id', $outletId)
                ->firstOrFail();

            $employee->is_active_admin = $request->is_active_admin;

            if ($request->is_active_admin) {
                // Activating - clear reason and timestamp
                $employee->deactivation_reason = null;
                $employee->deactivated_at = null;
            } else {
                // Deactivating - save reason and timestamp
                $employee->deactivation_reason = $request->deactivation_reason;
                $employee->deactivated_at = now();
            }

            $employee->save();

            $status = $request->is_active_admin ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Employee account has been {$status} successfully"
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update employee status'
            ], 500);
        }
    }
}