<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Redirect;

use App\Events\ProfileUpdated;
use App\Models\User;
use App\Models\Cities;

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
        return $this->showProfile(Auth::user());
    }

    public function postMyInfos(Request $request) {
        return $this->saveProfile($request, Auth::user());
    }

    protected function showProfile(User $user, string $layout = 'layouts.app') {

        $cities = Cities::where("status", "1")->get();

        $contact = $user->contact;

        return view('profile.edit', [
            "user" => $user,
            "cities" => $cities,
            "layout" => $layout,
            // Managers see the approved photo; only the person uploads or deletes.
            "photo" => [
                'approved' => $contact?->approvedPhoto,
                'upload' => $contact?->latestPhotoUpload,
                'editable' => $user->is(Auth::user()),
            ],
            "fields" => $contact ? [
                'fields' => app(\App\Support\CustomFields::class)->fieldsFor($contact, member: $user->is(Auth::user())),
                'values' => app(\App\Support\CustomFields::class)->values($contact),
                'editable' => $user->is(Auth::user()),
            ] : null,
            "acceptances" => \App\Models\AgreementAcceptance::with('version.agreement.currentVersion')
                ->where(fn ($query) => $query->where('user_id', $user->id)->when($contact, fn ($query) => $query->orWhere('contact_id', $contact->id)))
                ->latest('accepted_at')->latest('id')->get(),
            "consents" => [
                'current' => $contact ? app(\App\Support\Consents::class)->current($contact) : array_fill_keys(array_keys(\App\Support\Consents::CHANNELS), null),
                'editable' => $user->is(Auth::user()),
            ],
            // Only on the person's own profile.
            "deletion" => $user->is(Auth::user()) ? [
                'pending' => $contact ? \App\Models\DataDeletionRequest::pending()->where('contact_id', $contact->id)->first() : null,
            ] : null,
        ]);
    }

    protected function saveProfile(Request $request, User $user) {

        $validator = $request->validate([
            'city' => ['required', 'integer', 'exists:cities,id'],
            'birthday' => ['nullable', 'date', 'required_with:national_id'],
            'national_id' => ['nullable', 'digits:11'],
        ]);

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

        if ($request->has('lkd_user_id') && in_array(Auth::user()->accessLevel(), [1, 2], true)) {
            $user->lkd_user_id = $request->get("lkd_user_id");
        }

        $user->save();

        $event = new ProfileUpdated($user);
        event($event);

        return $event->redirect ?? Redirect::back()->with("status", trans("panel.successfully_saved"));
    }


}
