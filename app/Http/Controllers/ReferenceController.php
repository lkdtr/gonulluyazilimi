<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Redirect;

use App\Models\User;
use App\Models\ReferenceRequests;

use BahriCanli\TcKimlik;
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

        $name = $this->normalizeIdentityText($request->get("name"));
        $surname = $this->normalizeIdentityText($request->get("surname"));
        $national_id = preg_replace('/\D+/', '', (string) $request->get("national_id"));
        $birthday = $this->parseIdentityBirthday($request->get("birthday"));

        $data = $this->buildTcKimlikData($national_id, $name, $surname, $birthday->format("Y-m-d"));

        if (!$this->validateTcKimlikIdentity($data)) {
            return back()->withErrors(["national_id" => "TC Kimlik Numarası vermiş olduğunuz kimlik bilgilerinizle eşleşmiyor"])->withInput();
        }

        $user_id = Auth::id();
        $user = User::where("id", $user_id)->first();
        $user->birthday = $birthday->format("Y-m-d");
        $user->name = $this->tr_ucwords($name);
        $user->surname = $this->tr_ucwords($surname);
        $user->national_id = $national_id;
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

    private function normalizeIdentityText($value) {
        $value = trim((string) $value);
        $value = preg_replace('/\s+/u', ' ', $value);

        return $this->tr_ucwords($value);
    }

    private function parseIdentityBirthday($birthday) {
        $birthday = trim((string) $birthday);

        foreach (['d-m-Y', 'd.m.Y', 'd/m/Y', 'Y-m-d'] as $format) {
            try {
                return Carbon::createFromFormat($format, $birthday);
            } catch (\Exception $e) {
            }
        }

        return Carbon::parse($birthday);
    }

    private function buildTcKimlikData($nationalId, $name, $surname, $birthday) {
        $birthday = $this->parseIdentityBirthday($birthday);

        return [
            'tcno'          => preg_replace('/\D+/', '', (string) $nationalId),
            'isim'          => $this->normalizeIdentityText($name),
            'soyisim'       => $this->normalizeIdentityText($surname),
            'dogumyili'     => $birthday->format('Y'),
        ];
    }

    private function validateTcKimlikIdentity(array $data) {
        if (!TcKimlik::verify($data)) {
            return false;
        }

        $payload = [
            'TCKimlikNo' => $data['tcno'],
            'Ad' => TcKimlik::trUppercase($data['isim']),
            'Soyad' => TcKimlik::trUppercase($data['soyisim']),
            'DogumYili' => $data['dogumyili'],
        ];

        $fields = array_reduce(
            array_chunk($payload, 1, true),
            function ($result, $item) {
                return $result . '<' . key($item) . '>' . current($item) . '</' . key($item) . '>' . PHP_EOL;
            },
            ''
        );

        $postData = '<?xml version="1.0" encoding="utf-8"?>' .
            '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">' .
            '<soap:Body>' .
            '<TCKimlikNoDogrula xmlns="http://tckimlik.linux.org.tr/WS">' .
            $fields .
            '</TCKimlikNoDogrula>' .
            '</soap:Body>' .
            '</soap:Envelope>';

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://tckimlik.linux.org.tr/Service/KPSPublic.asmx',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => [
                'POST /Service/KPSPublic.asmx HTTP/1.1',
                'Host: tckimlik.linux.org.tr',
                'Content-Type: text/xml; charset=utf-8',
                'SOAPAction: "http://tckimlik.linux.org.tr/WS/TCKimlikNoDogrula"',
                'Content-Length: ' . strlen($postData),
            ],
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return preg_match('/<TCKimlikNoDogrulaResult>\s*true\s*<\/TCKimlikNoDogrulaResult>/i', (string) $response) === 1;
    }

}
