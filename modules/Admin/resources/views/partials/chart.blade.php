{{-- Server-side SVG chart: $chart = ['title', 'type' => bar|line, 'data' => [label => int], 'description'] --}}
@php
    $data = $chart['data'];
    $labels = array_keys($data);
    $values = array_values($data);
    $count = max(count($values), 1);

    $width = 560;
    $height = 240;
    $left = 44;
    $right = 12;
    $top = 16;
    $bottom = 30;
    $plotWidth = $width - $left - $right;
    $plotHeight = $height - $top - $bottom;

    // Round the axis up to 1, 2, 2.5 or 5 times a power of ten, split in four steps.
    $max = max(max($values ?: [0]), 1);
    $magnitude = 10 ** floor(log10($max / 4));
    $tick = $magnitude;
    foreach ([1, 2, 2.5, 5, 10] as $factor) {
        $tick = $factor * $magnitude;
        if ($tick * 4 >= $max) {
            break;
        }
    }
    $tick = max((int) ceil($tick), 1);
    $axisMax = $tick * 4;

    $step = $plotWidth / $count;
    $x = fn (int $i) => $left + $step * ($i + 0.5);
    $y = fn (int $value) => $top + $plotHeight - $plotHeight * $value / $axisMax;
    $baseline = $top + $plotHeight;
    $labelEvery = $count > 8 ? 2 : 1;
    $format = fn (int $value) => number_format($value, 0, ',', '.');
@endphp

<figure class="dashboard-chart mb-0">
    <svg viewBox="0 0 {{ $width }} {{ $height }}" width="100%" role="img" aria-label="{{ $chart['title'] }}">
        @for ($i = 0; $i <= 4; $i++)
            @php $gy = $y($tick * $i); @endphp
            <line x1="{{ $left }}" x2="{{ $width - $right }}" y1="{{ $gy }}" y2="{{ $gy }}" class="{{ $i === 0 ? 'chart-axis' : 'chart-grid' }}"/>
            <text x="{{ $left - 8 }}" y="{{ $gy }}" dy="0.32em" text-anchor="end" class="chart-tick">{{ $format($tick * $i) }}</text>
        @endfor

        @foreach ($labels as $i => $label)
            @if (($count - 1 - $i) % $labelEvery === 0)
                <text x="{{ $x($i) }}" y="{{ $height - 8 }}" text-anchor="middle" class="chart-tick">{{ $label }}</text>
            @endif
        @endforeach

        @if ($chart['type'] === 'line')
            @php
                $points = collect($values)->map(fn ($value, $i) => round($x($i), 1).','.round($y($value), 1));
            @endphp
            <polygon class="chart-area" points="{{ round($x(0), 1) }},{{ $baseline }} {{ $points->implode(' ') }} {{ round($x($count - 1), 1) }},{{ $baseline }}"/>
            <polyline class="chart-line" points="{{ $points->implode(' ') }}"/>
            @foreach ($values as $i => $value)
                <g class="chart-hover">
                    <rect x="{{ $x($i) - $step / 2 }}" y="{{ $top }}" width="{{ $step }}" height="{{ $plotHeight }}" class="chart-hit"/>
                    <line x1="{{ $x($i) }}" x2="{{ $x($i) }}" y1="{{ $top }}" y2="{{ $baseline }}" class="chart-crosshair"/>
                    <circle cx="{{ $x($i) }}" cy="{{ $y($value) }}" r="4" class="chart-point"/>
                    <title>{{ $labels[$i] }}: {{ $format($value) }}</title>
                </g>
            @endforeach
            <text x="{{ $x($count - 1) }}" y="{{ $y(end($values)) - 10 }}" text-anchor="end" class="chart-value">{{ $format(end($values)) }}</text>
        @else
            @php $barWidth = min($step * 0.6, 32); @endphp
            @foreach ($values as $i => $value)
                @php
                    $barHeight = $plotHeight * $value / $axisMax;
                    $radius = min(4, $barHeight, $barWidth / 2);
                    $bx = $x($i) - $barWidth / 2;
                    $by = $baseline - $barHeight;
                @endphp
                <g class="chart-hover">
                    <rect x="{{ $x($i) - $step / 2 }}" y="{{ $top }}" width="{{ $step }}" height="{{ $plotHeight }}" class="chart-hit"/>
                    @if ($value > 0)
                        <path class="chart-bar" d="M{{ $bx }},{{ $baseline }} V{{ $by + $radius }} Q{{ $bx }},{{ $by }} {{ $bx + $radius }},{{ $by }} H{{ $bx + $barWidth - $radius }} Q{{ $bx + $barWidth }},{{ $by }} {{ $bx + $barWidth }},{{ $by + $radius }} V{{ $baseline }} Z"/>
                    @endif
                    <text x="{{ $x($i) }}" y="{{ $by - 6 }}" text-anchor="middle" class="chart-value chart-hover-value">{{ $format($value) }}</text>
                    <title>{{ $labels[$i] }}: {{ $format($value) }}</title>
                </g>
            @endforeach
        @endif
    </svg>

    <table class="visually-hidden">
        <caption>{{ $chart['title'] }}</caption>
        <tr><th scope="col">Ay</th><th scope="col">Değer</th></tr>
        @foreach ($data as $label => $value)
            <tr><td>{{ $label }}</td><td>{{ $value }}</td></tr>
        @endforeach
    </table>
</figure>
