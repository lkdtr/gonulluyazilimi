<?php

use Illuminate\Support\Facades\Route;

// Old addresses of pages that moved to the admin panel.
Route::permanentRedirect('/announcements', '/admin/announcements');
Route::permanentRedirect('/new-announcement', '/admin/announcements/create');
Route::get('/edit-announcement/{id}', fn ($id) => redirect()->route('admin.announcements.edit', $id, 301));
