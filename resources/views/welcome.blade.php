@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-secondary">
                <div class="card-header text-white bg-secondary">{{ $title ?? 'Linux Kullanıcıları Derneği' }}</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div style="text-align: center;">
                        <img src="/images/lkd-gonullusu.png?v3" alt="Linux Kullanıcıları Derneği Gönüllüsü" style="width:100%; max-width: 450px;">
                    </div>

                    @moduleSlot('welcome.intro')

                    @moduleSlot('welcome.sections')

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
