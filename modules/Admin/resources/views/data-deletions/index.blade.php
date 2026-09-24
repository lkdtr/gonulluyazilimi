@extends('layouts.admin')

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <h2 class="page-title">Veri silme talepleri</h2>
        <div class="text-secondary mt-1">KVKK kapsamındaki kişisel veri silme talepleri. Onay; ad, iletişim bilgileri, kimlik numarası ve fotoğrafı siler, hesabı kapatır, sıfatları ve kartları sona erdirir, iletişim izinlerini geri alır ve açık modüllerdeki kişisel kayıtları temizler. Sözleşme kabulleri ve işlem kayıtları kimlik bilgisi olmadan saklanır. Geri alınamaz.</div>
    </div>

    @include('admin::partials.status')

    <ul class="nav nav-tabs mb-3">
        @foreach (\App\Models\DataDeletionRequest::STATUSES as $key => $label)
            <li class="nav-item"><a class="nav-link @if ($status === $key) active @endif" href="{{ route('admin.data-deletions', ['status' => $key]) }}">{{ $label }}</a></li>
        @endforeach
    </ul>

    @forelse ($requests as $deletion)
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-3 justify-content-between">
                    <div>
                        <div class="fw-bold">
                            #{{ $deletion->id }} ·
                            @if (! $deletion->contact->trashed() && Auth::user()->hasPermission('contacts.view'))
                                <a href="{{ route('admin.contacts.show', $deletion->contact) }}">{{ $deletion->contact->display_name }}</a>
                            @else
                                {{ $deletion->contact->display_name }}
                            @endif
                        </div>
                        <div class="small text-secondary">
                            {{ $deletion->created_at->format('d.m.Y H:i') }}
                            @if ($deletion->reviewed_at) · {{ \App\Models\DataDeletionRequest::STATUSES[$deletion->status] }} {{ $deletion->reviewed_at->format('d.m.Y H:i') }}@if ($deletion->reviewer), {{ $deletion->reviewer->name }} {{ $deletion->reviewer->surname }}@endif @endif
                        </div>
                        @if ($deletion->reason)<div class="mt-2"><span class="text-secondary">Gerekçe:</span> {{ $deletion->reason }}</div>@endif
                        @if ($deletion->response)<div class="mt-2"><span class="text-secondary">Yanıt:</span> {{ $deletion->response }}</div>@endif
                    </div>
                </div>

                @if ($deletion->isPending())
                    <div class="mt-3">
                        @moduleSlot('admin.data-deletion.notes', ['request' => $deletion])
                        <div class="d-flex flex-wrap gap-2">
                            <form method="POST" action="{{ route('admin.data-deletions.approve', $deletion) }}" onsubmit="return confirm('Kişinin verileri silinsin mi? Bu işlem geri alınamaz.')">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-danger"><i class="ti ti-trash icon"></i> Onayla ve verileri sil</button>
                            </form>
                            <form method="POST" action="{{ route('admin.data-deletions.reject', $deletion) }}" class="d-flex gap-2 flex-grow-1">
                                @csrf @method('PATCH')
                                <input name="response" class="form-control" maxlength="2000" required placeholder="Ret gerekçesi (kişiye e-postayla bildirilir)">
                                <button type="submit" class="btn btn-outline-secondary">Reddet</button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="card"><div class="empty"><p class="empty-title">Talep yok</p></div></div>
    @endforelse

    {{ $requests->links('pagination::bootstrap-5') }}
</div>
@endsection
