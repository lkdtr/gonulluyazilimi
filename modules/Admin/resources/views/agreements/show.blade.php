@extends('layouts.admin')

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-pretitle">Sözleşme</div>
                <h2 class="page-title">{{ $agreement->title }}</h2>
            </div>
            <div class="col-auto d-flex gap-2">
                <a href="{{ route('admin.agreements') }}" class="btn btn-outline-secondary">Listeye dön</a>
                <a href="{{ route('admin.agreements.edit', $agreement) }}" class="btn btn-primary"><i class="ti ti-edit icon"></i> Düzenle</a>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Yayınlanan sürümler</h3></div>
                <div class="list-group list-group-flush">
                    @forelse ($versions as $version)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('agreements.show', $agreement->key) }}?version={{ $version->version }}" target="_blank" rel="noopener">Sürüm {{ $version->version }}</a>
                                <a href="{{ route('admin.agreements.show', [$agreement, 'version' => $version->version]) }}" class="text-secondary">{{ $version->acceptances_count }} kabul</a>
                            </div>
                            <div class="small text-secondary">{{ $version->published_at->format('d.m.Y H:i') }}@if ($version->publisher) · {{ $version->publisher->name }} {{ $version->publisher->surname }}@endif</div>
                        </div>
                    @empty
                        <div class="list-group-item text-secondary">Yayınlanmış sürüm yok.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kabuller @if (request('version'))<span class="text-secondary">— sürüm {{ request('version') }}</span>@endif</h3>
                    @if (request('version'))
                        <div class="card-actions"><a href="{{ route('admin.agreements.show', $agreement) }}">Tümü</a></div>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table" data-no-datatable>
                        <thead>
                            <tr>
                                <th>Kişi</th>
                                <th>Sürüm</th>
                                <th>Nerede</th>
                                <th>IP</th>
                                <th>Zaman</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($acceptances as $acceptance)
                                <tr>
                                    <td>
                                        @if ($acceptance->contact && Auth::user()->hasPermission('contacts.view'))
                                            <a href="{{ route('admin.contacts.show', $acceptance->contact) }}">{{ $acceptance->contact->display_name }}</a>
                                        @else
                                            {{ $acceptance->contact?->display_name ?? ($acceptance->user ? $acceptance->user->name.' '.$acceptance->user->surname : '—') }}
                                        @endif
                                    </td>
                                    <td>{{ $acceptance->version->version }}</td>
                                    <td class="text-secondary">{{ ['register' => 'Kayıt', 'email-forwarding' => 'E-posta yönlendirme', 'reference' => 'Referans talebi'][$acceptance->context] ?? $acceptance->context }}</td>
                                    <td class="text-secondary"><code>{{ $acceptance->ip ?? '—' }}</code></td>
                                    <td class="text-secondary">{{ $acceptance->accepted_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-secondary py-4">Kabul kaydı yok.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($acceptances->hasPages())
                    <div class="card-footer">{{ $acceptances->links('pagination::bootstrap-5') }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
