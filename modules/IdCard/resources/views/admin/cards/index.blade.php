@extends('layouts.admin')

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <h2 class="page-title">Verilen kartlar</h2>
        <div class="text-secondary mt-1">Kart, kişi "Kartlarım" sayfasını ilk açtığında verilir. İptal edilen kart doğrulamada geçersiz görünür; QR kodu yenilemek kartın eski çıktılarını geçersiz kılar.</div>
    </div>

    @include('admin::partials.status')

    <form method="GET" class="card card-body mb-3">
        <div class="row g-2">
            <div class="col-md-6">
                <input name="q" value="{{ $search }}" class="form-control" placeholder="Ad, soyad veya kart no">
            </div>
            <div class="col-md-4">
                <select name="template" class="form-select">
                    <option value="">Tüm kartlar</option>
                    @foreach ($templates as $template)
                        <option value="{{ $template->id }}" @selected(request('template') == $template->id)>{{ $template->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Ara</button>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table" data-no-datatable>
                <thead>
                    <tr>
                        <th>Kart no</th>
                        <th>Kişi</th>
                        <th>Kart</th>
                        <th>Verilme</th>
                        <th>Durum</th>
                        <th class="w-1"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cards as $card)
                        @php($status = $card->status())
                        <tr>
                            <td><code>{{ $card->number }}</code></td>
                            <td>
                                @if (Auth::user()->hasPermission('contacts.view'))
                                    <a href="{{ route('admin.contacts.show', $card->contact) }}">{{ $card->contact->display_name }}</a>
                                @else
                                    {{ $card->contact->display_name }}
                                @endif
                            </td>
                            <td>{{ $card->template->name }}</td>
                            <td class="text-secondary">{{ $card->created_at->format('d.m.Y') }}</td>
                            <td>
                                @switch($status)
                                    @case('valid')<span class="badge bg-green-lt">Geçerli</span>@break
                                    @case('revoked')<span class="badge bg-red-lt" title="{{ $card->revoked_reason }}">İptal</span>@break
                                    @case('ended')<span class="badge bg-secondary-lt">Sıfat sona erdi</span>@break
                                    @default<span class="badge bg-yellow-lt">Şablon kapalı</span>
                                @endswitch
                            </td>
                            <td class="text-nowrap">
                                <div class="d-flex gap-1 justify-content-end">
                                    <form method="POST" action="{{ route('admin.id-cards.renew', $card) }}" onsubmit="return confirm('QR kod yenilensin mi? Kartın eski çıktıları doğrulanmaz.')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-ghost-secondary" title="QR kodu yenile"><i class="ti ti-refresh icon"></i></button>
                                    </form>
                                    @if ($card->revoked_at)
                                        <form method="POST" action="{{ route('admin.id-cards.restore', $card) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">İptali kaldır</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.id-cards.revoke', $card) }}" class="d-flex gap-1" onsubmit="return confirm('Kart iptal edilsin mi?')">
                                            @csrf @method('PATCH')
                                            <input name="reason" class="form-control form-control-sm" maxlength="255" placeholder="Gerekçe" style="width: 9rem;">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">İptal et</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-secondary py-4">Kart yok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $cards->links('pagination::bootstrap-5') }}</div>
</div>
@endsection
