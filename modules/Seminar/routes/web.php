<?php

use Illuminate\Support\Facades\Route;
use Modules\Seminar\Http\Controllers\SeminarController;
use Modules\Seminar\Http\Controllers\SeminarOfferController;

Route::get('/create-seminar-request', [SeminarController::class, 'getCreate'])->name('create-seminar-request');
Route::get('/create-seminar-request/create', [SeminarController::class, 'getCreate'])->middleware('auth')->name('seminar-request.start');
Route::post('/create-seminar-request', [SeminarController::class, 'postCreate'])->middleware('auth')->name('seminar-request.store');
Route::get('/create-seminar-offer', [SeminarOfferController::class, 'create'])->name('create-seminar-offer');
Route::post('/create-seminar-offer', [SeminarOfferController::class, 'store'])->name('seminar-offer.store');

// Old addresses of pages that moved to the admin panel.
Route::permanentRedirect('/seminar-subjects', '/admin/seminar-subjects');
Route::permanentRedirect('/new-seminar-subject', '/admin/seminar-subjects/create');
Route::get('/edit-seminar-subject/{id}', fn ($id) => redirect()->route('admin.seminar-subjects.edit', $id, 301));
