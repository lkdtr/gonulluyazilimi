<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Redirect;

use App\Models\User;
use App\Models\Cities;
use App\Models\LegalRepresentation;
use App\Models\LegalRepresentationVolunteer;

use BahriCanli\TcKimlik;
use Carbon\Carbon;

class UserController extends Controller
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

    public function getMyInfos() {
        $user_id = Auth::id();
        return $this->getUserInfos($user_id);
    }

    public function getUserInfos($user_id) {

        if ( (Auth::user()->role!=1 ) && (Auth::user()->role!=2 ) && (Auth::id()!=$user_id) ) {
            return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        $user = User::findOrFail($user_id);
        $cities = Cities::where("status", "1")->get();

        return view('admin.user_infos', ["user" => $user, "cities" => $cities]);
    }


    public function postMyInfos(Request $request) {
        $user_id = Auth::id();
        return $this->postUserInfos($request, $user_id);
    }

    public function postUserInfos(Request $request, $user_id) {

        if ( (Auth::user()->role!=1 ) && (Auth::user()->role!=2 ) && (Auth::id()!=$user_id) ) {
            return Redirect::to(secure_url('/users'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        $validator = $request->validate([
            'city' => ['required', 'integer', 'exists:cities,id'],
            'birthday' => ['nullable', 'date', 'required_with:national_id'],
            'national_id' => ['nullable', 'digits:11'],
        ]);

        $user = User::findOrFail($user_id);
        $user->name = $this->tr_ucwords($user->name);
        $user->surname = $this->tr_ucwords($user->surname);
        $user->city_id = $request->get("city");

        if($request->has("birthday")) {
            $user->birthday = Carbon::parse($request->get("birthday"))->format("Y-m-d");
        }

        if($request->has("national_id")) {
            $user->national_id = $request->get("national_id");

            $birty_year = date("Y", strtotime($user->birthday));

            $data = [
                'tcno'          => $user->national_id,
                'isim'          => $user->name,
                'soyisim'       => $user->surname,
                'dogumyili'     => $birty_year,
            ];

            if (!TcKimlik::validate($data)) {
                return back()->withErrors(["national_id" => "TC Kimlik Numarası vermiş olduğunuz kimlik bilgilerinizle eşleşmiyor"])->withInput();
            }
        }

        if ($request->has('lkd_user_id') && in_array(Auth::user()->role, [1, 2], true)) {
            $user->lkd_user_id = $request->get("lkd_user_id");
        }

        $user->save();

        $representation = LegalRepresentation::where('city', Cities::find($user->city_id)?->city_name)->where('status', true)->first();
        if ($representation && ! LegalRepresentationVolunteer::where('legal_representation_id', $representation->id)->where('user_id', $user->id)->exists()) {
            return Redirect::route('representations.consent', $representation);
        }

        return Redirect::back()->with("status", trans("panel.successfully_saved"));
    }


}
