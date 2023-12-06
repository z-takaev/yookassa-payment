<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PaymentController::class, 'index'])->name('payment.index');
Route::get('payment/{product}', [PaymentController::class, 'process'])->name('payment.process');
Route::post('payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
