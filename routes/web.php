<?php

use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('welcome')); // keep your welcome

Route::prefix('student/{student}')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/profile',   [StudentController::class, 'profile'])->name('student.profile');
    Route::post('/profile',  [StudentController::class, 'updateProfile'])->name('student.profile.update');

    Route::get('/classes',   [StudentController::class, 'classes'])->name('student.classes');
    Route::post('/enroll',   [StudentController::class, 'enroll'])->name('student.enroll');
    Route::post('/unenroll', [StudentController::class, 'unenroll'])->name('student.unenroll');

    Route::get('/payments',  [StudentController::class, 'payments'])->name('student.payments');
    Route::post('/pay',      [StudentController::class, 'makePayment'])->name('student.pay');
    Route::get('/receipts',  [StudentController::class, 'receipts'])->name('student.receipts');

    Route::get('/api/payments', [StudentController::class, 'paymentsApi'])->name('student.api.payments');
});


