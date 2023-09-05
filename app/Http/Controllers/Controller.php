<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

use App\Models\ProcessLogs;

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

    public function tr_strtolower($str) {
        $inArray = ["Ö", "Ç", "Ş", "Ğ", "Ü", "I", "İ"];
        $outArray = ["ö", "ç", "ş", "ğ", "ü", "ı", "i"];
        return strtolower(str_replace($inArray, $outArray, $str));
    }

    public function tr_strtoupper($str) {
        $inArray = ["ö", "ç", "ş", "ğ", "ü", "ı", "i"];
        $outArray = ["Ö", "Ç", "Ş", "Ğ", "Ü", "I", "İ"];
        return strtoupper(str_replace($inArray, $outArray, $str));
    }

    public function tr_ucwords($str) {
        $str = $this->tr_strtolower($str);
        $strArray = explode(" ", $str);
        $newStr = "";
        foreach ($strArray as $string) {
            $newStr .=  $this->tr_strtoupper(mb_substr($string, 0, 1, "UTF-8"), "UTF-8").mb_substr($string, 1, 100, "UTF-8")." ";
        }
        return trim($newStr);
    }

    public function getClientIp() {

        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }

    public function set_log($process_type="other", $process="") {

        $user_id = Auth::id();

        $processLog = new ProcessLogs();
        $processLog->process_by = $user_id;
        $processLog->process_type = $process_type;
        $processLog->process = $process;
        $processLog->request_ip = $this->getClientIp();
        if($processLog->save()) {
            return true;
        }
        else {
            return false;
        }

    }

}
