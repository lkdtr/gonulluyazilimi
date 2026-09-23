@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row g-3">
        @foreach ($groups as $group)
            <div class="col-md-4">
                <div class="card h-100 border-secondary">
                    <div class="card-header text-white bg-secondary">{{ __($group['label']) }}</div>
                    <div class="list-group list-group-flush">
                        @foreach ($group['items'] as $item)
                            <a class="list-group-item list-group-item-action" href="{{ route($item['route']) }}">{{ __($item['label']) }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
