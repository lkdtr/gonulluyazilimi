@extends('layouts.app')
                    <table class="table">
                        <thead><tr><th>Seminer</th><th>Kurum</th><th>Tür</th><th>Yer</th><th>Tarih</th><th>Talep sahibi</th><th>Durum</th></tr></thead>
                        <tbody>
                            @forelse ($seminarRequests as $seminarRequest)
                                <tr>
                                    <td>{{ $seminarRequest->seminarSubject->subject }}</td>
                                    <td>{{ $seminarRequest->organizationRecord?->name ?? $seminarRequest->organization }}</td>
                                    <td>{{ $seminarRequest->seminar_type === 'online' ? 'Online' : 'Yüz yüze' }}</td>
                                    <td>{{ $seminarRequest->seminar_type === 'online' ? '-' : $seminarRequest->location }}</td>
                                    <td>{{ $seminarRequest->seminar_start_date->format('d.m.Y') }}@if(!$seminarRequest->seminar_start_date->isSameDay($seminarRequest->seminar_end_date)) – {{ $seminarRequest->seminar_end_date->format('d.m.Y') }}@endif</td>
                                    <td>{{ $seminarRequest->user->name }} {{ $seminarRequest->user->surname }}<br><small>{{ $seminarRequest->user->email }}</small></td>
                                    <td>Değerlendiriliyor</td>
                                </tr>
                            @empty
                                <tr><td colspan="7">Henüz seminer talebi yok.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-secondary">
                <div class="card-header text-white bg-secondary">{{ trans("panel.seminar_requests_title") }}</div>

                <div class="card-body">
                    @if (session('success-status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success-status') }}
                        </div>
                    @endif

                    @if (session('danger-status'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('danger-status') }}
                        </div>
                    @endif


                </div>
            </div>
        </div>
    </div>
</div>
@endsection
