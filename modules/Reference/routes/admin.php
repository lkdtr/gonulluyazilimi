<?php

use Illuminate\Support\Facades\Route;
use Modules\Reference\Http\Controllers\Admin\ReferenceRequestController;

Route::get('/reference-requests', [ReferenceRequestController::class, 'getList'])->name('reference-requests');
