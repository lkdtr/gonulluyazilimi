@extends('layouts.admin')

@php($organization = old('type', $contact->type) === 'organization')
@php($readOnly = $contact->exists && $contact->user)

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <h2 class="page-title">{{ $contact->exists ? $contact->display_name.' — düzenle' : 'Kişi / kurum ekle' }}</h2>
    </div>

    @include('admin::partials.status')

    @if ($readOnly)
        <div class="alert alert-info">Bu kişinin sisteme giriş hesabı var; bilgileri hesap profilinden düzenlenir.
            <a href="{{ route('admin.users.show', $contact->user->id) }}">Hesap profiline git</a></div>
    @endif

    <form method="POST" action="{{ $contact->exists ? route('admin.contacts.update', $contact) : route('admin.contacts.store') }}" class="card">
        @csrf
        @if ($contact->exists) @method('PUT') @endif

        <fieldset class="card-body" @disabled($readOnly)>
            <div class="mb-3">
                <div class="form-label">Kayıt türü</div>
                <div>
                    <label class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="type" value="person" data-contact-type @checked(! $organization)>
                        <span class="form-check-label">Kişi</span>
                    </label>
                    <label class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="type" value="organization" data-contact-type @checked($organization)>
                        <span class="form-check-label">Kurum</span>
                    </label>
                </div>
            </div>

            <div class="row" data-for-type="person" @if ($organization) hidden @endif>
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label required">Ad</label>
                    <input id="first_name" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $contact->first_name) }}" maxlength="255">
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label required">Soyad</label>
                    <input id="last_name" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $contact->last_name) }}" maxlength="255">
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3" data-for-type="organization" @unless ($organization) hidden @endunless>
                <label for="organization_name" class="form-label required">Kurum adı</label>
                <input id="organization_name" name="organization_name" class="form-control @error('organization_name') is-invalid @enderror" value="{{ old('organization_name', $contact->organization_name) }}" maxlength="255">
                @error('organization_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="identity_number" class="form-label">
                        <span data-for-type="person" @if ($organization) hidden @endif>TC kimlik no</span>
                        <span data-for-type="organization" @unless ($organization) hidden @endunless>Vergi no</span>
                    </label>
                    <input id="identity_number" name="identity_number" inputmode="numeric" class="form-control @error('identity_number') is-invalid @enderror" value="{{ old('identity_number', $contact->identity_number) }}" maxlength="11">
                    @error('identity_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3" data-for-type="person" @if ($organization) hidden @endif>
                    <label for="contact_birthday" class="form-label">Doğum tarihi</label>
                    <input id="contact_birthday" name="birthday" type="date" class="form-control @error('birthday') is-invalid @enderror" value="{{ old('birthday', $contact->birthday?->toDateString()) }}">
                    @error('birthday')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">E-posta</label>
                    <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $contact->email) }}" maxlength="100">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Telefon</label>
                    <input id="phone" name="phone" type="tel" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $contact->phone) }}" maxlength="30" placeholder="+90">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </fieldset>

        <div class="card-footer d-flex gap-2">
            @unless ($readOnly)
                <button type="submit" class="btn btn-primary">Kaydet</button>
            @endunless
            <a href="{{ $contact->exists ? route('admin.contacts.show', $contact) : route('admin.contacts') }}" class="btn btn-outline-secondary">Geri dön</a>
        </div>
    </form>
</div>

<script>
    document.querySelectorAll('[data-contact-type]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            document.querySelectorAll('[data-for-type]').forEach(function (element) {
                element.hidden = element.dataset.forType !== radio.value;
            });
        });
    });
</script>
@endsection
