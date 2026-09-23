<?php

use Illuminate\Support\Facades\Route;
use Modules\Seminar\Http\Controllers\SeminarController;
use Modules\Seminar\Http\Controllers\SeminarOfferController;

Route::get('/create-seminar-request', [SeminarController::class, 'getCreate'])->name('create-seminar-request');
Route::get('/create-seminar-request/create', [SeminarController::class, 'getCreate'])->middleware('auth')->name('seminar-request.start');
Route::post('/create-seminar-request', [SeminarController::class, 'postCreate'])->middleware('auth')->name('seminar-request.store');
Route::get('/create-seminar-offer', [SeminarOfferController::class, 'create'])->name('create-seminar-offer');
Route::post('/create-seminar-offer', [SeminarOfferController::class, 'store'])->name('seminar-offer.store');

Route::middleware(['auth', 'role:1'])->group(function () {
    Route::get('/admin/seminar-requests', [SeminarController::class, 'getList'])->name('admin.seminar-requests');
    Route::get('/admin/seminar-offers', [SeminarOfferController::class, 'index'])->name('admin.seminar-offers');
    Route::patch('/admin/seminar-subject-proposals/{seminarSubjectProposal}/accept', [SeminarOfferController::class, 'acceptSubjectProposal'])->name('admin.seminar-subject-proposals.accept');
});

Route::get('/new-seminar-subject', [SeminarController::class, 'getCreateSubject'])->name('new-seminar-subject');
Route::post('/new-seminar-subject', [SeminarController::class, 'postCreateSubject']);
Route::get('/edit-seminar-subject/{id}', [SeminarController::class, 'getEditSubject'])->name('edit-seminar-subject');
Route::post('/edit-seminar-subject/{id}', [SeminarController::class, 'postEditSubject']);
Route::get('/seminar-subjects', [SeminarController::class, 'getSubjectList'])->name('seminar-subjects');
