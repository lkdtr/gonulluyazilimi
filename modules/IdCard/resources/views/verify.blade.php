@extends('layouts.app')

@section('title', 'Kart doğrulama')

@section('content')
<div class="container-tight py-4">
    @php
        $states = [
            'valid' => ['success', 'shield-check', 'Geçerli kart'],
            'revoked' => ['danger', 'shield-x', 'İptal edilmiş kart'],
            'ended' => ['danger', 'shield-x', 'Geçerliliği sona ermiş kart'],
            'inactive' => ['warning', 'shield-off', 'Geçersiz kart'],
        ];
        [$color, $icon, $headline] = $card ? $states[$status] : ['danger', 'shield-question', 'Kart bulunamadı'];
    @endphp

    <div class="card card-md">
        <div class="card-status-top bg-{{ $color }}"></div>
        <div class="card-body text-center">
            <div class="mb-3"><i class="ti ti-{{ $icon }} text-{{ $color }}" style="font-size: 4rem; line-height: 1;"></i></div>
            <h2 class="h1 mb-2 text-{{ $color }}">{{ $headline }}</h2>

            @if ($card)
                <div class="text-secondary mb-4">{{ $card->template->organization_name }}</div>
                <dl class="row text-start mb-0">
                    <dt class="col-5">Kart sahibi</dt>
                    <dd class="col-7">{{ $maskedName }}</dd>
                    <dt class="col-5">Kart</dt>
                    <dd class="col-7">{{ $card->template->name }}</dd>
                    <dt class="col-5">Kart no</dt>
                    <dd class="col-7"><code>{{ $card->number }}</code></dd>
                    @if ($card->affiliation->started_at)
                        <dt class="col-5">{{ $card->template->affiliationType->name }}</dt>
                        <dd class="col-7">{{ $card->affiliation->started_at->format('m/Y') }} tarihinden beri</dd>
                    @endif
                </dl>
                <p class="small text-secondary mt-4 mb-0">Kartın üzerindeki ad ve kart numarasının bu bilgilerle uyuştuğunu kontrol edin.</p>
            @else
                <p class="text-secondary mb-0">Bu QR koda ait bir kart yok. Kart yenilenmiş ya da QR kod hatalı olabilir.</p>
            @endif
        </div>
        <div class="card-footer text-secondary small text-center">Doğrulama zamanı: {{ now()->format('d.m.Y H:i') }}</div>
    </div>
</div>
@endsection
