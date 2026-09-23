<?php

use Illuminate\Support\Facades\Route;
use Modules\Seminar\Http\Controllers\Admin\SeminarOfferController;
use Modules\Seminar\Http\Controllers\Admin\SeminarRequestController;
use Modules\Seminar\Http\Controllers\Admin\SeminarSubjectController;

Route::get('/seminar-subjects', [SeminarSubjectController::class, 'getSubjectList'])->name('seminar-subjects');
Route::get('/seminar-subjects/create', [SeminarSubjectController::class, 'getCreateSubject'])->name('seminar-subjects.create');
Route::post('/seminar-subjects/create', [SeminarSubjectController::class, 'postCreateSubject']);
Route::get('/seminar-subjects/{id}/edit', [SeminarSubjectController::class, 'getEditSubject'])->name('seminar-subjects.edit');
Route::post('/seminar-subjects/{id}/edit', [SeminarSubjectController::class, 'postEditSubject']);

Route::middleware('role:1')->group(function () {
    Route::get('/seminar-requests', [SeminarRequestController::class, 'getList'])->name('seminar-requests');
    Route::get('/seminar-offers', [SeminarOfferController::class, 'index'])->name('seminar-offers');
    Route::patch('/seminar-subject-proposals/{seminarSubjectProposal}/accept', [SeminarOfferController::class, 'acceptSubjectProposal'])->name('seminar-subject-proposals.accept');
});
