<?php

use Illuminate\Support\Facades\Route;
use Modules\MailForwarding\Http\Controllers\Admin\ForwardingController;

Route::middleware('role:1')->group(function () {
    Route::post('/users/{user_id}/forwarding-welcome', [ForwardingController::class, 'sendWelcome'])->name('forwarding.welcome');
    Route::delete('/users/{user_id}/forwarding', [ForwardingController::class, 'remove'])->name('forwarding.remove');
});
