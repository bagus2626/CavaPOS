<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Partner\Product\PartnerProductController;
use App\Http\Controllers\Partner\Store\PartnerTableController;
use App\Http\Controllers\Admin\PortfolioController;
use App\Http\Controllers\Admin\Product\CategoryController;
use App\Http\Controllers\Partner\Product\PartnerCategoryController;
use App\Http\Controllers\Partner\HumanResource\PartnerEmployeeController;
use App\Http\Controllers\Customer\Menu\CustomerMenuController;
use App\Http\Controllers\Customer\Auth\CustomerAuthController;
use App\Http\Controllers\Customer\Transaction\CustomerPaymentController;
use App\Http\Controllers\Employee\Dashboard\CashierDashboardController;
use App\Http\Controllers\Employee\Dashboard\KitchenDashboardController;
use App\Http\Controllers\Employee\Auth\EmployeeAuthController;
use App\Http\Controllers\Public\PriceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Partner\PartnerDashboardController;
use App\Http\Controllers\Employee\Transaction\CashierTransactionController;
use Illuminate\Http\Request;
use Pusher\Pusher;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Employee\Transaction\KitchenTransactionController;
// use App\Http\Middleware\IsAdmin;
// use App\Http\Middleware\RedirectIfAuthenticatedWithRole;

// Route::get('/debug/fire-order', function () {
//     $order = \App\Models\Transaction\BookingOrder::where('partner_id', 6)->latest()->first();
//     broadcast(new \App\Events\OrderCreated($order));
//     return 'OK';
// })->middleware(['web', 'auth:employee']);

// Route::post('/broadcasting/auth-employee', function (Request $request) {
//     try {
//         $user = auth('employee')->user();
//         abort_unless($user, 401, 'Unauthorized');

//         $socketId = $request->input('socket_id');
//         $channel  = $request->input('channel_name');

//         if (!preg_match('/^private-partner\.(\d+)\.orders$/', (string) $channel, $m)) {
//             abort(403, 'Invalid channel');
//         }
//         $partnerId = (int) $m[1];
//         abort_unless((int) $user->partner_id === $partnerId, 403, 'Forbidden');
//         // abort_unless(($user->role ?? null) === 'CASHIER', 403);

//         $options = [
//             'cluster' => env('PUSHER_APP_CLUSTER', 'ap1'),
//             'useTLS'  => (env('PUSHER_SCHEME', 'https') === 'https'),
//         ];
//         if (env('PUSHER_HOST')) {
//             $options['host']   = env('PUSHER_HOST');
//             $options['port']   = (int) (env('PUSHER_PORT') ?: ($options['useTLS'] ? 443 : 80));
//             $options['scheme'] = env('PUSHER_SCHEME', 'https');
//         }

//         foreach (['PUSHER_APP_ID', 'PUSHER_APP_KEY', 'PUSHER_APP_SECRET'] as $k) {
//             if (!env($k)) abort(500, "Env $k empty");
//         }

//         $pusher = new Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'), $options);
//         $auth = $pusher->authorizeChannel($channel, $socketId);

//         // return response()->json($auth);
//         return response($auth, 200)->header('Content-Type', 'application/json');
//     } catch (\Throwable $e) {
//         Log::error('AUTH-EMPLOYEE FAILED', [
//             'msg'  => $e->getMessage(),
//             'file' => $e->getFile(),
//             'line' => $e->getLine(),
//         ]);
//         return response()->json(['error' => 'auth-failed'], 500);
//     }
// })->middleware(['web', 'auth:employee']);




Route::get('/', function () {
    return view('pages.client.home.index');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/feature', function () {
    return view('pages.client.feature.index');
})->name('feature');
Route::get('/portfolio', function () {
    return view('pages.client.portfolio.index');
})->name('portfolio');
Route::get('/contact', function () {
    return view('pages.client.contact.index');
})->name('contact');

Route::get('/price', [PriceController::class, 'index'])->name('price');
Route::get('/price/data', [PriceController::class, 'data']);
Route::get('/price/{product:slug}', [PriceController::class, 'show'])->name('price.show');


//admin
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', ProductController::class);
    // Route::resource('specifications', SpecificationController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('portfolios', PortfolioController::class);
});

