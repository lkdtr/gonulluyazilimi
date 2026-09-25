@extends('layouts.app')

@section('title', 'Parolanızı belirleyin')

@section('content')
<div class="container-tight py-4">
    <div class="card card-md">
        <div class="card-body">
            <h2 class="h2 mb-3">Parolanızı belirleyin</h2>
            <p class="text-secondary">Merhaba {{ $contact->display_name }}, parolanızı belirleyince hesabınız etkinleşir ve giriş yaparsınız.</p>

            <form method="POST" action="{{ $action }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">E-posta</label>
                    <input class="form-control" value="{{ $contact->email }}" disabled>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label required">Parola</label>
                    <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password" minlength="8">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-hint">En az 8 karakter.</div>
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label required">Parola (tekrar)</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
                </div>

                @if ($version = app(\App\Support\Agreements::class)->current(\App\Models\Agreement::PRIVACY))
                    <label class="form-check mb-3">
                        <input type="checkbox" class="form-check-input @error('agreement') is-invalid @enderror" name="agreement" value="true" required>
                        <span class="form-check-label"><a href="{{ route('agreements.show', $version->agreement->key) }}" target="_blank" rel="noopener">{{ $version->agreement->title }}</a> koşullarını kabul ediyorum</span>
                    </label>
                @endif

                <button type="submit" class="btn btn-primary w-100">Hesabımı etkinleştir</button>
            </form>
        </div>
    </div>
</div>
@endsection
