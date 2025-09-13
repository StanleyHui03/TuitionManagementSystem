<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceiptController;
use Illuminate\Support\Facades\Route;

use App\Http\Middleware\Authenticate; 
use Illuminate\Foundation\Application;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\PaymentReportController;
use Inertia\Inertia;

//////////////
// Resource routes
Route::resource('receipts', ReceiptController::class);
Route::resource('payments', PaymentsController::class);

// Undo (restore) route for payments
Route::patch('/payments/{id}/restore', [PaymentsController::class, 'undoDelete'])
    ->name('payments.undo');

Route::get('/payments/{payment}/view', [PaymentsController::class, 'view'])
    ->name('payments.view');

// Default landing page
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
/////////////

// Dashboard
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//////////////
Route::middleware(['auth', 'can:view-payments'])->group(function () {
    Route::get('/payments-report', [PaymentReportController::class, 'show'])
        ->name('payments.report');

    Route::get('/payments-report.json', [PaymentReportController::class, 'data'])
        ->name('payments.report.json');
});

if (app()->environment('local')) {
    Route::get('/_dev/payments-report.json', [PaymentReportController::class, 'data'])
        ->withoutMiddleware([Authenticate::class])
        ->name('payments.report.json.dev');
}
///////////
require __DIR__ . '/auth.php';

