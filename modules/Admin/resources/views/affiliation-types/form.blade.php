@extends('layouts.admin')

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <h2 class="page-title">{{ $type->exists ? $type->name.' — düzenle' : 'Sıfat ekle' }}</h2>
    </div>

    @include('admin::partials.status')

    <form method="POST" action="{{ $type->exists ? route('admin.affiliation-types.update', $type) : route('admin.affiliation-types.store') }}" class="card">
        @csrf
        @if ($type->exists) @method('PUT') @endif

        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label required">Ad</label>
                    <input id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $type->name) }}" maxlength="100" required placeholder="ör. Yönetim Kurulu Üyesi">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="key" class="form-label required">Anahtar</label>
                    @if ($type->exists)
                        <input id="key" class="form-control" value="{{ $type->key }}" disabled>
                        <div class="form-hint">Anahtar sonradan değiştirilemez.</div>
                    @else
                        <input id="key" name="key" class="form-control @error('key') is-invalid @enderror" value="{{ old('key') }}" maxlength="50" required pattern="[a-z0-9]+(-[a-z0-9]+)*" placeholder="ör. youth-board">
                        @error('key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-hint">Küçük harf, rakam ve tire.</div>
                    @endif
                </div>
                <div class="col-md-2 mb-3">
                    <label for="sort" class="form-label required">Sıra</label>
                    <input id="sort" name="sort" type="number" min="0" max="10000" class="form-control @error('sort') is-invalid @enderror" value="{{ old('sort', $type->sort) }}" required>
                    @error('sort')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Açıklama</label>
                <textarea id="description" name="description" rows="2" class="form-control @error('description') is-invalid @enderror" maxlength="1000">{{ old('description', $type->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <label class="form-check mb-3">
                <input type="checkbox" class="form-check-input" name="has_term" value="1" @checked(old('has_term', $type->has_term))>
                <span class="form-check-label">Görev süreli (kurullar gibi dönemle verilir; bitiş tarihi beklenir)</span>
            </label>

            <div class="mb-1">
                <div class="form-label">Rol şablonu</div>
                <div class="form-hint mb-2">Bu sıfat sürdükçe kişinin hesabı seçilen rollerin yetkilerini alır; sıfat sona erince yetki düşer.</div>
                @unless ($canManageRoles)
                    <div class="alert alert-info">Rol şablonunu yalnız rolleri yönetebilenler değiştirebilir.</div>
                @endunless
                <fieldset @disabled(! $canManageRoles)>
                    @foreach ($roles as $role)
                        <label class="form-check">
                            <input type="checkbox" class="form-check-input" name="roles[]" value="{{ $role->id }}"
                                @checked(in_array($role->id, old('roles', $selectedRoles)))
                                @disabled($role->isOwner() && ! Auth::user()->isOwner())>
                            <span class="form-check-label">{{ $role->name }} @if ($role->description)<span class="text-secondary">— {{ $role->description }}</span>@endif</span>
                        </label>
                    @endforeach
                </fieldset>
            </div>
        </div>

        <div class="card-footer d-flex gap-2">
            <button type="submit" class="btn btn-primary">Kaydet</button>
            <a href="{{ route('admin.affiliation-types') }}" class="btn btn-outline-secondary">İptal</a>
        </div>
    </form>
</div>
@endsection
