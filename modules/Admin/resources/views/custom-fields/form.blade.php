@extends('layouts.admin')

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <h2 class="page-title">{{ $field->exists ? $field->label.' — düzenle' : 'Özel alan ekle' }}</h2>
    </div>

    @include('admin::partials.status')

    <form method="POST" action="{{ $field->exists ? route('admin.custom-fields.update', $field) : route('admin.custom-fields.store') }}" class="card">
        @csrf
        @if ($field->exists) @method('PUT') @endif

        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="label" class="form-label required">Ad</label>
                    <input id="label" name="label" class="form-control @error('label') is-invalid @enderror" value="{{ old('label', $field->label) }}" maxlength="100" required placeholder="ör. Takma ad">
                    @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="key" class="form-label required">Anahtar</label>
                    @if ($field->exists)
                        <input id="key" class="form-control" value="{{ $field->key }}" disabled>
                    @else
                        <input id="key" name="key" class="form-control @error('key') is-invalid @enderror" value="{{ old('key') }}" maxlength="50" required pattern="[a-z0-9]+(_[a-z0-9]+)*" placeholder="ör. nickname">
                        @error('key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-hint">Küçük harf, rakam ve alt çizgi; sonradan değişmez.</div>
                    @endif
                </div>
                <div class="col-md-2 mb-3">
                    <label for="sort" class="form-label required">Sıra</label>
                    <input id="sort" name="sort" type="number" min="0" max="10000" class="form-control" value="{{ old('sort', $field->sort) }}" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="type" class="form-label required">Tür</label>
                    @if ($field->exists)
                        <input class="form-control" value="{{ \App\Models\CustomField::TYPES[$field->type] }}" disabled>
                        <div class="form-hint">Tür sonradan değişmez.</div>
                    @else
                        <select id="type" name="type" class="form-select" data-field-type>
                            @foreach (\App\Models\CustomField::TYPES as $type => $label)
                                <option value="{{ $type }}" @selected(old('type', $field->type) === $type)>{{ $label }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="col-md-4 mb-3">
                    <label for="group" class="form-label required">Grup</label>
                    <select id="group" name="group" class="form-select">
                        @foreach ($groups as $group => $label)
                            <option value="{{ $group }}" @selected(old('group', $field->group) === $group)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="applies_to" class="form-label required">Uygulandığı kayıt</label>
                    <select id="applies_to" name="applies_to" class="form-select">
                        @foreach (\App\Models\CustomField::APPLIES_TO as $value => $label)
                            <option value="{{ $value }}" @selected(old('applies_to', $field->applies_to) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-3" data-options-row @if (old('type', $field->type) !== 'select') style="display:none" @endif>
                <label for="options" class="form-label">Seçenekler</label>
                <textarea id="options" name="options" rows="4" class="form-control @error('options') is-invalid @enderror" placeholder="Her satıra bir seçenek">{{ old('options', implode("\n", $field->options ?? [])) }}</textarea>
                @error('options')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="help" class="form-label">Açıklama</label>
                <input id="help" name="help" class="form-control" value="{{ old('help', $field->help) }}" maxlength="500" placeholder="Formda alanın altında gösterilir">
            </div>

            <div class="mb-3">
                <div class="form-label">Kişinin erişimi</div>
                @foreach (\App\Models\CustomField::ACCESS as $value => $label)
                    <label class="form-check">
                        <input type="radio" class="form-check-input" name="member_access" value="{{ $value }}" @checked(old('member_access', $field->member_access) === $value)>
                        <span class="form-check-label">{{ $label }}</span>
                    </label>
                @endforeach
            </div>

            <label class="form-check">
                <input type="checkbox" class="form-check-input" name="is_required" value="1" @checked(old('is_required', $field->is_required))>
                <span class="form-check-label">Zorunlu (formlarda boş bırakılamaz)</span>
            </label>
            <label class="form-check">
                <input type="checkbox" class="form-check-input" name="is_active" value="1" @checked(old('is_active', $field->is_active))>
                <span class="form-check-label">Açık (kapalı alan hiçbir yerde gösterilmez, değerleri saklanır)</span>
            </label>
        </div>

        <div class="card-footer d-flex gap-2">
            <button type="submit" class="btn btn-primary">Kaydet</button>
            <a href="{{ route('admin.custom-fields') }}" class="btn btn-outline-secondary">İptal</a>
            @if ($field->exists)
                <button type="submit" form="delete-field" class="btn btn-ghost-danger ms-auto" onclick="return confirm('Alan ve kişilerdeki bütün değerleri silinsin mi?')">Sil</button>
            @endif
        </div>
    </form>

    @if ($field->exists)
        <form id="delete-field" method="POST" action="{{ route('admin.custom-fields.destroy', $field) }}">@csrf @method('DELETE')</form>
    @endif
</div>

<script>
    document.querySelector('[data-field-type]')?.addEventListener('change', function (event) {
        document.querySelector('[data-options-row]').style.display = event.target.value === 'select' ? '' : 'none';
    });
</script>
@endsection
