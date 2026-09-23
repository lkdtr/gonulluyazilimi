@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center"><div class="col-md-12">
        <div class="card border-secondary">
            <div class="card-header text-white bg-secondary">E-posta değişikliği talepleri</div>
            <div class="card-body">
                @if (session('success-status'))<div class="alert alert-success">{{ session('success-status') }}</div>@endif
                @if (session('danger-status'))<div class="alert alert-danger">{{ session('danger-status') }}</div>@endif
                <table class="table">
                    <thead><tr><th>Üye</th><th>Mevcut adres</th><th>İstenen adres</th><th>Açıklama</th><th>Talep tarihi</th><th>İşlem</th></tr></thead>
                    <tbody>
                    @forelse ($emailChangeRequests as $emailChangeRequest)
                        <tr>
                            <td>{{ $emailChangeRequest->user->name }} {{ $emailChangeRequest->user->surname }}</td>
                            <td>{{ $emailChangeRequest->current_email }}</td>
                            <td>{{ $emailChangeRequest->requested_email }}</td>
                            <td>{{ $emailChangeRequest->reason ?: '-' }}</td>
                            <td>{{ $emailChangeRequest->created_at->format('d.m.Y H:i') }}</td>
                            <td class="d-flex gap-1">
                                <form method="POST" action="{{ route('admin.email-change-requests.approve', $emailChangeRequest) }}">@csrf @method('PATCH')<button class="btn btn-success btn-sm" type="submit">Onayla</button></form>
                                <form method="POST" action="{{ route('admin.email-change-requests.reject', $emailChangeRequest) }}">@csrf @method('PATCH')<button class="btn btn-outline-danger btn-sm" type="submit">Reddet</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">Bekleyen e-posta değişikliği talebi yok.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div></div>
</div>
@endsection
