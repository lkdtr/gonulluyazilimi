@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            @if (session('danger-status'))
                <div class="alert alert-danger d-flex" role="alert">
                    <svg style="height: 20px;width: 20px;" class="bi flex-shrink-0 me-2" role="img" aria-label="Info:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                    <div>{{ session('danger-status') }}</div>
                </div>
            @endif

            @if (session('success-status'))
                <div class="alert alert-success d-flex" role="alert">
                    <svg style="height: 20px;width: 20px;" class="bi flex-shrink-0 me-2" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                    <div>{{ session('success-status') }}</div>
                </div>
            @endif

            @moduleSlot('home.top')

            @if (session('status'))
                <div class="alert alert-success d-flex" role="alert">
                    <svg style="height: 20px;width: 20px;" class="bi flex-shrink-0 me-2" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            @moduleSlot('home.main')

        </div>
    </div>
</div>
@endsection
