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
