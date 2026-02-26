<?php

use App\Http\Controllers\Owner\Product\OwnerStockMovementController;
use App\Jobs\SendAdminEmailVerification;
use App\Jobs\SendEmailVerification;
use Pusher\Pusher;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\ProfileController;
// use App\Http\Controllers\Public\PriceController;
use App\Http\Controllers\Admin\Dashboard\DashboardController;
use App\Http\Controllers\Admin\OwnerManagement\OwnerListController;
use App\Http\Controllers\Admin\OwnerVerification\OwnerVerificationController;
use App\Http\Controllers\Admin\XenPlatform\PartnerAccountController;
use App\Http\Controllers\Admin\XenPlatform\SplitPaymentController;
use App\Http\Controllers\Admin\XenPlatform\BalanceController;
use App\Http\Controllers\Admin\XenPlatform\DisbursementController;
use App\Http\Controllers\Admin\XenPlatform\TransactionsController;
use App\Http\Controllers\Admin\SendPayment\PayoutController;
use App\Http\Controllers\Owner\Auth\OwnerAuthController;
use App\Http\Controllers\Owner\Auth\OwnerPasswordResetController;
use App\Http\Controllers\Owner\OwnerDashboardController;
use App\Http\Controllers\Owner\Outlet\OwnerOutletController;
use App\Http\Controllers\Owner\SettingsProfile\OwnerSettingsController;
use App\Http\Controllers\Owner\Report\SalesReportController;
use App\Http\Controllers\Owner\XenPlatform\AccountsController;
use App\Http\Controllers\Owner\XenPlatform\OwnerPayoutController;
use App\Http\Controllers\Owner\XenPlatform\OwnerSplitPaymentController;
use App\Http\Controllers\Partner\PartnerDashboardController;
use App\Http\Controllers\Auth\GoogleCallbackController;
use App\Http\Controllers\Owner\Product\OwnerPromotionController;
use App\Http\Controllers\Owner\PaymentMethod\OwnerPaymentMethodController;
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
use App\Http\Controllers\Owner\Outlet\OwnerTablesController;
use App\Http\Controllers\Partner\HumanResource\PartnerEmployeeController;
use App\Http\Controllers\Employee\Transaction\CashierTransactionController;
use App\Http\Controllers\Customer\Auth\CustomerPasswordResetController;
use App\Http\Controllers\Employee\Transaction\KitchenTransactionController;
use App\Http\Controllers\Owner\Verification\VerificationController;
use App\Http\Controllers\PaymentGateway\Xendit\SubAccountController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use \App\Http\Controllers\Admin\MessageNotification\MessageController;
use App\Http\Controllers\Customer\Table\TableStatusController;
use App\Http\Controllers\Employee\Dashboard\StaffDashboardController;
use App\Http\Controllers\Employee\Product\StaffCategoryController;
use App\Http\Controllers\Employee\Product\StaffPromotionController;
use App\Http\Controllers\Employee\Staff\Product\StaffProductController;
use App\Http\Controllers\Employee\StaffMessageController;
use App\Http\Controllers\Employee\Employee\StaffEmployeeController;
use App\Http\Controllers\Employee\SettingsProfile\StaffSettingsController;
use App\Http\Controllers\Employee\StockReport\StaffStockReportController;
use App\Http\Controllers\Owner\OwnerMessageController;
use App\Http\Controllers\Owner\Report\StockReportController;
use App\Http\Controllers\Partner\PartnerMessageController;
use App\Notifications\CustomerVerifyEmail;
use App\Models\Owner;

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

    Route::get('/partner/account-suspended', function () {
        return view('pages.owner.owner-management.partner-account-suspended');
    })->name('partner.account.suspended')->middleware('partner.access');

    Route::get('/employee/account-suspended', function () {
        return view('pages.owner.owner-management.employee-account-suspended');
    })->name('employee.account.suspended')->middleware('employee.access');

    Route::get('/customer/account-suspended', function () {
        return view('pages.owner.owner-management.customer-account-suspended');
    })->name('customer.account.suspended')->middleware('customer.access');


    //admin
    Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::prefix('owner-list')->name('owner-list.')->group(function () {
            Route::get('/', [OwnerListController::class, 'index'])->name('index');
            // In your admin routes group
            Route::post('{owner}/toggle-status', [OwnerListController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('{ownerId}/outlets/{outletId}/toggle-status', [OwnerListController::class, 'toggleOutletStatus'])->name('outlets.toggle-status');
            Route::post('{ownerId}/outlets/{outletId}/employees/{employeeId}/toggle-status', [OwnerListController::class, 'toggleEmployeeStatus'])->name('employees.toggle-status');

            Route::get('{ownerId}/outlets', [OwnerListController::class, 'showOutlets'])->name('outlets');
            Route::get('{ownerId}/outlets/{outletId}/data', [OwnerListController::class, 'showOutletData'])->name('outlet-data');
        });

        Route::get('/owner-verification', [OwnerVerificationController::class, 'index'])->name('owner-verification');

        Route::get('/owner-verification/{id}', [OwnerVerificationController::class, 'show'])->name('owner-verification.show');
        Route::post('/owner-verification/{id}/approve', [OwnerVerificationController::class, 'approve'])->name('owner-verification.approve');
        Route::post('/owner-verification/{id}/reject', [OwnerVerificationController::class, 'reject'])->name('owner-verification.reject');
        Route::get('/owner-verification/{id}/ktp-image', [OwnerVerificationController::class, 'showKtpImage'])->name('owner-verification.ktp-image');

        // Route::prefix('send-payment')->name('send-payment.')->group(function () {
        //     Route::prefix('payout')->name('payout.')->group(function () {
        //         Route::get('/', [PayoutController::class, 'index'])->name('index');
        //         Route::post('get-data', [PayoutController::class, 'getData'])->name('get-data');
        //         Route::get('validate-bank', [PayoutController::class, 'validateBankAccount'])->name('validate-bank');
        //         Route::post('create', [PayoutController::class, 'createPayout'])->name('create');
        //         Route::get('{businessId}/detail/{payoutId}', [PayoutController::class, 'getPayout'])->name('detail');
        //     });
        // });
        Route::post('/owner-verification/register-xendit-account', [OwnerVerificationController::class, 'registerXenditAccount'])->name('owner-verification.register-xendit-account');;


        Route::prefix('xen_platform')->name('xen_platform.')->group(function () {
            Route::prefix('transactions')->name('transactions.')->group(function () {
                Route::get('/', [TransactionsController::class, 'index'])->name('index');
                Route::post('data', [TransactionsController::class, 'getData'])->name('data');
                Route::get('detail/{id}', [TransactionsController::class, 'getTransactionById'])->name('detail');
            });

            Route::prefix('balance')->name('balance.')->group(function () {
                Route::get('/', [BalanceController::class, 'index'])->name('index');
                Route::post('data', [BalanceController::class, 'getData'])->name('data');
            });

            Route::prefix('partner-account')->name('partner-account.')->group(function () {
                Route::prefix('{accountId}')->group(function () {
                    Route::get('information', [PartnerAccountController::class, 'showAccountInfo'])->name('information');
                    Route::get('tab/{tab}', [PartnerAccountController::class, 'getTabData']);
                    Route::get('filter/{tab}', [PartnerAccountController::class, 'filter']);;
                    Route::get('transaction-detail/{transactionId}', [PartnerAccountController::class, 'getTransactionById']);
                    Route::get('invoice-detail/{invoiceId}', [PartnerAccountController::class, 'getInvoiceById']);
                });
            });
            Route::resource('partner-account', PartnerAccountController::class);

            Route::prefix('split-payments')->name('split-payments.')->group(function () {
                Route::get('/', [SplitPaymentController::class, 'index'])->name('index');
                Route::get('/data', [SplitPaymentController::class, 'getSplitPayments']);;
                Route::prefix('rules')->name('rules.')->group(function () {
                    Route::get('/data', [SplitPaymentController::class, 'getSplitRules']);
                    Route::post('/create', [SplitPaymentController::class, 'createSplitRule'])->name('create');
                });
            });

            Route::prefix('disbursement')->name('disbursement.')->group(function () {
                Route::get('/', [DisbursementController::class, 'index'])->name('index');
                Route::post('get-data', [DisbursementController::class, 'getData'])->name('get-data');
                Route::get('validate-bank', [DisbursementController::class, 'validateBankAccount'])->name('validate-bank');
                Route::post('create', [DisbursementController::class, 'createPayout'])->name('create');
                Route::get('{businessId}/detail/{disbursementId}', [DisbursementController::class, 'getPayout'])->name('detail');
            });
        });

        Route::prefix('xendit')->name('xendit.')->group(function () {
            Route::prefix('sub-account')->name('sub-account.')->group(function () {
                Route::get('list', [SubAccountController::class, 'getSubAccounts'])->name('list');
                Route::get('profile/{id}', [SubAccountController::class, 'getSubAccountById'])->name('profile');
            });


            Route::prefix('balance')->name('balance.')->group(function () {
                //                Route::post('create', [SubAccountController::class, 'createAccount'])->name('create');
                //                Route::get('list', [SubAccountController::class, 'getSubAccounts'])->name('list');
            });
        });

        Route::prefix('message-notification')->name('message-notification.')->group(function () {
            Route::get('/get-recipients', [MessageController::class, 'getRecipients'])->name('get-recipients');
            Route::resource('messages', MessageController::class);
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

        Route::get('email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
            $owner = Owner::findOrFail($id);

            if (! hash_equals(
                (string) $hash,
                sha1($owner->getEmailForVerification())
            )) {
                abort(403, 'Link verifikasi tidak valid.');
            }

            if (! $owner->hasVerifiedEmail()) {
                $owner->markEmailAsVerified();
                event(new Verified($owner));
            }

            return redirect()
                ->route('owner.login')
                ->with('success', 'Email Anda berhasil diverifikasi. Silakan login.');
        })->middleware('signed')->name('verification.verify');

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
                $owner = $request->user('owner');

                if ($owner && $owner->hasVerifiedEmail()) {
                    auth('owner')->logout();
                    // $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()
                        ->route('owner.login')
                        ->with('success', 'Email Anda sudah terverifikasi. Silakan login.');
                }

                $owner->sendEmailVerificationNotification();

                return back()->with('status', 'verification-link-sent');
            })->middleware('throttle:6,1')->name('verification.send');


            // Verify link (signed)
            // Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
            //     $request->fulfill();
            //     return redirect()->route('owner.user-owner.dashboard')
            //         ->with('success', 'Email Anda berhasil diverifikasi.');
            // })->middleware('signed')->name('verification.verify');
        });

        // OWNER area
        Route::middleware(['auth:owner', 'is_owner:owner', 'verified'])->prefix('user-owner')->name('user-owner.')->group(function () {


            Route::middleware('owner.access')->group(function () {
                Route::get('/inactive-owners', function () {
                    return view('pages.owner.owner-management.owner-account-inactive');
                })->name('inactive-owners');
            });


            Route::middleware('owner.verification.access')->prefix('verification')->name('verification.')->group(function () {
                Route::get('/', [VerificationController::class, 'index'])->name('index');
                Route::post('/', [VerificationController::class, 'store'])->name('store');
                Route::get('/status', [VerificationController::class, 'status'])->name('status');
                Route::get('verification/ktp-image', [VerificationController::class, 'showKtpImage'])->name('ktp-image');
            });


            Route::middleware('owner.verification.access', 'owner.access')->group(function () {
                Route::get('/', [OwnerDashboardController::class, 'index'])->name('dashboard');
                Route::get('timeline/messages', [OwnerDashboardController::class, 'timelineMessages'])->name('timeline.messages');

                Route::prefix('messages')->name('messages.')->group(function () {
                    Route::get('/', action: [OwnerMessageController::class, 'index'])->name('index');
                    Route::get('/{id}', [OwnerMessageController::class, 'show'])->name('show');
                    Route::get('/notifications/list', [OwnerMessageController::class, 'getNotificationMessages'])->name('notifications');
                    Route::post('/mark-as-read/{id}', [OwnerMessageController::class, 'markMessageAsRead'])->name('mark-read');
                    Route::post('/mark-all-read', [OwnerMessageController::class, 'markAllMessagesAsRead'])->name('mark-all-read');
                });

                Route::get('outlets/check-username', [OwnerOutletController::class, 'checkUsername'])->name('outlets.check-username')->middleware('throttle:30,1');
                Route::get('outlets/check-slug', [OwnerOutletController::class, 'checkSlug'])->name('outlets.check-slug')->middleware('throttle:30,1');
                Route::resource('outlets', OwnerOutletController::class);
Route::get('tables/generate-barcode/{tableId}', [OwnerTablesController::class, 'generateBarcode'])->name('tables.generate-barcode');
Route::get('tables/generate-all-barcode', [OwnerTablesController::class, 'generateAllBarcode'])->name('tables.generate-all-barcode');
Route::resource('tables', OwnerTablesController::class);
                Route::get('employees/check-username', [OwnerEmployeeController::class, 'checkUsername'])->name('employees.check-username');
                Route::resource('employees', OwnerEmployeeController::class);
                Route::resource('master-products', OwnerMasterProductController::class);
                Route::get('outlet-products/get-master-products', [OwnerOutletProductController::class, 'getMasterProducts'])->name('outlet-products.get-master-products');
                Route::get('outlet-products/list-product', [OwnerOutletProductController::class, 'list'])->name('outlet-products.list');
                Route::resource('outlet-products', OwnerOutletProductController::class);

                Route::get('outlet-products/recipe/ingredients', [OwnerOutletProductController::class, 'getRecipeIngredients'])
                    ->name('outlet-products.recipe.ingredients');

                Route::get('outlet-products/recipe/load', [OwnerOutletProductController::class, 'loadRecipe'])
                    ->name('outlet-products.recipe.load');

                Route::post('outlet-products/recipe/save', [OwnerOutletProductController::class, 'saveRecipe'])
                    ->name('outlet-products.recipe.save');

                Route::resource('products', OwnerProductController::class);
                Route::post('/categories/reorder', [OwnerCategoryController::class, 'reorder'])->name('categories.reorder');
                Route::resource('categories', OwnerCategoryController::class);

                Route::prefix('xen_platform')->name('xen_platform.')->group(function () {
                    Route::prefix('accounts')->name('accounts.')->group(function () {
                        Route::get('information', [AccountsController::class, 'showAccountInfo'])->name('information');
                        Route::get('tab/{tab}', [AccountsController::class, 'getTabData']);
                        Route::get('filter/{tab}', [AccountsController::class, 'filter']);;
                        Route::get('transaction-detail/{transactionId}', [AccountsController::class, 'getTransactionById']);
                        Route::get('invoice-detail/{invoiceId}', [AccountsController::class, 'getInvoiceById']);
                    });

                    Route::prefix('split-payment')->name('split-payment.')->group(function () {
                        Route::get('/', [OwnerSplitPaymentController::class, 'index'])->name('index');
                        Route::get('/get-data', [OwnerSplitPaymentController::class, 'getSplitPayments'])->name('data');
                    });

                    Route::prefix('payout')->name('payout.')->group(function () {
                        Route::get('/', [OwnerPayoutController::class, 'index'])->name('index');
                        Route::post('get-data', [OwnerPayoutController::class, 'getData'])->name('get-data');
                        Route::get('validate-bank', [OwnerPayoutController::class, 'validateBankAccount'])->name('validate-bank');
                        Route::post('create', [OwnerPayoutController::class, 'createPayout'])->name('create');
                        Route::get('detail/{payoutId}', [OwnerPayoutController::class, 'getPayout'])->name('detail');
                    });
                });

                Route::prefix('report')->name('report.')->group(function () {
                    // Existing Sales Report Routes
                    Route::get('sales/export', [SalesReportController::class, 'export'])->name('sales.export');
                    Route::get('sales/products', [SalesReportController::class, 'getTopProductsAjax'])->name('sales.products');
                    Route::get('order-details/{id}', [SalesReportController::class, 'getOrderDetails'])->name('order-details');
                    Route::resource('sales', SalesReportController::class)->only(['index']);

                    Route::prefix('stocks')->name('stocks.')->group(function () {
                        Route::get('/', [StockReportController::class, 'index'])->name('index');
                        Route::get('/{stock:stock_code}/movement', [StockReportController::class, 'showStockMovement'])->name('movement');
                        Route::get('/export', [StockReportController::class, 'export'])->name('export');

                        Route::get('/{stock:stock_code}/movement/export', [StockReportController::class, 'exportMovement'])->name('movement.export');
                    });
                });
                Route::resource('promotions', OwnerPromotionController::class);
                Route::resource('payment-methods', OwnerPaymentMethodController::class);

                Route::prefix('stocks')->name('stocks.')->group(function () {
                    Route::delete('/delete-stock/{id}', [OwnerStockController::class, 'deleteStock'])->name('delete-stock');
                    Route::resource('/', OwnerStockController::class);

                    Route::prefix('movements')->name('movements.')->group(function () {
                        Route::get('/', [OwnerStockMovementController::class, 'index'])->name('index');

                        Route::get('/stock-in/create', [OwnerStockMovementController::class, 'createStockIn'])->name('create-stock-in');
                        Route::get('/adjustment/create', [OwnerStockMovementController::class, 'createAdjustment'])->name('create-adjustment');
                        Route::get('/transfer/create', [OwnerStockMovementController::class, 'createTransfer'])->name('create-transfer');

                        Route::post('/', [OwnerStockMovementController::class, 'store'])->name('store');
                        Route::get('/{id}/items', [OwnerStockMovementController::class, 'getMovementItemsJson'])->name('items.json');
                    });
                });


                Route::prefix('settings')->name('settings.')->group(function () {
                    Route::get('/', [OwnerSettingsController::class, 'index'])->name('index');
                    Route::get('/edit', [OwnerSettingsController::class, 'edit'])->name('edit'); // TAMBAH INI
                    Route::post('/personal-info', [OwnerSettingsController::class, 'updatePersonalInfo'])->name('update-personal-info');
                });
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
    Route::middleware(['auth', 'is_partner', 'partner.access'])->prefix('partner')->name('partner.')->group(function () {
        Route::get('/', [PartnerDashboardController::class, 'index'])->name('dashboard');

        Route::get('timeline/messages', [PartnerDashboardController::class, 'timelineMessages'])
            ->name('timeline.messages');

        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/', [PartnerMessageController::class, 'index'])->name('index');
            Route::get('/{id}', [PartnerMessageController::class, 'show'])->name('show');
            Route::get('/notifications/list', [PartnerMessageController::class, 'getNotificationMessages'])->name('notifications');
            Route::post('/mark-as-read/{id}', [PartnerMessageController::class, 'markMessageAsRead'])->name('mark-read');
            Route::post('/mark-all-read', [PartnerMessageController::class, 'markAllMessagesAsRead'])->name('mark-all-read');
        });

        Route::get('products/recipe/ingredients', [PartnerProductController::class, 'getRecipeIngredients'])
            ->name('products.recipe.ingredients');

        Route::get('products/recipe/load', [PartnerProductController::class, 'loadRecipe'])
            ->name('products.recipe.load');

        Route::post('products/recipe/save', [PartnerProductController::class, 'saveRecipe'])
            ->name('products.recipe.save');

        Route::resource('products', PartnerProductController::class);
        Route::prefix('store')->name('store.')->group(function () {
            Route::get('tables/generate-barcode/{tableId}', [PartnerTableController::class, 'generateBarcode'])->name('tables.generate-barcode');
            Route::get('tables/generate-all-barcode', [PartnerTableController::class, 'generateAllBarcode'])->name('tables.generate-all-barcode');
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
        Route::middleware(['auth:employee', 'employee.access', 'is_employee:CASHIER'])->prefix('cashier')->name('cashier.')->group(function () {
            Route::get('dashboard', [CashierDashboardController::class, 'index'])->name('dashboard');
            Route::get('metrics', [CashierDashboardController::class, 'metrics'])->name('metrics');
            Route::get('tab/{tab}', [CashierDashboardController::class, 'show'])->name('tab');
            Route::get('/open-order/{id}', [CashierDashboardController::class, 'openOrder'])->name('open-order');
            Route::get('/cashier/activity', [CashierDashboardController::class, 'activity'])->name('activity');

            Route::post('cash-payment/{id}', [CashierTransactionController::class, 'cashPayment'])->name('cash-payment');
            Route::get('order-detail/{id}', [CashierTransactionController::class, 'orderDetail'])->name('order-detail');
            Route::get('print-receipt/{id}', [CashierTransactionController::class, 'printReceipt'])->name('print-receipt');
            Route::post('cancel-process-order/{id}', [CashierTransactionController::class, 'cancelProcessOrder'])->name('cancel-process-order');
            Route::post('process-order/{id}', [CashierTransactionController::class, 'processOrder'])->name('process-order');
            Route::post('finish-order/{id}', [CashierTransactionController::class, 'finishOrder'])->name('finish-order');
            Route::post('checkout-order', [CashierTransactionController::class, 'checkout'])->name('checkout');
            Route::post('check-stock', [CashierTransactionController::class, 'checkStockRealtime'])->name('check-stock');
            Route::delete('order/{id}/soft-delete', [CashierTransactionController::class, 'softDeleteUnpaidOrder'])->name('order.soft-delete');
        });


        // KITCHEN area
        Route::middleware(['auth:employee', 'employee.access', 'is_employee:KITCHEN'])->prefix('kitchen')->name('kitchen.')->group(function () {
            Route::get('dashboard', [KitchenDashboardController::class, 'index'])->name('dashboard');

            // API Endpoints - UPDATE YANG SUDAH ADA
            Route::get('orders/queue', [KitchenDashboardController::class, 'getOrderQueue'])->name('orders.queue');
            Route::get('orders/active', [KitchenDashboardController::class, 'getActiveOrders'])->name('orders.active');
            Route::get('orders/served', [KitchenDashboardController::class, 'getServedOrders'])->name('orders.served');
            Route::put('orders/{orderId}/pickup', [KitchenDashboardController::class, 'pickUpOrder'])->name('orders.pickup');
            Route::put('orders/{orderId}/serve', [KitchenDashboardController::class, 'markAsServed'])->name('orders.serve');
            Route::put('orders/{orderId}/cancel', [KitchenDashboardController::class, 'cancelOrder'])->name('orders.cancel');

            // TAMBAH ROUTE BARU UNTUK KITCHEN_STATUS
            Route::get('orders/my-active', [KitchenDashboardController::class, 'getMyActiveOrders'])->name('orders.my-active');
            Route::get('summary', [KitchenDashboardController::class, 'getKitchenSummary'])->name('summary');
            Route::put('orders/{orderId}/release', [KitchenDashboardController::class, 'releaseOrder'])->name('orders.release');
            Route::get('kitchen/orders/{orderId}/test', [KitchenDashboardController::class, 'testOrderStatus'])->name('orders.test');
            Route::get('kitchen/orders/{orderId}/debug', [KitchenDashboardController::class, 'debugOrderStatus'])->name('orders.debug');
        });

        // MANAGER & SUPERVISOR area
        $staffRoles = ['manager', 'supervisor'];

        foreach ($staffRoles as $role) {
            Route::middleware(['auth:employee', 'employee.access', 'is_employee:MANAGER,SUPERVISOR'])->prefix($role)->name($role . '.')->group(function () {

                // Dashboard & General
                Route::get('dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
                Route::get('timeline/messages', [StaffDashboardController::class, 'timelineMessages'])->name('timeline.messages');

                // Messages
                Route::prefix('messages')->name('messages.')->group(function () {
                    Route::get('/', [StaffMessageController::class, 'index'])->name('index');
                    Route::get('/{id}', [StaffMessageController::class, 'show'])->name('show');
                    Route::get('/notifications/list', [StaffMessageController::class, 'getNotificationMessages'])->name('notifications');
                    Route::post('/mark-as-read/{id}', [StaffMessageController::class, 'markMessageAsRead'])->name('mark-read');
                    Route::post('/mark-all-read', [StaffMessageController::class, 'markAllMessagesAsRead'])->name('mark-all-read');
                });

                // // Employees
                // Route::get('employees/check-username', [StaffEmployeeController::class, 'checkUsername'])->name('employees.check-username');
                // Route::resource('employees', StaffEmployeeController::class);
                // Employees ← TAMBAHKAN INI
        Route::get('employees/check-username', [StaffEmployeeController::class, 'checkUsername'])->name('employees.check-username')->middleware('throttle:30,1');
        Route::resource('employees', StaffEmployeeController::class);

                // PRODUCTS (Di Owner ini adalah Outlet Products)
                Route::resource('products', StaffProductController::class);
                Route::prefix('products')->name('products.')->group(function () {
                    Route::get('get-master-products', [StaffProductController::class, 'getMasterProducts'])->name('get-master-products');
                    Route::get('list-product', [StaffProductController::class, 'list'])->name('list');

                    // Recipe Management
                    Route::get('recipe/ingredients', [StaffProductController::class, 'getRecipeIngredients'])->name('recipe.ingredients');
                    Route::get('recipe/load', [StaffProductController::class, 'loadRecipe'])->name('recipe.load');
                    Route::post('recipe/save', [StaffProductController::class, 'saveRecipe'])->name('recipe.save');
                });

                // Categories
                Route::post('/categories/reorder', [StaffCategoryController::class, 'reorder'])->name('categories.reorder');
                Route::resource('categories', StaffCategoryController::class);

                // // Promotions
                Route::resource('promotions', StaffPromotionController::class);

                // // Payment Methods

                // Route::resource('payment-methods', StaffPaymentMethodController::class);

                // // Reports
                Route::prefix('report')->name('report.')->group(function () {
                //     // Sales
                //     Route::get('sales/export', [StaffSalesReportController::class, 'export'])->name('sales.export');
                //     Route::get('sales/products', [StaffSalesReportController::class, 'getTopProductsAjax'])->name('sales.products');
                //     Route::get('order-details/{id}', [StaffSalesReportController::class, 'getOrderDetails'])->name('order-details');
                //     Route::resource('sales', StaffSalesReportController::class)->only(['index']);

                    // Stocks
                    Route::prefix('stocks')->name('stocks.')->group(function () {
                        Route::get('/', [StaffStockReportController::class, 'index'])->name('index');
                        Route::get('/{stock:stock_code}/movement', [StaffStockReportController::class, 'showStockMovement'])->name('movement');
                        Route::get('/export', [StaffStockReportController::class, 'export'])->name('export');
                        Route::get('/{stock:stock_code}/movement/export', [StaffStockReportController::class, 'exportMovement'])->name('movement.export');
                    });
                });

                // // Stocks & Movements
                // Route::prefix('stocks')->name('stocks.')->group(function () {
                //     Route::delete('/delete-stock/{id}', [StaffStockController::class, 'deleteStock'])->name('delete-stock');

                //     // Parameter binding manual agar route URL jadi '/stocks/{stock}' bukan '/stocks/{stock_id}' dll
                //     Route::resource('/', StaffStockController::class)->parameters(['' => 'stock']);

                //     Route::prefix('movements')->name('movements.')->group(function () {
                //         Route::get('/', [StaffStockMovementController::class, 'index'])->name('index');
                //         Route::get('/stock-in/create', [StaffStockMovementController::class, 'createStockIn'])->name('create-stock-in');
                //         Route::get('/adjustment/create', [StaffStockMovementController::class, 'createAdjustment'])->name('create-adjustment');
                //         Route::get('/transfer/create', [StaffStockMovementController::class, 'createTransfer'])->name('create-transfer');
                //         Route::post('/', [StaffStockMovementController::class, 'store'])->name('store');
                //         Route::get('/{id}/items', [StaffStockMovementController::class, 'getMovementItemsJson'])->name('items.json');
                //     });
                // });

                
                // Settings
Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [StaffSettingsController::class, 'index'])->name('index');
    Route::get('/edit', [StaffSettingsController::class, 'edit'])->name('edit');
    Route::post('/personal-info', [StaffSettingsController::class, 'updatePersonalInfo'])->name('update-personal-info');
});
            });
        }
    });

    // customer verify email
    Route::get('customer/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
        $customer = Customer::findOrFail($id);

        if (! hash_equals(
            (string) $hash,
            sha1($customer->getEmailForVerification())
        )) {
            abort(403, 'Link verifikasi tidak valid.');
        }

        if (! $customer->hasVerifiedEmail()) {
            $customer->markEmailAsVerified();
            event(new Verified($customer));
        }

        $partnerSlug = $request->query('partner_slug');
        $tableCode   = $request->query('table_code');

        if ($partnerSlug && $tableCode) {
            return redirect()->route('customer.menu.index', [
                'partner_slug' => $partnerSlug,
                'table_code'   => $tableCode,
            ])->with('success', 'Email Anda berhasil diverifikasi.');
        }

        return redirect('/')
            ->with('success', 'Email Anda berhasil diverifikasi.');
    })->middleware('signed')->name('customer.verification.verify');

    Route::get('customer/reset-password/{token}', [CustomerPasswordResetController::class, 'resetForm'])
        ->name('customer.password.reset');
    Route::post('customer/reset-password', [CustomerPasswordResetController::class, 'update'])
        ->name('customer.password.update');


    //customer
    Route::prefix('customer')->name('customer.')->middleware('customer.access')->group(function () {
        Route::get('{partner_slug}/table-status/{table_code}', [TableStatusController::class, 'show'])->name('table.status');

        Route::get('{partner_slug}/menu/{table_code}', [CustomerMenuController::class, 'index'])->name('menu.index')->middleware(['throttle:30,1', 'check.table.status']);
        Route::post('{partner_slug}/menu/{table_code}/check-stock', [CustomerMenuController::class, 'checkStockRealtime'])->name('menu.check-stock');
        Route::post('{partner_slug}/checkout/{table_code}', [CustomerMenuController::class, 'checkout'])->name('menu.checkout')->middleware(['throttle:30,1', 'check.table.status']);
        Route::get('{partner_slug}/order-detail/{table_code}/{order_id}', [CustomerMenuController::class, 'orderDetail'])->name('orders.order-detail');
        Route::get('{partner_slug}/order-manual-payment/{table_code}/{order_id}', [CustomerMenuController::class, 'orderManualPayment'])->name('orders.order-manual-payment');
        Route::post('{partner_slug}/{table_code}/orders/{order_id}/manual-payment/upload', [CustomerMenuController::class, 'uploadManualPaymentProof'])->name('orders.manual-payment.upload');
        Route::get('{partner_slug}/order-histories/{table_code}', [CustomerMenuController::class, 'getOrderHistory'])->name('orders.histories');
        Route::post('{partner_slug}/unpaid-order/{order_id}', [CustomerMenuController::class, 'makeUnpaidOrder'])->name('orders.unpaid-order');
        Route::get('/orders/{id}/receipt', [CustomerMenuController::class, 'printReceipt'])->name('orders.receipt');
        Route::post('/cancel-order/{id}', [CustomerMenuController::class, 'cancelOrder'])->name('orders.cancel-order');

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
            Route::get('forgot-password/{partner_slug}/{table_code}', [CustomerPasswordResetController::class, 'requestForm'])
                ->name('password.request');

            Route::post('forgot-password/{partner_slug}/{table_code}', [CustomerPasswordResetController::class, 'sendLink'])
                ->name('password.email');
        });

        Route::middleware('auth:customer')->group(function () {
            // Halaman notice
            Route::get('/email/verify', function () {
                return view('pages.customer.auth.verify-email'); // buat view ini
            })->name('verification.notice');

            // Kirim ulang link (rate limited)
            Route::post('/email/verification-notification', function (Request $request) {
                $customer = $request->user('customer');

                // ambil dari session (sudah kamu simpan waktu login/register)
                $partnerSlug = session('customer.partner_slug');
                $tableCode   = session('customer.table_code');

                if ($partnerSlug && $tableCode) {
                    $customer->notify(new CustomerVerifyEmail($partnerSlug, $tableCode));
                } else {
                    // fallback: kirim URL tanpa konteks meja (optional)
                    $customer->notify(new CustomerVerifyEmail('', ''));
                }

                return back()->with('status', 'verification-link-sent');
            })->middleware('throttle:6,1')->name('verification.send');
        });

        Route::middleware('auth:customer', 'verified')->group(function () {
            Route::get('/dashboard', function () {
                return view('customer.dashboard');
            })->name('dashboard');
        });
    });
    require __DIR__ . '/auth.php';
});
