@extends('layouts.admin')

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <h2 class="page-title">E-posta yönlendirme</h2>
        <div class="text-secondary mt-1">Dernek e-posta adresini (ad.soyad@alan-adı) kimlerin alacağı. Bir sıfatın karşısına alan adı yazın; boş bırakılan sıfata adres verilmez. Birden fazla uygun sıfatı olan kişi, listede üstte olan sıfatın alan adını alır. Hiçbir sıfata alan adı yazılmazsa hizmet kimseye açık olmaz.</div>
    </div>

    @include('admin::partials.status')

    <form method="POST" action="{{ route('admin.forwarding.settings.update') }}" class="card">
        @csrf @method('PUT')
        <div class="card-body">
            @foreach ($types as $key => $name)
                <div class="row mb-2 align-items-center">
                    <label class="col-md-4 col-form-label" for="domain-{{ $key }}">{{ $name }}</label>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">ad.soyad@</span>
                            <input id="domain-{{ $key }}" name="domains[{{ $key }}]" class="form-control @error('domains.'.$key) is-invalid @enderror" value="{{ old('domains.'.$key, $domains[$key] ?? '') }}" placeholder="adres verilmez">
                        </div>
                        @error('domains.'.$key)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
            @endforeach

            <div class="row mt-3">
                <label class="col-md-4 col-form-label" for="label">Adresin adı</label>
                <div class="col-md-6">
                    <input id="label" name="label" class="form-control @error('label') is-invalid @enderror" value="{{ old('label', $label) }}" maxlength="60" required>
                    @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-hint">Kimlik kartında ve kişi alanlarında görünür, ör. "Gönüllü e-posta adresi".</div>
                </div>
            </div>
        </div>
        <div class="card-footer"><button type="submit" class="btn btn-primary">Kaydet</button></div>
    </form>

    <p class="text-secondary small mt-3">Yeni bir alan adı kullanmadan önce alan adının PostfixAdmin sunucusunda tanımlı olduğundan emin olun.</p>
</div>
@endsection
