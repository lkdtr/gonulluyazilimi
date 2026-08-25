@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-secondary">
                <div class="card-header text-white bg-secondary">Parola değiştir</div>
                <div class="card-body">
                    @if (session('success-status'))<div class="alert alert-success">{{ session('success-status') }}</div>@endif
                    <form method="POST" action="{{ route('password.change.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mevcut parola</label>
                            <input id="current_password" type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required autocomplete="current-password">
                            @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Yeni parola</label>
                            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
                            <div id="password-strength-status" class="password-strength-status"></div>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="password-confirm" class="form-label">Yeni parola (tekrar)</label>
                            <input id="password-confirm" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
                            <div id="password-confirm-strength-status" class="password-strength-status"></div>
                        </div>
                        <button type="submit" class="btn btn-primary">Parolayı değiştir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
