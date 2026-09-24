<?php

use Illuminate\Support\Facades\Route;
use Modules\MailForwarding\Http\Controllers\Admin\ForwardingController;
use Modules\MailForwarding\Http\Controllers\Admin\ForwardingSettingsController;

Route::middleware('role:1')->group(function () {
    Route::post('/users/{user_id}/forwarding-welcome', [ForwardingController::class, 'sendWelcome'])->name('forwarding.welcome');
    Route::delete('/users/{user_id}/forwarding', [ForwardingController::class, 'remove'])->name('forwarding.remove');
});

Route::middleware('permission:forwarding.manage')->group(function () {
    Route::get('/forwarding/settings', [ForwardingSettingsController::class, 'edit'])->name('forwarding.settings');
    Route::put('/forwarding/settings', [ForwardingSettingsController::class, 'update'])->name('forwarding.settings.update');
});
