<?php

use Pusher\Pusher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\PriceController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PortfolioController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Owner\Auth\OwnerAuthController;
use App\Http\Controllers\Owner\OwnerDashboardController;
use App\Http\Controllers\Admin\Product\CategoryController;
use App\Http\Controllers\Owner\Outlet\OwnerOutletController;
use App\Http\Controllers\Owner\Report\SalesReportController;
use App\Http\Controllers\Partner\PartnerDashboardController;
use App\Http\Controllers\Customer\Auth\CustomerAuthController;
use App\Http\Controllers\Customer\Menu\CustomerMenuController;
use App\Http\Controllers\Employee\Auth\EmployeeAuthController;
use App\Http\Controllers\Owner\Product\OwnerProductController;
use App\Http\Controllers\Partner\Store\PartnerTableController;
use App\Http\Controllers\Owner\Product\OwnerCategoryController;
use App\Http\Controllers\Partner\Product\PartnerProductController;
use App\Http\Controllers\Partner\Product\PartnerCategoryController;
use App\Http\Controllers\Owner\Product\OwnerMasterProductController;
use App\Http\Controllers\Owner\Product\OwnerOutletProductController;
use App\Http\Controllers\Owner\HumanResource\OwnerEmployeeController;
use App\Http\Controllers\Employee\Dashboard\CashierDashboardController;
use App\Http\Controllers\Employee\Dashboard\KitchenDashboardController;
use App\Http\Controllers\Customer\Transaction\CustomerPaymentController;
use App\Http\Controllers\Partner\HumanResource\PartnerEmployeeController;
use App\Http\Controllers\Employee\Transaction\CashierTransactionController;
use App\Http\Controllers\Employee\Transaction\KitchenTransactionController;

Route::post('/set-language', function () {
    $locale = request('locale');
    abort_unless(in_array($locale, ['en', 'id'], true), 400);
    session(['app_locale' => $locale]);
    return back();
})->name('language.set');

Route::middleware('setlocale')->group(function () {

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

    Route::middleware('guest')->group(function () {});


    //admin
    Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('products', ProductController::class);
        // Route::resource('specifications', SpecificationController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('portfolios', PortfolioController::class);
    });

    // Owner
    Route::prefix('owner')->name('owner.')->group(function () {
        // register owner
        Route::get('register',  [OwnerAuthController::class, 'create'])->name('register');
        Route::post('register', [OwnerAuthController::class, 'store'])->name('register.store');
        // Login owner
        Route::get('login',     [OwnerAuthController::class, 'login'])->name('login');
        Route::post('login',    [OwnerAuthController::class, 'authenticate'])->name('login.attempt');
        Route::post('logout',    [OwnerAuthController::class, 'logout'])->name('logout');


        // OWNER area
        Route::middleware(['auth:owner', 'is_owner:owner'])->prefix('user-owner')->name('user-owner.')->group(function () {
            Route::get('/', [OwnerDashboardController::class, 'index'])->name('dashboard');
            Route::get('outlets/check-username', [OwnerOutletController::class, 'checkUsername'])->name('outlets.check-username')->middleware('throttle:30,1');
            Route::resource('outlets', OwnerOutletController::class);
            Route::get('employees/check-username', [OwnerEmployeeController::class, 'checkUsername'])->name('employees.check-username');
            Route::resource('employees', OwnerEmployeeController::class);
            Route::resource('master-products', OwnerMasterProductController::class);
            Route::get('outlet-products/get-master-products', [OwnerOutletProductController::class, 'getMasterProducts'])->name('outlet-products.get-master-products');
            Route::resource('outlet-products', OwnerOutletProductController::class);
            Route::resource('products', OwnerProductController::class);
            Route::resource('categories', OwnerCategoryController::class);

            Route::prefix('report')->name('report.')->group(function () {
                Route::resource('sales', SalesReportController::class);
            });
        });
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
});
require __DIR__ . '/auth.php';
