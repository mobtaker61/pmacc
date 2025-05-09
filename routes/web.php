<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PettyCashBoxController;
use App\Http\Controllers\PettyCashTransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\PartyGroupController;
use App\Http\Controllers\ExpenseGroupController;
use App\Http\Controllers\ExpenseController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root URL to the dashboard
Route::redirect('/', '/dashboard');

// The original welcome route is now replaced by the redirect above
// Route::get('/', function () {
//     return view('welcome');
// });

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => [
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class
        ]
    ],
    function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->middleware(['auth', 'verified'])
            ->name('dashboard');

        Route::post('language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

        // Test route for session locale
        Route::get('/test-session-locale', function () {
            return [
                'session_locale' => session('locale'),
                'app_locale' => app()->getLocale(),
            ];
        })->middleware('web');

        // نمایش همه تراکنش‌های تنخواه
        Route::get('/petty-cash/transactions', [PettyCashTransactionController::class, 'allTransactions'])->name('petty-cash.transactions.all');

        // نمایش تراکنش‌های یک صندوق خاص
        Route::get('/petty-cash/boxes/{box}/transactions', [PettyCashTransactionController::class, 'index'])->name('petty-cash.transactions.index');

        // Routes that need authentication
        Route::middleware('auth')->group(function () {
            // Profile routes
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

            // Resource routes
            Route::resource('petty_cash_boxes', PettyCashBoxController::class);
            Route::resource('petty_cash_transactions', PettyCashTransactionController::class);
            Route::resource('expense_groups', ExpenseGroupController::class);
            Route::resource('expenses', ExpenseController::class);
            Route::resource('party_groups', PartyGroupController::class);
            Route::resource('parties', PartyController::class);
            //Route::resource('settings', SettingsController::class);

            // Settings routes
            Route::resource('settings', SettingController::class)->only(['index', 'edit', 'update']);
            Route::get('/settings/try-rate', [SettingsController::class, 'getTryRate'])->name('settings.try-rate');

            // Petty Cash routes
            Route::prefix('petty-cash')->name('petty-cash.')->group(function () {
                // Boxes routes
                Route::get('boxes', [PettyCashBoxController::class, 'index'])->name('boxes.index');
                Route::get('boxes/create', [PettyCashBoxController::class, 'create'])->name('boxes.create');
                Route::post('boxes', [PettyCashBoxController::class, 'store'])->name('boxes.store');
                Route::get('boxes/{box}/edit', [PettyCashBoxController::class, 'edit'])->name('boxes.edit');
                Route::put('boxes/{box}', [PettyCashBoxController::class, 'update'])->name('boxes.update');

                // Transactions CRUD (بدون all و بدون index با پارامتر box)
                Route::get('transactions/create', [PettyCashTransactionController::class, 'create'])->name('transactions.create');
                Route::post('transactions', [PettyCashTransactionController::class, 'store'])->name('transactions.store');
            });
        });

        Route::get('/test-locale', function () {
            return [
                'session_locale' => session('locale'),
                'app_locale' => app()->getLocale(),
            ];
        });

        require __DIR__ . '/auth.php';
    }
);
