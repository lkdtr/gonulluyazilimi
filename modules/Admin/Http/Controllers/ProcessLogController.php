<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProcessLogs;

class ProcessLogController extends Controller
{
    public function getList() {

        $processLogs = ProcessLogs::all();

        return view('admin::process_logs', ["processLogs" => $processLogs]);
    }

}

