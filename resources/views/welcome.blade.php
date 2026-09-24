@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h3 class="card-title">{{ $organization->get('home_title', $title ?? $organization->name()) }}</h3></div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($content = $organization->get('home_content'))
                        {{-- Written in the admin panel (Kurum ayarları), sanitized on save. --}}
                        <div class="home-content">{!! $content !!}</div>
                    @else
                        @if ($organization->logoUrl())
                            <div style="text-align: center;">
                                <img src="{{ $organization->logoUrl() }}" alt="{{ $organization->name() }}" style="width:100%; max-width: 450px;">
                            </div>
                        @endif

                        @moduleSlot('welcome.intro')
                    @endif

                    @moduleSlot('welcome.sections')

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
