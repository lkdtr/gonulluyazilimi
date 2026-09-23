<?php

namespace Modules\Reference\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Modules\Reference\Models\ReferenceRequests;

class ReferenceRequestController extends Controller
{
    public function getList() {

        if (Auth::user()->role!=1 ) {
            return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        $referenceRequests = ReferenceRequests::where("status", 1)->get();

        return view('reference::admin.index', ["referenceRequests" => $referenceRequests]);

    }
}
