{{-- Agreements the person accepted: the latest acceptance per agreement, then earlier ones. $acceptances (newest first) --}}
@if ($acceptances->isNotEmpty())
    @php($current = $acceptances->unique(fn ($acceptance) => $acceptance->version->agreement_id))
    @php($past = $acceptances->diff($current))
    <div class="card mt-3" id="agreements">
        <div class="card-header"><h3 class="card-title">Sözleşmelerim</h3></div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table" data-no-datatable>
                <thead><tr><th>Sözleşme</th><th>Sürüm</th><th>Kabul tarihi</th><th>IP adresi</th></tr></thead>
                <tbody>
                    @foreach ($current as $acceptance)
                        <tr>
                            <td>
                                <a href="{{ route('agreements.show', $acceptance->version->agreement->key) }}?version={{ $acceptance->version->version }}" target="_blank" rel="noopener">{{ $acceptance->version->agreement->title }}</a>
                                @if ($acceptance->version->agreement->currentVersion && $acceptance->version->isNot($acceptance->version->agreement->currentVersion))
                                    <span class="badge bg-yellow-lt ms-1" title="Sözleşmenin daha yeni bir sürümü yayınlandı">Yeni sürüm var</span>
                                @endif
                            </td>
                            <td>{{ $acceptance->version->version }}</td>
                            <td class="text-secondary">{{ $acceptance->accepted_at->format('d.m.Y H:i') }}</td>
                            <td class="text-secondary"><code>{{ $acceptance->ip ?? '—' }}</code></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($past->isNotEmpty())
            <div class="card-body">
                <details>
                    <summary class="text-secondary">Geçmiş kabuller ({{ $past->count() }})</summary>
                    <table class="table table-sm mt-2 mb-0" data-no-datatable>
                        @foreach ($past as $acceptance)
                            <tr>
                                <td><a href="{{ route('agreements.show', $acceptance->version->agreement->key) }}?version={{ $acceptance->version->version }}" target="_blank" rel="noopener">{{ $acceptance->version->agreement->title }}</a></td>
                                <td>Sürüm {{ $acceptance->version->version }}</td>
                                <td class="text-secondary">{{ $acceptance->accepted_at->format('d.m.Y H:i') }}</td>
                                <td class="text-secondary"><code>{{ $acceptance->ip ?? '—' }}</code></td>
                            </tr>
                        @endforeach
                    </table>
                </details>
            </div>
        @endif
    </div>
@endif
