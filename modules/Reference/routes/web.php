<?php

use Illuminate\Support\Facades\Route;
use Modules\Reference\Http\Controllers\ReferenceController;

Route::get('/reference-requests', [ReferenceController::class, 'getList'])->name('reference-requests');
Route::get('/create-reference-request', [ReferenceController::class, 'getCreate'])->name('create-reference-request');
Route::post('/create-reference-request', [ReferenceController::class, 'postCreate']);
