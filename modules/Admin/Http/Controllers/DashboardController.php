<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Menu;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Menu $menu)
    {
        return view('admin::dashboard', ['groups' => $menu->groups('admin', Auth::user())]);
    }
}
