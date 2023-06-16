<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Redirect;

use App\Models\User;
use App\Models\ReferenceRequests;

use Epigra\TcKimlik;
use Carbon\Carbon;

class ReferenceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {

            $this->middleware('auth');

            if(!Auth::check() ) {
                return redirect('/login')->with('redirect', URL::full() );
            }

            return $next($request);
        });
    }

    public function getList() {

        if (Auth::user()->role!=1 ) {
            return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        $referenceRequests = ReferenceRequests::where("status", 1)->get();

        return view('admin.reference_requests', ["referenceRequests" => $referenceRequests]);

    }

    public function getCreate() {

        $user_id = Auth::id();
        $user = User::where("id", $user_id)->first();
        $user->birthday = date("d-m-Y", strtotime($user->birthday));

        $referenceRequest = ReferenceRequests::where("user_id", $user_id)->first();

        return view('user.create_reference_request', ["user" => $user, "referenceRequest" => $referenceRequest]);
    }

    public function postCreate(Request $request) {

        $validator = $request->validate([
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'surname' => ['required', 'string', 'max:255', 'min:2'],
            'national_id' => ['required', 'string', 'max:11', 'tckimlik'],
            'birthday' => ['required'],
            'agreement' => ['required']
        ]);

        $name = $request->get("name");
        $surname = $request->get("surname");
        $national_id = $request->get("national_id");
        $birthday = $request->get("birthday");

        $birty_year = date("Y", strtotime($birthday));

        $data = [
            'tcno'          => $national_id,
            'isim'          => $name,
            'soyisim'       => $surname,
            'dogumyili'     => $birty_year,
        ];

        if (!TcKimlik::validate($data)) {
            return back()->withErrors(["national_id" => "TC Kimlik Numarası vermiş olduğunuz kimlik bilgilerinizle eşleşmiyor"])->withInput();
        }

        $user_id = Auth::id();
        $user = User::where("id", $user_id)->first();
        $user->birthday = Carbon::parse($birthday);
        $user->name = $this->tr_ucwords($name);
        $user->surname = $this->tr_ucwords($surname);
        $user->save();

        $referenceRequest = ReferenceRequests::where("user_id", $user_id)->first();
        if($referenceRequest==null) {
            $referenceRequest = new ReferenceRequests();
            $referenceRequest->user_id = $user_id;
            $referenceRequest->demand_met = 2;
            $referenceRequest->created_by = $user_id;
            if($referenceRequest->save()) {

                $this->set_log("create", $user->name." ".$user->surname. " referans talebi kaydedildi");

                return Redirect::to(secure_url('/home'))->with("success-status", trans("panel.reference_requests_success"));
            }
        }

        return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.reference_requests_failed"));
    }

}
