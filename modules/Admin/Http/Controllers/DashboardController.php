<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Dashboard;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Dashboard $dashboard)
    {
        return view('admin::dashboard', [
            'stats' => $dashboard->stats(Auth::user()),
            'charts' => $dashboard->charts(Auth::user()),
        ]);
    }
}
