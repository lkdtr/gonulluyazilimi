<?php

use Illuminate\Support\Facades\Route;
use Modules\IdCard\Http\Controllers\Admin\IssuedCardController;
use Modules\IdCard\Http\Controllers\Admin\TemplateController;

Route::middleware('permission:id-cards.manage')->prefix('id-cards')->name('id-cards')->group(function () {
    Route::get('/', [TemplateController::class, 'index']);
    Route::get('/templates/create', [TemplateController::class, 'create'])->name('.templates.create');
    Route::post('/templates', [TemplateController::class, 'store'])->name('.templates.store');
    Route::get('/templates/{template}/edit', [TemplateController::class, 'edit'])->name('.templates.edit');
    Route::put('/templates/{template}', [TemplateController::class, 'update'])->name('.templates.update');
    Route::delete('/templates/{template}', [TemplateController::class, 'destroy'])->name('.templates.destroy');

    Route::get('/issued', [IssuedCardController::class, 'index'])->name('.issued');
    Route::patch('/issued/{card}/revoke', [IssuedCardController::class, 'revoke'])->name('.revoke');
    Route::patch('/issued/{card}/restore', [IssuedCardController::class, 'restore'])->name('.restore');
    Route::patch('/issued/{card}/renew', [IssuedCardController::class, 'renew'])->name('.renew');
});
