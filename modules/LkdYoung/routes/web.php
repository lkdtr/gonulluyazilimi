<?php

use Illuminate\Support\Facades\Route;
use Modules\LkdYoung\Http\Controllers\LkdYoungController;

Route::get('/join-lkd-young', [LkdYoungController::class, 'getJoinLkdYoung'])->name('join-lkd-young');
Route::post('/join-lkd-young', [LkdYoungController::class, 'postJoinLkdYoung']);
Route::get('/lkd-young/announcements', [LkdYoungController::class, 'announcements'])->name('lkd-young.announcements');
Route::get('/lkd-young/announcements/create', [LkdYoungController::class, 'createAnnouncement'])->name('lkd-young.announcements.create');
Route::post('/lkd-young/announcements', [LkdYoungController::class, 'storeAnnouncement'])->name('lkd-young.announcements.store');

Route::middleware(['auth', 'role:1'])->group(function () {
    Route::get('/admin/lkd-young', [LkdYoungController::class, 'admin'])->name('admin.lkd-young');
    Route::patch('/admin/lkd-young/representatives/{rep}/approve', [LkdYoungController::class, 'approveRepresentative'])->name('admin.lkd-young.representatives.approve');
    Route::patch('/admin/lkd-young/representatives/{rep}/reject', [LkdYoungController::class, 'rejectRepresentative'])->name('admin.lkd-young.representatives.reject');
    Route::patch('/admin/lkd-young/representatives/{rep}/announcement-permission', [LkdYoungController::class, 'setAnnouncementPermission'])->name('admin.lkd-young.representatives.announcement-permission');
    Route::patch('/admin/lkd-young/announcements/{a}/approve', [LkdYoungController::class, 'approveAnnouncement'])->name('admin.lkd-young.announcements.approve');
});
