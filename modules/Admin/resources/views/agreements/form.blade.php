@extends('layouts.admin')

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">{{ $agreement->exists ? $agreement->title : 'Sözleşme ekle' }}</h2>
                @if ($agreement->exists)
                    <div class="text-secondary mt-1">
                        @if ($agreement->currentVersion)
                            Yürürlükte: sürüm {{ $agreement->currentVersion->version }} ({{ $agreement->currentVersion->published_at->format('d.m.Y H:i') }}).
                        @else
                            Henüz yayınlanmadı; formlarda gösterilmez.
                        @endif
                        @if ($agreement->draft)
                            <strong>Aşağıdaki metin sürüm {{ $agreement->draft->version }} taslağıdır.</strong>
                        @endif
                    </div>
                @endif
            </div>
            @if ($agreement->exists)
                <div class="col-auto d-flex gap-2">
                    <a href="{{ route('admin.agreements.show', $agreement) }}" class="btn btn-outline-secondary"><i class="ti ti-history icon"></i> Sürümler ve kabuller</a>
                </div>
            @endif
        </div>
    </div>

    @include('admin::partials.status')

    <form method="POST" action="{{ $agreement->exists ? route('admin.agreements.update', $agreement) : route('admin.agreements.store') }}" class="card">
        @csrf
        @if ($agreement->exists) @method('PUT') @endif

        <div class="card-body">
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label for="title" class="form-label required">Başlık</label>
                    <input id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $agreement->title) }}" maxlength="200" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="key" class="form-label required">Anahtar</label>
                    @if ($agreement->exists)
                        <input id="key" class="form-control" value="{{ $agreement->key }}" disabled>
                    @else
                        <input id="key" name="key" class="form-control @error('key') is-invalid @enderror" value="{{ old('key') }}" maxlength="50" required pattern="[a-z0-9]+(-[a-z0-9]+)*" placeholder="ör. kvkk">
                        @error('key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-hint">Küçük harf, rakam ve tire; sonradan değişmez.</div>
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Açıklama</label>
                <input id="description" name="description" class="form-control @error('description') is-invalid @enderror" value="{{ old('description', $agreement->description) }}" maxlength="1000" placeholder="Nerede kullanıldığı (yalnız yönetimde görünür)">
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div>
                <label for="content" class="form-label required">Metin</label>
                <textarea id="content" name="content" rows="20" class="wysiwyg form-control @error('content') is-invalid @enderror">{{ old('content', $content) }}</textarea>
                @error('content')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="card-footer d-flex flex-wrap gap-2 align-items-center">
            <button type="submit" name="action" value="draft" class="btn btn-outline-primary">Taslak olarak kaydet</button>
            <button type="submit" name="action" value="publish" class="btn btn-primary" onclick="return confirm('Metin yeni sürüm olarak yayınlansın mı? Yayınlanan sürüm değiştirilemez; bundan sonraki kabuller bu sürüme kaydedilir.')"><i class="ti ti-send icon"></i> Yayınla</button>
            <a href="{{ route('admin.agreements') }}" class="btn btn-ghost-secondary">İptal</a>
            @if ($agreement->draft)
                <button type="submit" form="discard-draft" class="btn btn-ghost-danger ms-auto" onclick="return confirm('Taslak silinsin mi?')">Taslağı sil</button>
            @endif
        </div>
    </form>

    @if ($agreement->draft)
        <form id="discard-draft" method="POST" action="{{ route('admin.agreements.draft.destroy', $agreement) }}">@csrf @method('DELETE')</form>
    @endif
</div>
@endsection
