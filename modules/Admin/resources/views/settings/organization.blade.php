@extends('layouts.admin')

@php
    $value = fn (string $key) => old($key, $key === 'name' ? $organization->name() : $organization->get($key));
    $input = function (string $key, string $label, string $type = 'text', ?string $hint = null, ?string $placeholder = null) use ($value, $errors) {
        return view('admin::settings.partials.input', compact('key', 'label', 'type', 'hint', 'placeholder') + ['value' => $value($key), 'errors' => $errors]);
    };
@endphp

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <h2 class="page-title">Kurum ayarları</h2>
        <div class="text-secondary mt-1">Sitede, e-postalarda ve kartlarda görünen kurum bilgileri. Boş bırakılan alanlar gösterilmez.</div>
    </div>

    @include('admin::partials.status')

    <form method="POST" action="{{ route('admin.settings.organization.update') }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="row row-cards">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header"><h3 class="card-title">Genel</h3></div>
                    <div class="card-body">
                        {{ $input('name', 'Kurum adı') }}
                        {{ $input('short_name', 'Kısa ad', 'text', 'Yönetim paneli başlığında kullanılır.', 'ör. LKD') }}
                        {{ $input('website_url', 'Web sitesi', 'url', null, 'https://') }}
                        {{ $input('source_url', 'Kaynak kodu adresi', 'url', 'Alt bilgideki "Kaynak kodu" bağlantısı. Boşsa yazılımın kendi deposu gösterilir.', config('organization.source_url')) }}
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header"><h3 class="card-title">Görünüm</h3></div>
                    <div class="card-body">
                        @foreach (['logo' => ['Logo', 'logoUrl', 'Üst menüde, ana sayfada ve e-postalarda gösterilir. PNG, JPEG veya WebP, en çok 2 MB.', 'image/png,image/jpeg,image/webp'], 'favicon' => ['Favicon', 'faviconUrl', 'Tarayıcı sekmesindeki simge. PNG veya ICO, en çok 512 KB.', 'image/png,image/x-icon,.ico']] as $field => [$label, $url, $hint, $accept])
                            <div class="mb-3">
                                <label for="{{ $field }}" class="form-label">{{ $label }}</label>
                                @if ($organization->$url())
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        <img src="{{ $organization->$url() }}" alt="{{ $label }}" class="border rounded p-1 bg-white" style="max-height: {{ $field === 'logo' ? 64 : 32 }}px; max-width: 240px;">
                                        <label class="form-check mb-0">
                                            <input type="checkbox" class="form-check-input" name="remove_{{ $field }}" value="1">
                                            <span class="form-check-label">Kaldır</span>
                                        </label>
                                    </div>
                                @endif
                                <input id="{{ $field }}" name="{{ $field }}" type="file" accept="{{ $accept }}" class="form-control @error($field) is-invalid @enderror">
                                @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-hint">{{ $hint }}</div>
                            </div>
                        @endforeach

                        <div class="mb-3">
                            <label for="primary_color" class="form-label">Ana renk</label>
                            <div class="d-flex gap-2 align-items-center">
                                <input id="primary_color" name="primary_color" type="color" class="form-control form-control-color @error('primary_color') is-invalid @enderror" value="{{ $value('primary_color') ?: '#d9151d' }}" @disabled(! $value('primary_color')) data-color-input>
                                <label class="form-check mb-0">
                                    <input type="checkbox" class="form-check-input" @checked(! $value('primary_color')) data-color-default>
                                    <span class="form-check-label">Temanın rengini kullan</span>
                                </label>
                            </div>
                            @error('primary_color')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            <div class="form-hint">Düğmeler, bağlantılar ve vurgular bu renkte görünür.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header"><h3 class="card-title">İletişim</h3></div>
                    <div class="card-body">
                        {{ $input('contact_email', 'İletişim e-postası', 'email', 'Alt bilgide "Soru, şikâyet ve önerileriniz için" adresi.') }}
                        {{ $input('phone', 'Telefon', 'text', null, '+90 ...') }}
                        {{ $input('address', 'Adres', 'text', 'E-postaların alt bilgisinde gösterilir.') }}
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header"><h3 class="card-title">Sosyal medya</h3></div>
                    <div class="card-body">
                        <div class="row">
                            @foreach (\App\Support\Organization::SOCIALS as $key => [$label, $icon])
                                <div class="col-sm-6">{{ $input($key, $label, 'url', null, 'https://') }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header"><h3 class="card-title">E-posta</h3></div>
                    <div class="card-body">
                        {{ $input('notification_email', 'Bildirim adresi', 'email', 'Seminer talepleri, e-posta değişikliği talepleri gibi yönetime giden bildirimler. Boşsa bildirim gönderilmez.', 'ör. yk@ornek.org.tr') }}
                        {{ $input('sender_email', 'Duyuru gönderici adresi', 'email', 'Boşsa uygulamanın genel gönderici adresi kullanılır.') }}
                        {{ $input('sender_name', 'Duyuru gönderici adı', 'text', 'Boşsa kurum adı kullanılır.') }}
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header"><h3 class="card-title">Gömme ve ölçüm</h3></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="frame_ancestors" class="form-label">Gömülebilecek siteler</label>
                            <textarea id="frame_ancestors" name="frame_ancestors" rows="3" class="form-control @error('frame_ancestors') is-invalid @enderror" placeholder="https://www.ornek.org.tr">{{ $value('frame_ancestors') }}</textarea>
                            @error('frame_ancestors')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-hint">Giriş, kayıt ve seminer sayfalarını <code>?in-iframe=1</code> ile iframe içinde gösterebilecek siteler; her satıra bir https adresi.</div>
                        </div>
                        {{ $input('ga_measurement_id', 'Google Analytics ölçüm kimliği', 'text', 'Boşsa Google Analytics yüklenmez.', 'G-XXXXXXXXXX') }}
                        {{ $input('gtm_container_id', 'Google Tag Manager kimliği', 'text', 'Boşsa Tag Manager yüklenmez.', 'GTM-XXXXXXX') }}
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Ana sayfa</h3></div>
                    <div class="card-body">
                        {{ $input('home_title', 'Başlık', 'text', 'Boşsa kurum adı gösterilir.') }}
                        <div class="mb-1">
                            <label for="home_content" class="form-label">İçerik</label>
                            <textarea id="home_content" name="home_content" rows="14" class="wysiwyg form-control @error('home_content') is-invalid @enderror" data-upload-url="{{ route('admin.settings.organization.images') }}">{{ $value('home_content') }}</textarea>
                            @error('home_content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-hint">Giriş yapmamış ziyaretçilerin gördüğü ana sayfa. Boşsa logo ve modüllerin tanıtım metni gösterilir. Açık modüllerin bölümleri (ör. temsilcilikler) içeriğin altında görünmeye devam eder.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Kaydet</button>
        </div>
    </form>
</div>

<script>
    // "Use the theme colour" empties the colour field so the setting is removed.
    document.querySelectorAll('[data-color-default]').forEach(function (box) {
        box.addEventListener('change', function () {
            document.querySelector('[data-color-input]').disabled = box.checked;
        });
    });
</script>
@endsection
