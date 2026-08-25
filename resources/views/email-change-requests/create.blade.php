@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-secondary">
                <div class="card-header text-white bg-secondary">E-posta değişikliği talebi</div>
                <div class="card-body">
                    @if (session('success-status'))<div class="alert alert-success">{{ session('success-status') }}</div>@endif
                    @if (session('danger-status'))<div class="alert alert-danger">{{ session('danger-status') }}</div>@endif

                    @if ($pendingRequest)
                        <div class="alert alert-info mb-0">
                            <strong>{{ $pendingRequest->requested_email }}</strong> adresine ilişkin talebiniz {{ $pendingRequest->created_at->format('d.m.Y H:i') }} tarihinde alındı ve değerlendiriliyor.
                        </div>
                    @else
                        <p>Mevcut e-posta adresiniz: <strong>{{ auth()->user()->email }}</strong></p>
                        <p class="text-muted">Talebiniz yönetim onayından sonra uygulanır. Aktif penguen.org.tr yönlendirmeniz varsa, yeni adrese de güncellenir.</p>
                        <form method="POST" action="{{ route('email-change-requests.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="requested_email" class="form-label">Yeni e-posta adresi</label>
                                <input id="requested_email" name="requested_email" type="email" value="{{ old('requested_email') }}" class="form-control @error('requested_email') is-invalid @enderror" required autocomplete="email">
                                @error('requested_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label for="reason" class="form-label">Açıklama <small class="text-muted">(isteğe bağlı)</small></label>
                                <textarea id="reason" name="reason" rows="3" class="form-control @error('reason') is-invalid @enderror">{{ old('reason') }}</textarea>
                                @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mevcut parolanız</label>
                                <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Talep oluştur</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
