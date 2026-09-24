@extends('layouts.admin')

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <h2 class="page-title">Etiketler</h2>
        <div class="text-secondary mt-1">Kişi ve kurumlara verilen serbest etiketler (ör. Basın, Sponsor, Eğitmen). Kişi listesinde etikete göre süzülür.</div>
    </div>

    @include('admin::partials.status')

    <form method="POST" action="{{ route('admin.tags.store') }}" class="card card-body mb-3">
        @csrf
        <div class="row g-2 align-items-end">
            <div class="col-md-6">
                <label for="name" class="form-label">Yeni etiket</label>
                <input id="name" name="name" class="form-control @error('name') is-invalid @enderror" maxlength="60" required value="{{ old('name') }}">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label for="color" class="form-label">Renk</label>
                <select id="color" name="color" class="form-select">
                    @foreach (\App\Models\Tag::COLORS as $color)
                        <option value="{{ $color }}">{{ $color }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Ekle</button></div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table" data-no-datatable>
                <thead><tr><th>Etiket</th><th>Kişi</th><th class="w-1"></th></tr></thead>
                <tbody>
                    @forelse ($tags as $tag)
                        <tr>
                            <td>
                                <form method="POST" action="{{ route('admin.tags.update', $tag) }}" class="d-flex gap-2 align-items-center">
                                    @csrf @method('PUT')
                                    <span class="badge bg-{{ $tag->color }}-lt">{{ $tag->name }}</span>
                                    <input name="name" value="{{ $tag->name }}" class="form-control form-control-sm" maxlength="60" style="max-width: 16rem;" required>
                                    <select name="color" class="form-select form-select-sm" style="max-width: 9rem;">
                                        @foreach (\App\Models\Tag::COLORS as $color)
                                            <option value="{{ $color }}" @selected($tag->color === $color)>{{ $color }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Kaydet</button>
                                </form>
                            </td>
                            <td><a href="{{ route('admin.contacts', ['tag' => $tag->id]) }}">{{ $tag->contacts_count }}</a></td>
                            <td>
                                <form method="POST" action="{{ route('admin.tags.destroy', $tag) }}" onsubmit="return confirm('Etiket silinsin mi? Kişilerden de kaldırılır.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-ghost-danger" title="Sil"><i class="ti ti-trash icon"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-secondary py-4">Henüz etiket yok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
