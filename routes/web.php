<?php

use App\Http\Controllers\PayPalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()
        ->route('createTransaction');
})->name('home');

Route::get('createTransaction', [PayPalController::class, 'createTransaction'])->name('createTransaction');
Route::post('processTransaction', [PayPalController::class, 'processTransaction'])->name('processTransaction');
Route::get('successTransaction', [PayPalController::class, 'successTransaction'])->name('successTransaction');
Route::get('cancelTransaction', [PayPalController::class, 'cancelTransaction'])->name('cancelTransaction');

Route::get('/success', function () {
    return view('success');
})->name('success');
