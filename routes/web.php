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

require __DIR__.'/auth.php';

Route::post('language/{locale}', [App\Http\Controllers\LanguageController::class, 'switch'])->name('language.switch');
Route::get('language-test', [App\Http\Controllers\LanguageController::class, 'test'])->name('language.test');

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [\Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class]
], function() {
    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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

            // Transactions routes
            Route::get('transactions', [PettyCashTransactionController::class, 'index'])->name('transactions.index');
            Route::get('transactions/create', [PettyCashTransactionController::class, 'create'])->name('transactions.create');
            Route::post('transactions', [PettyCashTransactionController::class, 'store'])->name('transactions.store');
        });

        // Parties routes
        Route::resource('parties', PartyController::class);

        // Party Groups routes
        Route::resource('party-groups', PartyGroupController::class);

        // Expense Groups
        Route::resource('expense-groups', ExpenseGroupController::class)->middleware(['auth', 'verified']);

        // Expenses
        Route::resource('expenses', ExpenseController::class)->middleware(['auth', 'verified']);
    });
});