@extends('layouts.app')

@section('title')
{{$title}}
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-secondary">
                <div class="card-header text-white bg-secondary">{{$title}}</div>

                <div class="card-body">
                    {!! $content !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
