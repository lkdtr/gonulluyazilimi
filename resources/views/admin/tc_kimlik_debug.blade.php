@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-secondary">
                <div class="card-header text-white bg-secondary">TC Kimlik Doğrulama Debug</div>
                <div class="card-body">
                    <h5>Kullanıcı Bilgileri (Veritabanı)</h5>
                    <pre>{{ json_encode([
                        'id' => $user->id,
                        'name' => $user->name,
                        'surname' => $user->surname,
                        'national_id' => $user->national_id,
                        'birthday' => $user->birthday,
                        'email' => $user->email,
                    ], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>

                    <h5>Doğrulama Servisine Giden İstek</h5>
                    <pre>{{ json_encode($request_data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>

                    <h5>Doğrulama Sonucu</h5>
                    <pre>{{ json_encode([
                        'result' => $result,
                        'error_message' => $error_message,
                    ], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
