@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ trans("panel.user_infos_title") }}</div>
                <div>

                    <p>name: {{$user->name}}</p>
                    <p>surname: {{$user->surname}}</p>

                    <p>national_id: {{$user->national_id}}</p>
                    <p>email: {{$user->email}}</p>
                    <p>phone_number: {{$user->phone_number}}</p>

                    <p>birthday: {{$user->birthday}}</p>
                    <p>city_id: {{$user->city_id}}</p>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
