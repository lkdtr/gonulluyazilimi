<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmailRedirectsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function get()
    {

    }

    public function post()
    {

    }
}
