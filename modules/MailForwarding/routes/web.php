<?php

use Illuminate\Support\Facades\Route;
use Modules\MailForwarding\Http\Controllers\EmailRedirectsController;

Route::get('/email-redirects', [EmailRedirectsController::class, 'getValidation']);
Route::post('/email-redirects', [EmailRedirectsController::class, 'postValidation'])->name('email-redirects');

Route::get('/email-forwarding', function () {
     return redirect('/email-redirects');
});
Route::post('/email-forwarding', [EmailRedirectsController::class, 'postForwarding'])->name('email-forwarding');
