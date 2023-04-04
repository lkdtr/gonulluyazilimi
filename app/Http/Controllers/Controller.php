<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Support\Facades\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function generateRandomString($length = 5) {

        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {

            $randomString .= $characters[rand(0, $charactersLength - 1)];

        }

        return $randomString;
    }

    public function output($outputType, $result) {

        try {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, HEAD, OPTIONS');
            header('Access-Control-Allow-Headers: X-Requested-With, Origin, X-Csrftoken, Content-Type, Accept');
        }
        catch(Exception $e) {
            Log::info($e->getMessage());
        }

        switch ($outputType) {
            case 'json':
                  return Response::json($result);
                  break;
            case 'jsonp':
                  return Response::json($result)->setCallback(Input::get('callback'));
                  break;
            default:
                  return Response::json($result);
                  break;
        }

    }

}
