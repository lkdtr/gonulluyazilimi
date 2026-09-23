<?php

use Illuminate\Support\Facades\Route;
use Modules\EmailChange\Http\Controllers\Admin\EmailChangeRequestController;

Route::middleware('role:1')->group(function () {
    Route::get('/email-change-requests', [EmailChangeRequestController::class, 'index'])->name('email-change-requests');
    Route::patch('/email-change-requests/{emailChangeRequest}/approve', [EmailChangeRequestController::class, 'approve'])->name('email-change-requests.approve');
    Route::patch('/email-change-requests/{emailChangeRequest}/reject', [EmailChangeRequestController::class, 'reject'])->name('email-change-requests.reject');
});
