<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;

use Illuminate\Http\Request;

class AgreementController extends Controller
{
    public function userAgreement(Request $request) {
        $iframe = $request->has("iframe")?true:false;
        $title = "Kişisel Verilerin Korunması ve İşlenmesi Politikası";
        $content = View::make('agreements.userAgreement');
        $link = "/user-agreement";

        if($iframe) {
            return view('agreement-iframe', ["title" => $title, "content" => $content]);
        }
        else {
            return view('agreement', ["title" => $title, "content_block" => $content, "link" => $link]);
        }
    }
}
