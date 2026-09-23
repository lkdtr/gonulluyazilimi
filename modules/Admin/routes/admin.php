<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\DashboardController;
use Modules\Admin\Http\Controllers\ProcessLogController;
use Modules\Admin\Http\Controllers\ProfileAdminController;
use Modules\Admin\Http\Controllers\UserAdminController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

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
