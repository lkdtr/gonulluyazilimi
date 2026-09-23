@extends('layouts.admin')

@section('content')
<div class="container-xl">
    <div class="page-header mb-4">
        <h2 class="page-title">Genel Bakış</h2>
    </div>

    <div class="row row-cards">
        @foreach ($groups as $group)
            @continue($group['items'][0]['route'] === 'admin.dashboard')
            <div class="col-sm-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <span class="avatar bg-primary-lt me-3"><i class="ti ti-{{ $group['icon'] }} icon"></i></span>
                            <h3 class="card-title mb-0">{{ __($group['label']) }}</h3>
                        </div>
                        <div class="list-group list-group-flush">
                            @foreach ($group['items'] as $item)
                                <a class="list-group-item list-group-item-action d-flex align-items-center px-0" href="{{ route($item['route']) }}">
                                    <span class="flex-fill">{{ __($item['label']) }}</span>
                                    <i class="ti ti-chevron-right icon text-secondary"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
