@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ trans("panel.user_infos_title") }}</div>
                <div>
                    @foreach ($user as $key => $value)
                        <p>{{$key}}:{{$value}}</p>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
