<?php

use Illuminate\Support\Facades\Route;
use Modules\LkdYoung\Http\Controllers\Admin\LkdYoungController;

Route::middleware('role:1')->group(function () {
    Route::get('/lkd-young', [LkdYoungController::class, 'admin'])->name('lkd-young');
    Route::patch('/lkd-young/representatives/{rep}/approve', [LkdYoungController::class, 'approveRepresentative'])->name('lkd-young.representatives.approve');
    Route::patch('/lkd-young/representatives/{rep}/reject', [LkdYoungController::class, 'rejectRepresentative'])->name('lkd-young.representatives.reject');
    Route::patch('/lkd-young/representatives/{rep}/announcement-permission', [LkdYoungController::class, 'setAnnouncementPermission'])->name('lkd-young.representatives.announcement-permission');
    Route::patch('/lkd-young/announcements/{a}/approve', [LkdYoungController::class, 'approveAnnouncement'])->name('lkd-young.announcements.approve');
});
