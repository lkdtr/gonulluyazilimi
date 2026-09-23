<?php

use Illuminate\Support\Facades\Route;
use Modules\Representation\Http\Controllers\Admin\LegalRepresentationController;

Route::middleware('role:1')->group(function () {
    Route::get('/representations', [LegalRepresentationController::class, 'admin'])->name('representations');
    Route::get('/representations/{representation}/edit', [LegalRepresentationController::class, 'edit'])->name('representations.edit');
    Route::patch('/representations/{representation}', [LegalRepresentationController::class, 'update'])->name('representations.update');
    Route::patch('/representation-candidates/{candidate}/approve', [LegalRepresentationController::class, 'approveCandidate'])->name('representations.candidates.approve');
    Route::patch('/representation-candidates/{candidate}/reject', [LegalRepresentationController::class, 'rejectCandidate'])->name('representations.candidates.reject');
    Route::patch('/representations/{representation}/announcement-permission', [LegalRepresentationController::class, 'setAnnouncementPermission'])->name('representations.announcement-permission');
    Route::patch('/representations/{representation}/user', [LegalRepresentationController::class, 'assignUser'])->name('representations.assign-user');
    Route::patch('/representation-announcements/{announcement}/approve', [LegalRepresentationController::class, 'approveAnnouncement'])->name('representations.announcements.approve');
});
