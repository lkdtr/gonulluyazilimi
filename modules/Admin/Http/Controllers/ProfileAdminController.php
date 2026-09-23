<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Profile form of any user, opened from the admin user list.
 */
class ProfileAdminController extends UserController
{
    public function show($user_id)
    {
        return $this->showProfile(User::findOrFail($user_id), 'layouts.admin');
    }

    public function update(Request $request, $user_id)
    {
        return $this->saveProfile($request, User::findOrFail($user_id));
    }
}
