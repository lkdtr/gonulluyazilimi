<?php

use Illuminate\Support\Facades\Route;
use Modules\Announcements\Http\Controllers\AnnouncementController;

Route::get('/announcements', [AnnouncementController::class, 'getList'])->name('announcements');
Route::get('/new-announcement', [AnnouncementController::class, 'getCreate'])->name('new-announcement');
Route::post('/new-announcement', [AnnouncementController::class, 'postCreate']);

Route::get('/edit-announcement/{id}', [AnnouncementController::class, 'getEdit'])->name('edit-announcement');
Route::post('/edit-announcement/{id}', [AnnouncementController::class, 'postEdit']);
