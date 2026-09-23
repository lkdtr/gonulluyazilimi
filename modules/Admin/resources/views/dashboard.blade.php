@extends('layouts.admin')

@section('content')
<style>
    .dashboard-chart .chart-grid { stroke: var(--tblr-border-color); stroke-width: 1; }
    .dashboard-chart .chart-axis { stroke: var(--tblr-border-color-dark, var(--tblr-border-color)); stroke-width: 1; }
    .dashboard-chart .chart-tick { fill: var(--tblr-secondary); font-size: 14px; }
    .dashboard-chart .chart-value { fill: var(--tblr-body-color); font-size: 14px; font-weight: 600; }
    .dashboard-chart .chart-bar { fill: var(--tblr-primary); }
    .dashboard-chart .chart-line { fill: none; stroke: var(--tblr-primary); stroke-width: 2; stroke-linejoin: round; }
    .dashboard-chart .chart-area { fill: var(--tblr-primary); opacity: .08; }
    .dashboard-chart .chart-point { fill: var(--tblr-primary); stroke: var(--tblr-bg-surface); stroke-width: 2; opacity: 0; }
    .dashboard-chart .chart-hit { fill: transparent; }
    .dashboard-chart .chart-crosshair { stroke: var(--tblr-secondary); stroke-dasharray: 3 3; opacity: 0; }
    .dashboard-chart .chart-hover-value { opacity: 0; }
    .dashboard-chart .chart-hover:hover .chart-point,
    .dashboard-chart .chart-hover:hover .chart-crosshair,
    .dashboard-chart .chart-hover:hover .chart-hover-value { opacity: 1; }
    .dashboard-chart .chart-hover:hover .chart-bar { opacity: .8; }
</style>

<div class="container-xl">
    <div class="page-header mb-4">
        <h2 class="page-title">Genel Bakış</h2>
    </div>

    @if ($stats)
        <div class="row row-cards mb-4">
            @foreach ($stats as $stat)
                <div class="col-6 col-lg-3">
                    <div class="card card-sm h-100 {{ $stat['route'] ? 'card-link' : '' }}">
                        @if ($stat['route'])
                            <a href="{{ route($stat['route']) }}" class="stretched-link" aria-label="{{ __($stat['label']) }}"></a>
                        @endif
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto d-none d-sm-block">
                                    <span class="avatar bg-primary-lt"><i class="ti ti-{{ $stat['icon'] }} icon"></i></span>
                                </div>
                                <div class="col">
                                    <div class="h1 mb-0">{{ number_format($stat['value'], 0, ',', '.') }}</div>
                                    <div class="text-secondary">{{ __($stat['label']) }}</div>
                                    @if ($stat['hint'])
                                        <div class="small text-secondary opacity-75">{{ $stat['hint'] }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if ($charts)
        <div class="row row-cards">
            @foreach ($charts as $chart)
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <div>
                                <h3 class="card-title">{{ $chart['title'] }}</h3>
                                @if ($chart['description'])
                                    <div class="card-subtitle">{{ $chart['description'] }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            @if (array_sum($chart['data']) > 0)
                                @include('admin::partials.chart', ['chart' => $chart])
                            @else
                                <div class="text-center text-secondary py-5">
                                    <i class="ti ti-chart-bar-off icon mb-2"></i>
                                    <div>Son 12 ayda kayıt yok</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
