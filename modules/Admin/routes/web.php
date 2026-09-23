<?php

use Illuminate\Support\Facades\Route;

// Old addresses of pages that moved to the admin panel.
Route::permanentRedirect('/users', '/admin/users');
Route::permanentRedirect('/process-logs', '/admin/process-logs');
Route::get('/user-infos/{user_id}', fn ($user_id) => redirect()->route('admin.users.show', $user_id, 301));
