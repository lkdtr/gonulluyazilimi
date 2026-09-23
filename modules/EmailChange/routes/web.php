<?php

use Illuminate\Support\Facades\Route;
use Modules\EmailChange\Http\Controllers\EmailChangeRequestController;

Route::get('/email-change-request', [EmailChangeRequestController::class, 'create'])->middleware('auth')->name('email-change-requests.create');
Route::post('/email-change-request', [EmailChangeRequestController::class, 'store'])->middleware('auth')->name('email-change-requests.store');
