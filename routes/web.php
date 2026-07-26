<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Auth\LoginController;

// Redirect root to dashboard or login
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Language switch
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['fr', 'en'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

// Admin / Authenticated routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', function() { return redirect()->route('admin.dashboard'); });

    // Properties (Logements)
    Route::resource('properties', PropertyController::class);
    Route::post('properties/{property}/toggle-status', [PropertyController::class, 'toggleStatus'])->name('properties.toggle-status');
    Route::post('properties/{property}/photos', [PropertyController::class, 'uploadPhoto'])->name('properties.photos.upload');
    Route::delete('properties/{property}/photos/{photo}', [PropertyController::class, 'deletePhoto'])->name('properties.photos.delete');

    // Clients
    Route::resource('clients', ClientController::class);
    Route::get('clients/{client}/reservations', [ClientController::class, 'reservations'])->name('clients.reservations');

    // Services (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('services', ServiceController::class);
    });

    // Reservations
    Route::resource('reservations', ReservationController::class);
    Route::patch('reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::patch('reservations/{reservation}/confirm', [ReservationController::class, 'confirm'])->name('reservations.confirm');
    Route::patch('reservations/{reservation}/checkin', [ReservationController::class, 'checkIn'])->name('reservations.checkin');
    Route::patch('reservations/{reservation}/checkout', [ReservationController::class, 'checkOut'])->name('reservations.checkout');
    Route::get('reservations/{reservation}/invoice', [ReservationController::class, 'invoice'])->name('reservations.invoice');
    Route::get('reservations/{reservation}/invoice/pdf', [ReservationController::class, 'invoicePdf'])->name('reservations.invoice.pdf');
    Route::post('reservations/{reservation}/payments', [ReservationController::class, 'addPayment'])->name('reservations.payments.add');

    // Calendar
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');

    // Finances (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/finances', [FinanceController::class, 'index'])->name('finances.index');
        Route::get('/finances/expenses/create', [FinanceController::class, 'createExpense'])->name('finances.create-expense');
        Route::post('/finances/expenses', [FinanceController::class, 'storeExpense'])->name('finances.store-expense');
        Route::delete('/finances/expenses/{expense}', [FinanceController::class, 'destroyExpense'])->name('finances.expenses.destroy');

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
        Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');

        // Users
        Route::resource('users', UserController::class);
    });
});

// API routes for calendar / AJAX
Route::middleware('auth')->prefix('api')->name('api.')->group(function () {
    Route::get('/properties/availability', [PropertyController::class, 'checkAvailability'])->name('properties.availability');
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
});

// Redirect legacy dashboard path
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware('auth')->name('dashboard');
