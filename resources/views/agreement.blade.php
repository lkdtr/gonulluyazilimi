@extends('layouts.app')

@section('title')
{{$title}}
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{$title}}</h3>
                    <div class="card-actions text-secondary small">
                        Sürüm {{ $version->version }} · {{ $version->published_at->format('d.m.Y') }}
                        @if ($version->isNot($agreement->currentVersion))
                            <span class="badge bg-yellow-lt ms-1">Eski sürüm</span>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    {!! $content !!}
                </div>

                @if ($history->count() > 1)
                    <div class="card-footer small">
                        <span class="text-secondary">Sürümler:</span>
                        @foreach ($history as $item)
                            @if ($item->is($version))
                                <strong class="ms-2">{{ $item->version }}</strong>
                            @else
                                <a class="ms-2" href="{{ route('agreements.show', $agreement->key) }}?version={{ $item->version }}">{{ $item->version }}</a>
                            @endif
                            <span class="text-secondary">({{ $item->published_at->format('d.m.Y') }})</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