//Partner
Route::middleware(['auth', 'is_partner'])->prefix('partner')->name('partner.')->group(function () {
    Route::get('/', [PartnerDashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', PartnerProductController::class);
    Route::prefix('store')->name('store.')->group(function () {
        Route::get('tables/generate-barcode/{tableId}', [PartnerTableController::class, 'generateBarcode'])->name('tables.generate-barcode');
        Route::resource('tables', PartnerTableController::class);
    });
    Route::prefix('user-management')->name('user-management.')->group(function () {
        Route::resource('employees', PartnerEmployeeController::class);
    });
    Route::resource('categories', PartnerCategoryController::class);
    // Route::resource('specifications', SpecificationController::class);
    Route::resource('portfolios', PortfolioController::class);
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//employees
Route::prefix('employee')->name('employee.')->group(function () {
    // Auth
    Route::get('login', [EmployeeAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [EmployeeAuthController::class, 'login'])->name('login.submit');
    Route::post('logout', [EmployeeAuthController::class, 'logout'])->name('logout');

    // CASHIER area
    Route::middleware(['auth:employee', 'is_employee:CASHIER'])->prefix('cashier')->name('cashier.')->group(function () {
        Route::get('dashboard', [CashierDashboardController::class, 'index'])->name('dashboard');
        Route::get('tab/{tab}', [CashierDashboardController::class, 'show'])->name('tab');
        Route::post('cash-payment/{id}', [CashierTransactionController::class, 'cashPayment'])->name('cash-payment');
        Route::get('order-detail/{id}', [CashierTransactionController::class, 'orderDetail'])->name('order-detail');
        Route::get('print-receipt/{id}', [CashierTransactionController::class, 'printReceipt'])->name('print-receipt');
        Route::post('process-order/{id}', [CashierTransactionController::class, 'processOrder'])->name('process-order');
        Route::post('finish-order/{id}', [CashierTransactionController::class, 'finishOrder'])->name('finish-order');
        Route::post('checkout-order', [CashierTransactionController::class, 'checkout'])->name('checkout');
    });

    // KITCHEN area
    Route::middleware(['auth:employee', 'is_employee:KITCHEN'])->prefix('kitchen')->name('kitchen.')->group(function () {
        Route::get('dashboard', [KitchenDashboardController::class, 'index'])->name('dashboard');
        Route::get('tab/{tab}', [KitchenDashboardController::class, 'show'])->name('tab');
    });
});

//customer
Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('{partner_slug}/menu/{table_code}', [CustomerMenuController::class, 'index'])->name('menu.index');
    Route::post('{partner_slug}/checkout/{table_code}', [CustomerMenuController::class, 'checkout'])->name('menu.checkout');
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('{partner_slug}/get-payment-cash/{table_code}', [CustomerPaymentController::class, 'getPaymentCash'])
            ->name('get-payment-cash')
            ->middleware('signed');
    });

    // Google login (Socialite)
    Route::get('{partner_slug}/menu/{table_code}/login/{provider}', [CustomerAuthController::class, 'redirectToProvider'])->name('social.login');
    Route::get('/menu/google/callback', [CustomerAuthController::class, 'handleProviderCallback'])->name('social.callback');

    Route::post('{partner_slug}/menu/{table_code}/guest', [CustomerAuthController::class, 'guestLogin'])->name('guest');
    Route::post('/guest-logout/{partner_slug}/{table_code}', [CustomerAuthController::class, 'guestLogout'])->name('guest-logout');


    // customer auth routes
    Route::get('register/{partner_slug}/{table_code}', [CustomerAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register/{partner_slug}/{table_code}', [CustomerAuthController::class, 'register'])->name('register.submit');

    Route::get('login/{partner_slug}/{table_code}', [CustomerAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login/{partner_slug}/{table_code}', [CustomerAuthController::class, 'login'])->name('login.submit');

    Route::post('logout/{partner_slug}/{table_code}', [CustomerAuthController::class, 'logout'])->name('logout');

    Route::middleware('auth:customer')->group(function () {
        Route::get('/dashboard', function () {
            return view('customer.dashboard');
        })->name('dashboard');
    });
});


require __DIR__ . '/auth.php';
