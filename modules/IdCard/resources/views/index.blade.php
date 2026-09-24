@extends('layouts.app')

@section('content')
<div class="container">
    @foreach (['success-status' => 'success', 'danger-status' => 'danger'] as $key => $class)
        @if (session($key))
            <div class="alert alert-{{ $class }}" role="alert">{{ session($key) }}</div>
        @endif
    @endforeach

    <div class="page-header mb-3 d-print-none">
        <h2 class="page-title">Kimlik kartlarım</h2>
        <div class="text-secondary mt-1">Dernekteki her sıfatınız için bir kimlik kartı. Kartın üzerindeki QR kod okutulunca kartın geçerli olup olmadığı gösterilir; sıfatınız sona erince kart da geçersiz olur.</div>
    </div>

    @if ($cards->isEmpty())
        <div class="card">
            <div class="empty">
                <div class="empty-icon"><i class="ti ti-id-badge-2 icon"></i></div>
                <p class="empty-title">Henüz kartınız yok</p>
                <p class="empty-subtitle text-secondary">Kimlik kartı gönüllü ve üye gibi sıfatlar için verilir. Sıfatınız tanımlandığında kartınız burada görünür.</p>
            </div>
        </div>
    @else
        @if ($cards->contains('ready', false))
            <div class="alert alert-info d-print-none">
                <div class="d-flex gap-2">
                    <i class="ti ti-camera icon alert-icon"></i>
                    <div>
                        Kartınızın gösterilmesi için onaylı bir fotoğrafınız olmalı.
                        @if ($contact->latestPhotoUpload?->isPending())
                            Yüklediğiniz fotoğraf onay bekliyor.
                        @else
                            <a href="{{ route('my-infos') }}#photo">Profil sayfanızdan fotoğraf yükleyin</a>; yönetici onayından sonra kartınız hazır olur.
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <div class="row row-cards">
            @foreach ($cards as $item)
                @php($card = $item['card'])
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header d-print-none">
                            <h3 class="card-title">{{ $card->template->name }}</h3>
                            <div class="card-actions text-secondary small">{{ $card->number }}</div>
                        </div>
                        <div class="card-body d-flex justify-content-center">
                            @if ($item['ready'])
                                @include('id-card::partials.card', [
                                    'id' => 'id-card-'.$card->id,
                                    'template' => $card->template,
                                    'name' => $contact->display_name,
                                    'title' => $card->affiliation->title ?: $card->affiliation->type->name,
                                    'fields' => $item['fields'],
                                    'number' => $card->number,
                                    'since' => $card->affiliation->started_at,
                                    'photoUrl' => $photo ? route('photos.show', $photo) : null,
                                    'qr' => $item['qr'],
                                    'void' => $card->revoked_at ? 'İPTAL' : null,
                                ])
                            @else
                                <div class="text-secondary py-4 text-center">
                                    <i class="ti ti-camera mb-2" style="font-size: 2rem;"></i>
                                    <div>Fotoğrafınız onaylanınca kartınız burada görünecek.</div>
                                </div>
                            @endif
                        </div>
                        @if ($item['ready'])
                            <div class="card-footer d-flex flex-wrap gap-2 d-print-none">
                                @if ($card->revoked_at)
                                    <span class="text-danger">Bu kart iptal edildi{{ $card->revoked_reason ? ': '.$card->revoked_reason : '' }}.</span>
                                @else
                                    <button type="button" class="btn btn-primary" onclick="printIdCard('id-card-{{ $card->id }}')"><i class="ti ti-printer icon"></i> Yazdır / PDF olarak kaydet</button>
                                    <a href="{{ \Modules\IdCard\Support\CardView::verifyUrl($card) }}" target="_blank" rel="noopener" class="btn btn-outline-secondary"><i class="ti ti-shield-check icon"></i> Doğrulama sayfası</a>
                                    <form method="POST" action="{{ route('id-cards.renew', $card) }}" class="ms-auto" onsubmit="return confirm('QR kod yenilensin mi? Kartın eski çıktıları ve ekran görüntüleri artık doğrulanmaz.')">
                                        @csrf
                                        <button type="submit" class="btn btn-ghost-secondary" title="Kartınızın görüntüsü başkasının eline geçtiyse eski QR kodu geçersiz kılar"><i class="ti ti-refresh icon"></i> QR kodu yenile</button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
