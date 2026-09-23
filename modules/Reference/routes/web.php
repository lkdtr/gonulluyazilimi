<?php

use Illuminate\Support\Facades\Route;
use Modules\Reference\Http\Controllers\ReferenceController;

Route::get('/create-reference-request', [ReferenceController::class, 'getCreate'])->name('create-reference-request');
Route::post('/create-reference-request', [ReferenceController::class, 'postCreate']);

// Old address of the page that moved to the admin panel.
Route::permanentRedirect('/reference-requests', '/admin/reference-requests');
