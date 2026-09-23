<?php

use Illuminate\Support\Facades\Route;
use Modules\LkdYoung\Http\Controllers\LkdYoungController;

Route::get('/join-lkd-young', [LkdYoungController::class, 'getJoinLkdYoung'])->name('join-lkd-young');
Route::post('/join-lkd-young', [LkdYoungController::class, 'postJoinLkdYoung']);
Route::get('/lkd-young/announcements', [LkdYoungController::class, 'announcements'])->name('lkd-young.announcements');
Route::get('/lkd-young/announcements/create', [LkdYoungController::class, 'createAnnouncement'])->name('lkd-young.announcements.create');
Route::post('/lkd-young/announcements', [LkdYoungController::class, 'storeAnnouncement'])->name('lkd-young.announcements.store');
