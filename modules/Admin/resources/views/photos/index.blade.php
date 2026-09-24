@extends('layouts.admin')

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <h2 class="page-title">Fotoğraf onayı</h2>
        <div class="text-secondary mt-1">Kişilerin yüklediği profil fotoğrafları. Onaylanan fotoğraf kimlik kartlarında görünür; kişinin önceki fotoğrafı silinir. Reddedilen fotoğrafın dosyası silinir, gerekçe kişiye gösterilir.</div>
    </div>

    @include('admin::partials.status')

    @if ($photos->isEmpty())
        <div class="card">
            <div class="empty">
                <div class="empty-icon"><i class="ti ti-photo-check icon"></i></div>
                <p class="empty-title">Onay bekleyen fotoğraf yok</p>
            </div>
        </div>
    @else
        <div class="row row-cards">
            @foreach ($photos as $photo)
                <div class="col-sm-6 col-lg-4 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex gap-2 justify-content-center mb-3">
                                @if ($photo->contact->approvedPhoto)
                                    <div class="text-center">
                                        <img src="{{ route('photos.show', $photo->contact->approvedPhoto) }}" alt="Mevcut fotoğraf" class="rounded border opacity-50" style="width: 72px; height: 90px; object-fit: cover;">
                                        <div class="small text-secondary">Mevcut</div>
                                    </div>
                                @endif
                                <div class="text-center">
                                    <a href="{{ route('photos.show', $photo) }}" target="_blank" rel="noopener">
                                        <img src="{{ route('photos.show', $photo) }}" alt="Onay bekleyen fotoğraf" class="rounded border" style="width: 144px; height: 180px; object-fit: cover;">
                                    </a>
                                    <div class="small text-secondary">Yeni</div>
                                </div>
                            </div>
                            <div class="fw-bold">
                                @if (Auth::user()->hasPermission('contacts.view'))
                                    <a href="{{ route('admin.contacts.show', $photo->contact) }}">{{ $photo->contact->display_name }}</a>
                                @else
                                    {{ $photo->contact->display_name }}
                                @endif
                            </div>
                            <div class="small text-secondary">{{ $photo->created_at->format('d.m.Y H:i') }}</div>
                        </div>
                        <div class="card-footer">
                            <form method="POST" action="{{ route('admin.photos.approve', $photo) }}" class="mb-2">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-success w-100"><i class="ti ti-check icon"></i> Onayla</button>
                            </form>
                            <form method="POST" action="{{ route('admin.photos.reject', $photo) }}" class="d-flex gap-2">
                                @csrf @method('PATCH')
                                <input name="reason" class="form-control form-control-sm" maxlength="255" placeholder="Gerekçe (isteğe bağlı)">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Reddet</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-3">{{ $photos->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
