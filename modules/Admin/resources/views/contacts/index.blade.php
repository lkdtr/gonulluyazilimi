@extends('layouts.admin')

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Kişi & Kurumlar</h2>
                <div class="text-secondary mt-1">{{ $contacts->total() }} kayıt</div>
            </div>
            @if (Auth::user()->hasPermission('contacts.manage'))
                <div class="col-auto">
                    <a href="{{ route('admin.contacts.create') }}" class="btn btn-primary"><i class="ti ti-plus icon"></i> Kişi / kurum ekle</a>
                </div>
            @endif
        </div>
    </div>

    @include('admin::partials.status')

    <div class="card">
        <div class="card-body border-bottom">
            <form method="GET" class="row g-2">
                <div class="col-md-4">
                    <input type="search" name="q" value="{{ $filters['search'] }}" class="form-control" placeholder="Ad, kurum, e-posta, telefon veya kimlik no" aria-label="Ara">
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select" aria-label="Kayıt türü">
                        <option value="">Tüm kayıt türleri</option>
                        <option value="person" @selected($filters['type'] === 'person')>Kişi</option>
                        <option value="organization" @selected($filters['type'] === 'organization')>Kurum</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="tag" class="form-select" aria-label="Etiket">
                        <option value="">Tüm etiketler</option>
                        @foreach ($tags as $tagOption)
                            <option value="{{ $tagOption->id }}" @selected($filters['tag'] === $tagOption->id)>{{ $tagOption->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="affiliation" class="form-select" aria-label="Sıfat">
                        <option value="">Tüm sıfatlar</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->key }}" @selected($filters['affiliation'] === $type->key)>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">Filtrele</button>
                    <a href="{{ route('admin.contacts') }}" class="btn btn-outline-secondary" title="Temizle"><i class="ti ti-x icon"></i></a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-vcenter card-table" data-no-datatable>
                <thead>
                    <tr>
                        <th>Ad / Kurum</th>
                        <th>E-posta</th>
                        <th>Telefon</th>
                        <th>Sıfatlar</th>
                        <th>Hesap</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contacts as $contact)
                        <tr>
                            <td>
                                <a href="{{ route('admin.contacts.show', $contact) }}">{{ $contact->display_name ?: '—' }}</a>
                                @foreach ($contact->tags as $contactTag)
                                    <span class="badge bg-{{ $contactTag->color }}-lt ms-1">{{ $contactTag->name }}</span>
                                @endforeach
                                @if ($contact->isOrganization())
                                    <span class="badge bg-azure-lt ms-1">Kurum</span>
                                @endif
                            </td>
                            <td class="text-secondary">{{ $contact->email }}</td>
                            <td class="text-secondary">{{ $contact->phone }}</td>
                            <td>
                                @foreach ($contact->affiliations as $affiliation)
                                    <span class="badge bg-blue-lt">{{ $affiliation->type->name }}@if ($affiliation->title) · {{ $affiliation->title }}@endif</span>
                                @endforeach
                            </td>
                            <td>
                                @if ($contact->user)
                                    <span class="badge bg-green-lt">Var</span>
                                @else
                                    <span class="text-secondary">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary py-4">Aranan kriterde kayıt bulunamadı.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($contacts->hasPages())
            <div class="card-footer">{{ $contacts->links('pagination::bootstrap-5') }}</div>
        @endif
    </div>
</div>
@endsection
