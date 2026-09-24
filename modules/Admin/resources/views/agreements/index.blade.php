@extends('layouts.admin')

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Sözleşmeler</h2>
                <div class="text-secondary mt-1">Formlarda kabul edilen metinler. Düzenleme taslağa yapılır; yayınlanan her sürüm saklanır ve kabuller o sürüme kaydedilir.</div>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.agreements.create') }}" class="btn btn-primary"><i class="ti ti-plus icon"></i> Sözleşme ekle</a>
            </div>
        </div>
    </div>

    @include('admin::partials.status')

    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table" data-no-datatable>
                <thead>
                    <tr>
                        <th>Sözleşme</th>
                        <th>Anahtar</th>
                        <th>Yürürlükteki sürüm</th>
                        <th>Kabul</th>
                        <th class="w-1"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($agreements as $agreement)
                        <tr>
                            <td>
                                {{ $agreement->title }}
                                @if ($agreement->draft)<span class="badge bg-yellow-lt ms-1">Taslak var</span>@endif
                                @if ($agreement->description)<div class="small text-secondary">{{ $agreement->description }}</div>@endif
                            </td>
                            <td><code>{{ $agreement->key }}</code></td>
                            <td>
                                @if ($agreement->currentVersion)
                                    {{ $agreement->currentVersion->version }}
                                    <span class="text-secondary small">({{ $agreement->currentVersion->published_at->format('d.m.Y') }})</span>
                                @else
                                    <span class="badge bg-secondary-lt">Yayında değil</span>
                                @endif
                            </td>
                            <td><a href="{{ route('admin.agreements.show', $agreement) }}">{{ $agreement->acceptance_count }}</a></td>
                            <td class="text-nowrap">
                                <a href="{{ route('admin.agreements.edit', $agreement) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
                                @if ($agreement->currentVersion)
                                    <a href="{{ route('agreements.show', $agreement->key) }}" target="_blank" rel="noopener" class="btn btn-sm btn-ghost-secondary" title="Yayındaki metni aç"><i class="ti ti-external-link icon"></i></a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary py-4">Henüz sözleşme yok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <p class="text-secondary small mt-3">Kodun kullandığı anahtarlar: <code>kvkk</code> (kayıt, referans ve e-posta yönlendirme formlarındaki kişisel veri politikası), <code>email-usage</code> (e-posta yönlendirme sözleşmesi). Bu anahtarla yayında sürüm yoksa formlarda onay kutusu gösterilmez.</p>
</div>
@endsection
