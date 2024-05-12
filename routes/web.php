<?php

use App\Http\Controllers\PayPalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()
        ->route('createTransaction');
})->name('home');


Route::get('createTransaction', [PayPalController::class, 'createTransaction'])->name('createTransaction');

// redirect to paypal
Route::post('processTransactionRedirect', [PayPalController::class, 'processTransactionPaypalRedirect'])->name('processTransactionRedirect');
Route::get('successTransactionRedirect', [PayPalController::class, 'successTransactionRedirect'])->name('successTransactionRedirect');

// paypal button 
Route::post('processTransactionButton', [PayPalController::class, 'processTransactionPaypalButton'])->name('processTransactionButton');
Route::post('successTransactionButton', [PayPalController::class, 'successTransactionButton'])->name('successTransactionButton');

Route::get('cancelTransaction', [PayPalController::class, 'cancelTransaction'])->name('cancelTransaction');

Route::get('/success', function () {
    return view('success');
})->name('success');
