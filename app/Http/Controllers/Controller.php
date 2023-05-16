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

    public function slug($string, $spaceRepl = "-") {


        $string = str_replace("&", "and", $string);// Replace "&" char with "and"
        $string = str_replace(  array("ö", "ç", "ş", "ğ", "ü", "ı", "İ", "Ş", "Ç", "Ö", "Ü", "Ğ", "I"),
                                array("o", "c", "s", "g", "u", "i", "i", "s", "c", "o", "u", "g", "i"),
                                $string
                            );
        $string = preg_replace("/[^a-zA-Z0-9 _-]/", "", $string);// Delete any chars but letters, numbers, spaces and _, -
        $string = strtolower($string);// Optional: Make the string lowercase
        $string = preg_replace("/[ ]+/", " ", $string);// Optional: Delete double spaces
        $string = str_replace(" ", $spaceRepl, $string);// Replace spaces with replacement

        return $string;

    }

}
