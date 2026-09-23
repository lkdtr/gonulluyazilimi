@extends('layouts.app')

@section('title')
{{$title}}
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h3 class="card-title">{{$title}}</h3></div>

                <div class="card-body">
                    {!! $content !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
