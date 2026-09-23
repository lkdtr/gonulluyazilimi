<?php

use Illuminate\Support\Facades\Route;
use Modules\Representation\Http\Controllers\LegalRepresentationController;

Route::middleware('auth')->group(function () {
    Route::get('/representations', [LegalRepresentationController::class, 'index'])->name('representations.index');
    Route::get('/representations/{representation}/contact-consent', [LegalRepresentationController::class, 'consent'])->name('representations.consent');
    Route::post('/representations/{representation}/contact-consent', [LegalRepresentationController::class, 'storeConsent'])->name('representations.consent.store');
    Route::get('/representation-candidate', [LegalRepresentationController::class, 'candidateForm'])->name('representations.candidate');
    Route::post('/representation-candidate', [LegalRepresentationController::class, 'storeCandidate'])->name('representations.candidate.store');
    Route::get('/representation-announcements', [LegalRepresentationController::class, 'announcements'])->name('representations.announcements');
    Route::post('/representation-announcements', [LegalRepresentationController::class, 'storeAnnouncement'])->name('representations.announcements.store');
});

Route::middleware(['auth', 'role:1'])->group(function () {
    Route::get('/admin/representations', [LegalRepresentationController::class, 'admin'])->name('admin.representations');
    Route::get('/admin/representations/{representation}/edit', [LegalRepresentationController::class, 'edit'])->name('admin.representations.edit');
    Route::patch('/admin/representations/{representation}', [LegalRepresentationController::class, 'update'])->name('admin.representations.update');
    Route::patch('/admin/representation-candidates/{candidate}/approve', [LegalRepresentationController::class, 'approveCandidate'])->name('admin.representations.candidates.approve');
    Route::patch('/admin/representation-candidates/{candidate}/reject', [LegalRepresentationController::class, 'rejectCandidate'])->name('admin.representations.candidates.reject');
    Route::patch('/admin/representations/{representation}/announcement-permission', [LegalRepresentationController::class, 'setAnnouncementPermission'])->name('admin.representations.announcement-permission');
    Route::patch('/admin/representations/{representation}/user', [LegalRepresentationController::class, 'assignUser'])->name('admin.representations.assign-user');
    Route::patch('/admin/representation-announcements/{announcement}/approve', [LegalRepresentationController::class, 'approveAnnouncement'])->name('admin.representations.announcements.approve');
});
