<?php

use Illuminate\Support\Facades\Route;
use Modules\MailForwarding\Http\Controllers\AdminForwardingController;
use Modules\MailForwarding\Http\Controllers\EmailRedirectsController;

Route::get('/email-redirects', [EmailRedirectsController::class, 'getValidation']);
Route::post('/email-redirects', [EmailRedirectsController::class, 'postValidation'])->name('email-redirects');

Route::get('/email-forwarding', function () {
     return redirect('/email-redirects');
});
Route::post('/email-forwarding', [EmailRedirectsController::class, 'postForwarding'])->name('email-forwarding');

Route::middleware(['auth', 'role:1'])->group(function () {
    Route::post('/send-penguen-welcome/{user_id}', [AdminForwardingController::class, 'sendWelcome'])->name('send-penguen-welcome');
    Route::delete('/remove-penguen/{user_id}', [AdminForwardingController::class, 'remove'])->name('remove-penguen');
});
