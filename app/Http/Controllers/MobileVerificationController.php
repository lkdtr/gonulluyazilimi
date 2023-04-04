<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Notification;

use App\Notifications\MobileVerification;
use App\Models\ContactPermissions;

class MobileVerificationController extends Controller
{

    public function postPhoneNumberVerificationRequest(Request $request) {

        $validator = $request->validate([
            'phone_number' => 'required',
        ]);

        $phone_number = $request->get("phone_number");
        $verification_code = $this->generateRandomString(6);

        $contactPermission = ContactPermissions::where("value_type", "phone_number")->where("value", $phone_number)->first();
        if($contactPermission==null) $contactPermission = new ContactPermissions();

        $smsObject = new \stdClass();
        $smsObject->phone_number = $phone_number;
        $smsObject->verification_code =$verification_code;

        Notification::send($smsObject, new MobileVerification());

        $contactPermission->value = $phone_number;
        $contactPermission->value_type = "phone_number";
        $contactPermission->verification_code = $verification_code;
        $contactPermission->status = 1;
        $contactPermission->save();

        return $this->output("json", ['status' => true]);
    }

    public function postPhoneNumberVerification(Request $request) {

        $validator = $request->validate([
            'phone_number' => 'required',
            'validation' => 'required',
        ]);

        $phone_number = $request->get("phone_number");
        $validation = $request->get("validation");

        $contactPermission = ContactPermissions::where("value_type", "phone_number")->where("value", $phone_number)->first();
        if($contactPermission==null) {
            return $this->output("json", ["status" => false, "message" => "null"]);
        }
        if($contactPermission->verification_code != $validation) {
            return $this->output("json", ["status" => false, "message" => "Code failed"]);
        }

        $contactPermission->verified = true;
        $contactPermission->save();

        return $this->output("json", ['status' => true]);
    }

}
