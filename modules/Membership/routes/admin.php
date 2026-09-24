<?php

use Illuminate\Support\Facades\Route;
use Modules\Membership\Http\Controllers\Admin\MembershipController;

Route::middleware('permission:memberships.view')->group(function () {
    Route::get('/memberships', [MembershipController::class, 'index'])->name('memberships');
});

Route::middleware('permission:memberships.manage')->group(function () {
    Route::post('/contacts/{contact}/membership', [MembershipController::class, 'store'])->name('memberships.store');
    Route::put('/memberships/{membership}', [MembershipController::class, 'update'])->name('memberships.update');
    Route::patch('/memberships/{membership}/status', [MembershipController::class, 'status'])->name('memberships.status');
    Route::post('/memberships/{membership}/events', [MembershipController::class, 'event'])->name('memberships.events.store');
});
