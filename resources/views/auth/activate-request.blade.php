@extends('layouts.app')

@section('title', 'Hesabımı etkinleştir')

@section('content')
<div class="container-tight py-4">
    <div class="card card-md">
        <div class="card-body">
            <h2 class="h2 mb-3">Hesabımı etkinleştir</h2>
            <p class="text-secondary">{{ $organization->name() }} kayıtlarında e-posta adresiniz varsa ama portalda henüz hesabınız yoksa (ör. önceki üyelik sisteminden aktarılan üyeler), parolanızı belirleyerek hesabınızı etkinleştirebilirsiniz.</p>

            @if (session('status'))
                <div class="alert alert-info" role="alert">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('account.activation.send') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label required">E-posta adresiniz</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus autocomplete="email">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn btn-primary w-100">Bağlantı gönder</button>
            </form>
        </div>
    </div>
    <div class="text-center text-secondary mt-3">Hesabınız var mı? <a href="{{ route('login') }}">Giriş yapın</a></div>
</div>
@endsection
