{{--
    One card. $template (IdCardTemplate), $name, $title, $fields (label => value),
    $number, $since (?Carbon), $photoUrl (?string), $qr (?string SVG markup),
    optional $id and $void (label shown across an invalid card).
--}}
@include('id-card::partials.styles')

<div class="id-card" @isset($id) id="{{ $id }}" @endisset
     style="--card-bg: {{ $template->background_color }}; --card-text: {{ $template->text_color }}; --card-accent: {{ $template->accent_color }};">
    <div class="id-card-inner">
        <div class="id-card-head">
            @if ($template->logo_path)
                <img src="{{ route('id-card.logo', $template) }}" alt="" class="id-card-logo">
            @endif
            <div class="id-card-org">{{ $template->organization_name }}</div>
            <div class="id-card-type">{{ $template->name }}</div>
        </div>

        <div class="id-card-body">
            @if ($photoUrl)
                <img src="{{ $photoUrl }}" alt="{{ $name }}" class="id-card-photo">
            @else
                <div class="id-card-photo"><i class="ti ti-user"></i></div>
            @endif

            <div class="id-card-main">
                <div class="id-card-name">{{ $name }}</div>
                <div class="id-card-title">{{ $title }}</div>

                @if ($fields)
                    <dl class="id-card-fields">
                        @foreach ($fields as $label => $value)
                            <dt>{{ $label }}</dt>
                            <dd>{{ $value }}</dd>
                        @endforeach
                    </dl>
                @endif

                <div class="id-card-foot">
                    <div>
                        <div class="id-card-number">{{ $number }}</div>
                        @if ($since)
                            <div class="id-card-since">{{ $since->format('m/Y') }} tarihinden beri</div>
                        @endif
                    </div>
                    @if ($qr)
                        <div class="id-card-qr">{!! $qr !!}</div>
                    @endif
                </div>
            </div>
        </div>

        @if ($template->footer_text)
            <div class="id-card-footer">{{ $template->footer_text }}</div>
        @endif
    </div>

    @if (! empty($void))
        <div class="id-card-void"><span>{{ $void }}</span></div>
    @endif
</div>
