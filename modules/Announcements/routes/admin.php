<?php

use Illuminate\Support\Facades\Route;
use Modules\Announcements\Http\Controllers\Admin\AnnouncementController;

Route::get('/announcements', [AnnouncementController::class, 'getList'])->name('announcements');
Route::get('/announcements/create', [AnnouncementController::class, 'getCreate'])->name('announcements.create');
Route::post('/announcements/create', [AnnouncementController::class, 'postCreate']);
Route::get('/announcements/{id}/edit', [AnnouncementController::class, 'getEdit'])->name('announcements.edit');
Route::post('/announcements/{id}/edit', [AnnouncementController::class, 'postEdit']);
