<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AffiliationTypeController;
use Modules\Admin\Http\Controllers\AgreementAdminController;
use Modules\Admin\Http\Controllers\ContactAffiliationController;
use Modules\Admin\Http\Controllers\ContactController;
use Modules\Admin\Http\Controllers\DashboardController;
use Modules\Admin\Http\Controllers\OrganizationSettingsController;
use Modules\Admin\Http\Controllers\PhotoReviewController;
use Modules\Admin\Http\Controllers\ProcessLogController;
use Modules\Admin\Http\Controllers\ProfileAdminController;
use Modules\Admin\Http\Controllers\RoleController;
use Modules\Admin\Http\Controllers\UserAdminController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware('permission:contacts.manage')->group(function () {
    Route::get('/contacts/create', [ContactController::class, 'create'])->name('contacts.create');
    Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
    Route::get('/contacts/{contact}/edit', [ContactController::class, 'edit'])->name('contacts.edit');
    Route::put('/contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update');
    Route::post('/contacts/{contact}/affiliations', [ContactAffiliationController::class, 'store'])->name('contacts.affiliations.store');
    Route::patch('/contacts/{contact}/affiliations/{affiliation}/end', [ContactAffiliationController::class, 'end'])->name('contacts.affiliations.end');
    Route::delete('/contacts/{contact}/affiliations/{affiliation}', [ContactAffiliationController::class, 'destroy'])->name('contacts.affiliations.destroy');
});

Route::middleware('permission:contacts.view')->group(function () {
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts');
    Route::get('/contacts/{contact}', [ContactController::class, 'show'])->name('contacts.show');
});

Route::middleware('permission:settings.manage')->group(function () {
    Route::get('/settings/organization', [OrganizationSettingsController::class, 'edit'])->name('settings.organization');
    Route::put('/settings/organization', [OrganizationSettingsController::class, 'update'])->name('settings.organization.update');
    Route::post('/settings/organization/images', [OrganizationSettingsController::class, 'uploadImage'])->middleware('throttle:30,1')->name('settings.organization.images');
});

Route::middleware('permission:agreements.manage')->group(function () {
    Route::get('/agreements', [AgreementAdminController::class, 'index'])->name('agreements');
    Route::get('/agreements/create', [AgreementAdminController::class, 'create'])->name('agreements.create');
    Route::post('/agreements', [AgreementAdminController::class, 'store'])->name('agreements.store');
    Route::get('/agreements/{agreement}', [AgreementAdminController::class, 'show'])->name('agreements.show');
    Route::get('/agreements/{agreement}/edit', [AgreementAdminController::class, 'edit'])->name('agreements.edit');
    Route::put('/agreements/{agreement}', [AgreementAdminController::class, 'update'])->name('agreements.update');
    Route::delete('/agreements/{agreement}/draft', [AgreementAdminController::class, 'discardDraft'])->name('agreements.draft.destroy');
});

Route::middleware('permission:photos.review')->group(function () {
    Route::get('/photos', [PhotoReviewController::class, 'index'])->name('photos');
    Route::patch('/photos/{photo}/approve', [PhotoReviewController::class, 'approve'])->name('photos.approve');
    Route::patch('/photos/{photo}/reject', [PhotoReviewController::class, 'reject'])->name('photos.reject');
});

Route::middleware('permission:affiliations.manage')->group(function () {
    Route::get('/affiliation-types', [AffiliationTypeController::class, 'index'])->name('affiliation-types');
    Route::get('/affiliation-types/create', [AffiliationTypeController::class, 'create'])->name('affiliation-types.create');
    Route::post('/affiliation-types', [AffiliationTypeController::class, 'store'])->name('affiliation-types.store');
    Route::get('/affiliation-types/{affiliationType}/edit', [AffiliationTypeController::class, 'edit'])->name('affiliation-types.edit');
    Route::put('/affiliation-types/{affiliationType}', [AffiliationTypeController::class, 'update'])->name('affiliation-types.update');
    Route::delete('/affiliation-types/{affiliationType}', [AffiliationTypeController::class, 'destroy'])->name('affiliation-types.destroy');
});

Route::middleware('permission:roles.manage')->group(function () {
    Route::get('/roles', [RoleController::class, 'index'])->name('roles');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
});

Route::get('/users', [UserAdminController::class, 'users'])->name('users');
Route::get('/users/{user_id}', [ProfileAdminController::class, 'show'])->name('users.show');
Route::post('/users/{user_id}', [ProfileAdminController::class, 'update']);

Route::middleware('role:1')->group(function () {
    Route::delete('/users/{user_id}', [UserAdminController::class, 'removeUser'])->name('users.destroy');
    Route::patch('/users/{user_id}/owner-role', [UserAdminController::class, 'setOwnerRole'])->name('users.owner-role');
    Route::patch('/users/{user_id}/manager-role', [UserAdminController::class, 'setManagerRole'])->name('users.manager-role');
    Route::patch('/users/{user_id}/user-role', [UserAdminController::class, 'setUserRole'])->name('users.user-role');
    Route::post('/users/{user_id}/tc-kimlik', [UserAdminController::class, 'tcKimlikDogrula'])->name('users.tc-kimlik');

    Route::get('/process-logs', [ProcessLogController::class, 'getList'])->name('process-logs');
});
