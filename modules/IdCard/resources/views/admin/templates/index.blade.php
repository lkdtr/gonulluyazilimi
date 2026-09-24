@extends('layouts.admin')

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Kart şablonları</h2>
                <div class="text-secondary mt-1">Her sıfat için bir kart tasarımı. Sıfatı süren kişi, "Kartlarım" sayfasından kartını görür; sıfat sona erince kart geçersiz olur.</div>
            </div>
            @if ($typesWithoutTemplate->isNotEmpty())
                <div class="col-auto">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-plus icon"></i> Şablon ekle</button>
                        <div class="dropdown-menu dropdown-menu-end">
                            @foreach ($typesWithoutTemplate as $type)
                                <a class="dropdown-item" href="{{ route('admin.id-cards.templates.create', ['type' => $type->id]) }}">{{ $type->name }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @include('admin::partials.status')

    <div class="row row-cards">
        @forelse ($templates as $item)
            @php($template = $item['template'])
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            {{ $template->name }}
                            <span class="text-secondary fw-normal">— {{ $template->affiliationType->name }}</span>
                        </h3>
                        <div class="card-actions">
                            @if ($template->is_active)
                                <span class="badge bg-green-lt">Açık</span>
                            @else
                                <span class="badge bg-secondary-lt">Kapalı</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body d-flex justify-content-center bg-body-secondary">
                        @include('id-card::partials.card', [
                            'template' => $template,
                            'name' => 'Ad Soyad',
                            'title' => $template->affiliationType->name,
                            'fields' => $item['fields'],
                            'number' => $template->formatNumber(1),
                            'since' => now(),
                            'photoUrl' => null,
                            'qr' => null,
                        ])
                    </div>
                    <div class="card-footer d-flex align-items-center gap-2">
                        <a href="{{ route('admin.id-cards.issued', ['template' => $template->id]) }}" class="text-secondary">{{ $template->cards_count }} kart verildi</a>
                        @if ($template->requires_photo)<span class="badge bg-blue-lt">Fotoğraf gerekli</span>@endif
                        <div class="ms-auto d-flex gap-2">
                            <a href="{{ route('admin.id-cards.templates.edit', $template) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
                            @if ($template->cards_count === 0)
                                <form method="POST" action="{{ route('admin.id-cards.templates.destroy', $template) }}" onsubmit="return confirm('Şablon silinsin mi?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-ghost-danger" title="Sil"><i class="ti ti-trash icon"></i></button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="empty">
                        <div class="empty-icon"><i class="ti ti-id-badge-2 icon"></i></div>
                        <p class="empty-title">Henüz kart şablonu yok</p>
                        <p class="empty-subtitle text-secondary">Bir sıfat seçip şablon ekleyin.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
