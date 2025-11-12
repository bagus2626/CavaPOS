<?php

//use App\Http\Controllers\Owner\Product\OwnerProductController;
//use App\Http\Controllers\Owner\Product\OwnerPromotionController;
//use App\Http\Controllers\Owner\Report\SalesReportController;
//use App\Http\Controllers\PaymentGateway\Xendit\WebhookController;
use Pusher\Pusher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\PriceController;
use App\Http\Controllers\Admin\Dashboard\DashboardController;
use App\Http\Controllers\Admin\OwnerVerification\OwnerVerificationController;
use App\Http\Controllers\Admin\XenPlatform\PartnerAccountController;
use App\Http\Controllers\Admin\XenPlatform\SplitPaymentController;
use App\Http\Controllers\Admin\SendPayment\PayoutController;
use App\Http\Controllers\Owner\Auth\OwnerAuthController;
use App\Http\Controllers\Owner\Auth\OwnerPasswordResetController;
use App\Http\Controllers\Owner\OwnerDashboardController;
use App\Http\Controllers\Owner\Outlet\OwnerOutletController;
use App\Http\Controllers\Owner\Report\SalesReportController;
use App\Http\Controllers\Partner\PartnerDashboardController;
use App\Http\Controllers\Auth\GoogleCallbackController;
use App\Http\Controllers\Owner\Product\OwnerPromotionController;
use App\Http\Controllers\Owner\Product\OwnerStockController;
use App\Http\Controllers\Customer\Auth\CustomerAuthController;
use App\Http\Controllers\Customer\Menu\CustomerMenuController;
use App\Http\Controllers\Customer\Transaction\CustomerPaymentController;
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
use App\Http\Controllers\Partner\HumanResource\PartnerEmployeeController;
use App\Http\Controllers\Employee\Transaction\CashierTransactionController;
use App\Http\Controllers\Customer\Auth\CustomerPasswordResetController;
use App\Http\Controllers\Employee\Transaction\KitchenTransactionController;
use App\Http\Controllers\Owner\Verification\VerificationController;
use App\Http\Controllers\PaymentGateway\Xendit\SubAccountController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/set-language', function () {
    $locale = request('locale');
    abort_unless(in_array($locale, ['en', 'id'], true), 400);
    session(['app_locale' => $locale]);
    return back();
})->name('language.set.get');
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

    Route::get('/oauth/google/callback', [GoogleCallbackController::class, 'handle'])->name('google.callback');

    Route::middleware('guest')->group(function () {});


    //admin
    Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/owner-verification', [OwnerVerificationController::class, 'index'])->name('owner-verification');

        Route::get('/owner-verification/{id}', [OwnerVerificationController::class, 'show'])->name('owner-verification.show');

        Route::post('/owner-verification/{id}/approve', [OwnerVerificationController::class, 'approve'])->name('owner-verification.approve');
        Route::post('/owner-verification/{id}/reject', [OwnerVerificationController::class, 'reject'])->name('owner-verification.reject');

        Route::get('/owner-verification/{id}/ktp-image', [OwnerVerificationController::class, 'showKtpImage'])->name('owner-verification.ktp-image');
        Route::post('/owner-verification/register-xendit-account', [OwnerVerificationController::class, 'registerXenditAccount'])->name('owner-verification.register-xendit-account');;

        Route::prefix('send-payment')->name('send-payment.')->group(function () {
            Route::prefix('payout')->name('payout.')->group(function () {
                Route::get('/', [PayoutController::class, 'index'])->name('index');
                Route::post('get-data', [PayoutController::class, 'getData'])->name('get-data');
                Route::get('validate-bank', [PayoutController::class, 'validateBankAccount'])->name('validate-bank');
                Route::post('create', [PayoutController::class, 'createPayout'])->name('create');
                Route::get('{businessId}/detail/{payoutId}', [PayoutController::class, 'getPayout'])->name('detail');

            });
        });

        Route::prefix('xen_platform')->name('xen_platform.')->group(function () {
            Route::prefix('partner-account')->name('partner-account.')->group(function () {
                Route::get('{accountId}/information', [PartnerAccountController::class, 'showAccountInfo'])->name('information');
                Route::get('{accountId}/tab/{tab}', [PartnerAccountController::class, 'getTabData']);
                Route::get('{accountId}/filter/{tab}', [PartnerAccountController::class, 'filter']);;
                Route::get('{accountId}/transaction-detail/{transactionId}', [PartnerAccountController::class, 'getTransactionById']);
                Route::get('{accountId}/invoice-detail/{invoiceId}', [PartnerAccountController::class, 'getInvoiceById']);
            });

            Route::resource('partner-account', PartnerAccountController::class);
            Route::prefix('split-payments')->name('split-payments.')->group(function () {
                Route::get('split-payments', [SplitPaymentController::class, 'getSplitPayments']);
                Route::get('split-rules', [SplitPaymentController::class, 'getSplitRules']);
                Route::post('split-rules/create', [SplitPaymentController::class, 'createSplitRule'])->name('split-rules.create');
            });
            Route::resource('split-payments', SplitPaymentController::class);
        });

        Route::prefix('xendit')->name('xendit.')->group(function () {
            Route::prefix('sub-account')->name('sub-account.')->group(function () {
                Route::get('list', [SubAccountController::class, 'getSubAccounts'])->name('list');
                Route::get('profile/{id}', [SubAccountController::class, 'getSubAccountById'])->name('profile');
            });

        });
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

        Route::get('/auth/google/redirect', [OwnerAuthController::class, 'redirect'])->name('google.redirect');

        Route::middleware('guest:owner')->group(function () {
            // minta link reset
            Route::get('forgot-password', [OwnerPasswordResetController::class, 'requestForm'])->name('password.request');
            Route::post('forgot-password', [OwnerPasswordResetController::class, 'sendLink'])->name('password.email');

            // form reset & submit
            Route::get('reset-password/{token}', [OwnerPasswordResetController::class, 'resetForm'])->name('password.reset');
            Route::post('reset-password', [OwnerPasswordResetController::class, 'update'])->name('password.update');
        });

        // ===== Email Verification (OWNER) =====
        // Halaman "cek email" dan kirim ulang link verifikasi
        Route::middleware('auth:owner')->group(function () {
            // Notice
            Route::get('/email/verify', function () {
                return view('pages.owner.auth.verify-email'); // buat view ini
            })->name('verification.notice');

            // Resend link (rate limited)
            Route::post('/email/verification-notification', function (Request $request) {
                $request->user('owner')->sendEmailVerificationNotification();
                return back()->with('status', 'verification-link-sent');
            })->middleware('throttle:6,1')->name('verification.send');

            // Verify link (signed)
            Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
                $request->fulfill(); // set email_verified_at
                return redirect()->route('owner.user-owner.dashboard')
                    ->with('success', 'Email Anda berhasil diverifikasi.');
            })->middleware('signed')->name('verification.verify');
        });

        // OWNER area
        Route::middleware(['auth:owner', 'is_owner:owner', 'verified'])->prefix('user-owner')->name('user-owner.')->group(function () {


            Route::middleware('owner.verification.access')->prefix('verification')->name('verification.')->group(function () {
                Route::get('/', [VerificationController::class, 'index'])->name('index');
                Route::post('/', [VerificationController::class, 'store'])->name('store');
                Route::get('/status', [VerificationController::class, 'status'])->name('status');
                Route::get('verification/ktp-image', [VerificationController::class, 'showKtpImage'])->name('ktp-image');
            });


            Route::middleware('owner.verification.access')->group(function () {
                Route::get('/', [OwnerDashboardController::class, 'index'])->name('dashboard');
                Route::get('outlets/check-username', [OwnerOutletController::class, 'checkUsername'])->name('outlets.check-username')->middleware('throttle:30,1');
                Route::get('outlets/check-slug', [OwnerOutletController::class, 'checkSlug'])->name('outlets.check-slug')->middleware('throttle:30,1');
                Route::resource('outlets', OwnerOutletController::class);
                Route::get('employees/check-username', [OwnerEmployeeController::class, 'checkUsername'])->name('employees.check-username');
                Route::resource('employees', OwnerEmployeeController::class);
                Route::resource('master-products', OwnerMasterProductController::class);
                Route::get('outlet-products/get-master-products', [OwnerOutletProductController::class, 'getMasterProducts'])->name('outlet-products.get-master-products');
                Route::resource('outlet-products', OwnerOutletProductController::class);
                Route::resource('products', OwnerProductController::class);
                Route::resource('categories', OwnerCategoryController::class);

                Route::prefix('report')->name('report.')->group(function () {
                    Route::get('sales/export', [SalesReportController::class, 'export'])->name('sales.export');
                    Route::get('sales/products', [SalesReportController::class, 'getTopProductsAjax'])->name('sales.products'); // ROUTE BARU
                    Route::get('order-details/{id}', [SalesReportController::class, 'getOrderDetails'])->name('order-details');
                    Route::resource('sales', SalesReportController::class)->only(['index']);
                });
                Route::resource('promotions', OwnerPromotionController::class);
                Route::resource('stocks', OwnerStockController::class);
            });


            // Route::middleware('owner.not_approved')->prefix('verification')->name('verification.')->group(function () {
            //     Route::get('/', [VerificationController::class, 'index'])->name('index');
            //     Route::post('/', [VerificationController::class, 'store'])->name('store');
            //     Route::get('/status', [VerificationController::class, 'status'])->name('status');

            //     Route::get('verification/ktp-image', [VerificationController::class, 'showKtpImage'])->name('ktp-image');
            // });
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
        // Route::resource('categories', PartnerCategoryController::class);
        // Route::resource('specifications', SpecificationController::class);
        //        Route::resource('portfolios', PortfolioController::class);
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

            // API Endpoints - UPDATE YANG SUDAH ADA
            Route::get('orders/queue', [KitchenDashboardController::class, 'getOrderQueue'])->name('orders.queue');
            Route::get('orders/active', [KitchenDashboardController::class, 'getActiveOrders'])->name('orders.active');
            Route::get('orders/served', [KitchenDashboardController::class, 'getServedOrders'])->name('orders.served');
            Route::put('orders/{orderId}/pickup', [KitchenDashboardController::class, 'pickUpOrder'])->name('orders.pickup');
            Route::put('orders/{orderId}/serve', [KitchenDashboardController::class, 'markAsServed'])->name('orders.serve');

            // TAMBAH ROUTE BARU UNTUK KITCHEN_STATUS
            Route::get('orders/my-active', [KitchenDashboardController::class, 'getMyActiveOrders'])->name('orders.my-active');
            Route::get('summary', [KitchenDashboardController::class, 'getKitchenSummary'])->name('summary');
            Route::put('orders/{orderId}/release', [KitchenDashboardController::class, 'releaseOrder'])->name('orders.release');
            Route::get('kitchen/orders/{orderId}/test', [KitchenDashboardController::class, 'testOrderStatus'])->name('orders.test');
            Route::get('kitchen/orders/{orderId}/debug', [KitchenDashboardController::class, 'debugOrderStatus'])->name('orders.debug');
        });
    });

    //customer
    Route::prefix('customer')->name('customer.')->group(function () {
        Route::get('{partner_slug}/menu/{table_code}', [CustomerMenuController::class, 'index'])->name('menu.index');
        Route::post('{partner_slug}/checkout/{table_code}', [CustomerMenuController::class, 'checkout'])->name('menu.checkout');
        Route::get('/orders/{id}/receipt', [CustomerMenuController::class, 'printReceipt'])->name('orders.receipt');

        Route::prefix('payment')->name('payment.')->group(function () {
            Route::get('{partner_slug}/get-payment-cash/{table_code}', [CustomerPaymentController::class, 'getPaymentCash'])
                ->name('get-payment-cash')
                ->middleware('signed');
        });

        // Google login (Socialite)
        Route::get('/auth/google/redirect/{partner_slug}/{table_code}', [CustomerAuthController::class, 'redirect'])->name('google.redirect');

        Route::post('{partner_slug}/menu/{table_code}/guest', [CustomerAuthController::class, 'guestLogin'])->name('guest');
        Route::post('/guest-logout/{partner_slug}/{table_code}', [CustomerAuthController::class, 'guestLogout'])->name('guest-logout');


        // customer auth routes
        Route::get('register/{partner_slug}/{table_code}', [CustomerAuthController::class, 'showRegisterForm'])->name('register');
        Route::post('register/{partner_slug}/{table_code}', [CustomerAuthController::class, 'register'])->name('register.submit');

        Route::get('login/{partner_slug}/{table_code}', [CustomerAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login/{partner_slug}/{table_code}', [CustomerAuthController::class, 'login'])->name('login.submit');

        Route::post('logout/{partner_slug}/{table_code}', [CustomerAuthController::class, 'logout'])->name('logout');
        Route::post('logout', [CustomerAuthController::class, 'logoutSimple'])->name('logout.simple');

        Route::middleware('guest:customer')->group(function () {
            // Form minta link reset (bawa partner_slug & table_code agar bisa direstor)
            Route::get('forgot-password/{partner_slug}/{table_code}', [CustomerPasswordResetController::class, 'requestForm'])
                ->name('password.request');

            // Kirim email reset
            Route::post('forgot-password/{partner_slug}/{table_code}', [CustomerPasswordResetController::class, 'sendLink'])
                ->name('password.email');

            // Form reset dari email (token + email di query; partner_slug & table_code optional via query)
            Route::get('reset-password/{token}', [CustomerPasswordResetController::class, 'resetForm'])
                ->name('password.reset');

            // Submit reset
            Route::post('reset-password', [CustomerPasswordResetController::class, 'update'])
                ->name('password.update');
        });

        Route::middleware('auth:customer')->group(function () {
            // Halaman notice
            Route::get('/email/verify', function () {
                return view('pages.customer.auth.verify-email'); // buat view ini
            })->name('verification.notice');

            // Kirim ulang link (rate limited)
            Route::post('/email/verification-notification', function (Request $request) {
                $request->user('customer')->sendEmailVerificationNotification();
                return back()->with('status', 'verification-link-sent');
            })->middleware('throttle:6,1')->name('verification.send');

            // Link verifikasi (signed)
            Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
                $request->fulfill(); // set email_verified_at

                // Ambil tujuan yg kita simpan ketika register/login
                $dest = session('customer.intended') ?? route('home');
                session()->forget('customer.intended');

                return redirect($dest)->with('success', 'Email Anda berhasil diverifikasi.');
            })->middleware('signed')->name('verification.verify');
        });

        Route::middleware('auth:customer', 'verified')->group(function () {
            Route::get('/dashboard', function () {
                return view('customer.dashboard');
            })->name('dashboard');
        });
    });
});
require __DIR__ . '/auth.php';
