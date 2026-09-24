@extends('layouts.admin')

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <div class="page-pretitle">{{ $template->affiliationType->name }}</div>
        <h2 class="page-title">{{ $template->exists ? $template->name.' — düzenle' : 'Kart şablonu ekle' }}</h2>
    </div>

    @include('admin::partials.status')

    <form method="POST" enctype="multipart/form-data" action="{{ $template->exists ? route('admin.id-cards.templates.update', $template) : route('admin.id-cards.templates.store') }}" class="card">
        @csrf
        @if ($template->exists)
            @method('PUT')
        @else
            <input type="hidden" name="affiliation_type_id" value="{{ $template->affiliation_type_id }}">
        @endif

        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label required">Kart adı</label>
                    <input id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $template->name) }}" maxlength="100" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="organization_name" class="form-label required">Kurum adı</label>
                    <input id="organization_name" name="organization_name" class="form-control @error('organization_name') is-invalid @enderror" value="{{ old('organization_name', $template->organization_name) }}" maxlength="150" required>
                    @error('organization_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                @foreach (['background_color' => 'Arka plan rengi', 'text_color' => 'Yazı rengi', 'accent_color' => 'Vurgu rengi'] as $field => $label)
                    <div class="col-sm-4 mb-3">
                        <label for="{{ $field }}" class="form-label required">{{ $label }}</label>
                        <input id="{{ $field }}" name="{{ $field }}" type="color" class="form-control form-control-color w-100 @error($field) is-invalid @enderror" value="{{ old($field, $template->$field) }}" required>
                        @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                @endforeach
            </div>

            <div class="row">
                <div class="col-sm-4 mb-3">
                    <label for="number_prefix" class="form-label">Kart no öneki</label>
                    <input id="number_prefix" name="number_prefix" class="form-control @error('number_prefix') is-invalid @enderror" value="{{ old('number_prefix', $template->number_prefix) }}" maxlength="10" pattern="[A-Za-z0-9-]*" placeholder="ör. G-">
                    @error('number_prefix')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-sm-4 mb-3">
                    <label for="number_digits" class="form-label required">Hane sayısı</label>
                    <input id="number_digits" name="number_digits" type="number" min="1" max="10" class="form-control @error('number_digits') is-invalid @enderror" value="{{ old('number_digits', $template->number_digits) }}" required>
                    @error('number_digits')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-sm-4 mb-3">
                    <div class="form-label">Örnek</div>
                    <div class="form-control-plaintext"><code>{{ $numberExample }}</code></div>
                    <div class="form-hint">Değişiklik yalnız yeni kartlara uygulanır.</div>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-label">Kartta gösterilecek alanlar</div>
                <div class="form-hint mb-2">Ad soyad, sıfat, kart no ve fotoğraf her kartta var. Bunlara ek olarak en çok {{ $maxFields }} alan seçin; değeri olmayan alan kartta görünmez.</div>
                @php($selected = old('fields', $template->fields ?? []))
                <div class="row">
                    @foreach ($fieldOptions as $key => $label)
                        <div class="col-sm-6 col-lg-4">
                            <label class="form-check">
                                <input type="checkbox" class="form-check-input" name="fields[]" value="{{ $key }}" @checked(in_array($key, $selected, true))>
                                <span class="form-check-label">{{ $label }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('fields')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="footer_text" class="form-label">Alt yazı</label>
                <input id="footer_text" name="footer_text" class="form-control @error('footer_text') is-invalid @enderror" value="{{ old('footer_text', $template->footer_text) }}" maxlength="150" placeholder="ör. Bu kart sahibinin derneğimizin gönüllüsü olduğunu gösterir.">
                @error('footer_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="logo" class="form-label">Logo</label>
                @if ($template->logo_path)
                    <div class="mb-2 d-flex align-items-center gap-3">
                        <img src="{{ route('id-card.logo', $template) }}" alt="Logo" style="height: 48px;" class="border rounded p-1">
                        <label class="form-check mb-0">
                            <input type="checkbox" class="form-check-input" name="remove_logo" value="1">
                            <span class="form-check-label">Logoyu kaldır</span>
                        </label>
                    </div>
                @endif
                <input id="logo" name="logo" type="file" accept="image/png,image/jpeg,image/webp" class="form-control @error('logo') is-invalid @enderror">
                @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-hint">PNG, JPEG veya WebP, en çok 1 MB. Kartın üst şeridinde beyaz zemin üzerinde gösterilir.</div>
            </div>

            <label class="form-check">
                <input type="checkbox" class="form-check-input" name="requires_photo" value="1" @checked(old('requires_photo', $template->requires_photo))>
                <span class="form-check-label">Fotoğraf gerekli (kart, kişinin onaylı fotoğrafı olunca gösterilir)</span>
            </label>
            <label class="form-check">
                <input type="checkbox" class="form-check-input" name="is_active" value="1" @checked(old('is_active', $template->is_active))>
                <span class="form-check-label">Açık (kapatılınca bu türdeki kartlar gösterilmez ve doğrulamada geçersiz görünür)</span>
            </label>
        </div>

        <div class="card-footer d-flex gap-2">
            <button type="submit" class="btn btn-primary">Kaydet</button>
            <a href="{{ route('admin.id-cards') }}" class="btn btn-outline-secondary">İptal</a>
        </div>
    </form>
</div>
@endsection
