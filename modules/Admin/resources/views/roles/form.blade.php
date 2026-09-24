@extends('layouts.admin')

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <h2 class="page-title">{{ $role->exists ? $role->name.' — düzenle' : 'Rol ekle' }}</h2>
    </div>

    @include('admin::partials.status')

    <form method="POST" action="{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}" class="card">
        @csrf
        @if ($role->exists) @method('PUT') @endif

        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label required">Rol adı</label>
                    <input id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $role->name) }}" maxlength="100" required placeholder="ör. Mali işler">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="key" class="form-label required">Anahtar</label>
                    @if ($role->exists)
                        <input id="key" class="form-control" value="{{ $role->key }}" disabled>
                    @else
                        <input id="key" name="key" class="form-control @error('key') is-invalid @enderror" value="{{ old('key') }}" maxlength="50" required pattern="[a-z0-9]+(-[a-z0-9]+)*" placeholder="ör. finance">
                        @error('key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-hint">Küçük harf, rakam ve tire; sonradan değiştirilemez.</div>
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Rolün hangi kullanıcılarla ilgili olduğu</label>
                <textarea id="description" name="description" rows="2" class="form-control @error('description') is-invalid @enderror" maxlength="1000">{{ old('description', $role->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            @if ($role->isOwner())
                <div class="alert alert-info mb-0">Sahip rolü bütün yetkilere sahiptir; yetkileri değiştirilemez.</div>
            @else
                <div class="form-label">Yetkiler</div>
                @error('permissions.*')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                <div class="row">
                    @foreach ($groups as $group)
                        <div class="col-md-6 mb-3">
                            <div class="fw-bold mb-1">{{ $group['label'] }}</div>
                            @foreach ($group['permissions'] as $permission)
                                <label class="form-check">
                                    <input type="checkbox" class="form-check-input" name="permissions[]" value="{{ $permission['key'] }}" @checked(in_array($permission['key'], old('permissions', $selected), true))>
                                    <span class="form-check-label">{{ $permission['label'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endforeach
                </div>
                <div class="form-hint">Yönetim paneline girebilmek için "Yönetim paneline girebilsin" yetkisi gerekir.</div>
            @endif
        </div>

        <div class="card-footer d-flex gap-2">
            <button type="submit" class="btn btn-primary">Kaydet</button>
            <a href="{{ route('admin.roles') }}" class="btn btn-outline-secondary">İptal</a>
        </div>
    </form>
</div>
@endsection
