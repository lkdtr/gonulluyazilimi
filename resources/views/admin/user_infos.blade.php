@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ trans("panel.user_infos_title") }}</div>
                <div class="card-body">

                    @if (session('status'))
                        <div class="alert alert-success d-flex" role="alert">
                            <svg style="height: 20px;width: 20px;" class="bi flex-shrink-0 me-2" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                            <div>{{ session('status') }}</div>
                        </div>
                    @endif

                    <form method="POST" action="">
                    @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">
                                {{ trans("auth.name") }}
                            </label>
                            <div class="col-md-6">
                                <span class="form-control">{{$user->name}}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="surname" class="col-md-4 col-form-label text-md-end">
                                {{ trans("auth.surname") }}
                            </label>
                            <div class="col-md-6">
                                <span class="form-control">{{$user->surname}}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="national_id" class="col-md-4 col-form-label text-md-end">
                                {{ trans("auth.national_id") }}
                            </label>
                            <div class="col-md-6">
                                <span class="form-control">{{$user->national_id}}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">
                                {{ trans("auth.email") }}
                            </label>
                            <div class="col-md-6">
                                <span class="form-control">{{$user->email}}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="phone_number" class="col-md-4 col-form-label text-md-end">
                                {{ trans("auth.phone_number") }}
                            </label>
                            <div class="col-md-6">
                                <span class="form-control">{{$user->phone_number}}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="birthday" class="col-md-4 col-form-label text-md-end">
                                {{ trans("auth.birthday") }}
                            </label>
                            <div class="col-md-6">
                                <span class="form-control">{{date("d/m/Y", strtotime($user->birthday))}}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="city" class="col-md-4 col-form-label text-md-end">
                                {{ trans("auth.city") }}
                            </label>
                            <div class="col-md-6">
                                <select id="city" name="city" class="form-control form-select" style="width: 100%; border: 1px solid #004153;">
                                    <option value="0" {{ $user->city_id == 0 ? "selected" : "" }}>{{ trans("auth.please_select") }}</option>
                                    @foreach($cities as $city)
                                    <option value="{{$city->city_plate_no}}" {{ $user->city_id == $city->city_plate_no ? "selected" : "" }}>{{$city->city_name}}</option>
                                    @endforeach
                                    <option value="999" {{ $user->city_id == 999 ? "selected" : "" }}>{{ trans("auth.out_of_turkey") }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="separator bottom"><br></div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button id="save_button" type="submit" class="btn btn-lg btn-primary">
                                    {{ trans("panel.save") }}
                                </button>
                            </div>
                        </div>

                    </form>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection
