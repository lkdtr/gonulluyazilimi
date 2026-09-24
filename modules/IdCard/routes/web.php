<?php

use Illuminate\Support\Facades\Route;
use Modules\IdCard\Http\Controllers\CardController;
use Modules\IdCard\Http\Controllers\VerifyController;

Route::middleware('auth')->group(function () {
    Route::get('/my-cards', [CardController::class, 'index'])->name('id-cards');
    Route::post('/my-cards/{card}/renew', [CardController::class, 'renew'])->name('id-cards.renew');
});

// Public: opened by the QR code on a card.
Route::get('/kart/{token}', [VerifyController::class, 'show'])->middleware('throttle:60,1')->where('token', '[0-9a-f]{40}')->name('id-card.verify');
Route::get('/id-card-logos/{template}', [CardController::class, 'logo'])->name('id-card.logo');
