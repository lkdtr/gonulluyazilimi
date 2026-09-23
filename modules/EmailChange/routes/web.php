<?php

use Illuminate\Support\Facades\Route;
use Modules\EmailChange\Http\Controllers\EmailChangeRequestController;

Route::get('/email-change-request', [EmailChangeRequestController::class, 'create'])->middleware('auth')->name('email-change-requests.create');
Route::post('/email-change-request', [EmailChangeRequestController::class, 'store'])->middleware('auth')->name('email-change-requests.store');

Route::middleware(['auth', 'role:1'])->group(function () {
    Route::get('/admin/email-change-requests', [EmailChangeRequestController::class, 'index'])->name('admin.email-change-requests');
    Route::patch('/admin/email-change-requests/{emailChangeRequest}/approve', [EmailChangeRequestController::class, 'approve'])->name('admin.email-change-requests.approve');
    Route::patch('/admin/email-change-requests/{emailChangeRequest}/reject', [EmailChangeRequestController::class, 'reject'])->name('admin.email-change-requests.reject');
});
