{{-- Membership on the person's profile page (slot profile.sections). $contact --}}
@php($membership = $contact ? \Modules\Membership\Models\Membership::with('events')->where('contact_id', $contact->id)->first() : null)

@if ($membership)
    <div class="card mt-3" id="membership">
        <div class="card-header">
            <h3 class="card-title">Üyelik</h3>
            <div class="card-actions"><span class="badge bg-{{ \Modules\Membership\Models\Membership::STATUS_COLORS[$membership->status] }}-lt">{{ $membership->statusLabel() }}</span></div>
        </div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-md-4">Üye no</dt><dd class="col-md-8">{{ $membership->number ?? '—' }}</dd>
                @if ($membership->applied_at)<dt class="col-md-4">Başvuru tarihi</dt><dd class="col-md-8">{{ $membership->applied_at->format('d.m.Y') }}</dd>@endif
                <dt class="col-md-4">Katılma tarihi</dt><dd class="col-md-8">{{ $membership->joined_at?->format('d.m.Y') ?? '—' }}</dd>
                @if ($membership->left_at)<dt class="col-md-4">Ayrılma tarihi</dt><dd class="col-md-8">{{ $membership->left_at->format('d.m.Y') }}</dd>@endif
            </dl>
        </div>
        <div class="card-header border-top"><h3 class="card-title">Üyelik tarihçesi</h3></div>
        <div class="table-responsive">
            <table class="table card-table" data-no-datatable>
                <thead><tr><th>Olay</th><th>Tarih</th><th>Ek üyelik süresi</th></tr></thead>
                <tbody>
                    @forelse ($membership->events->where('type', '!=', 'note') as $event)
                        <tr><td>{{ $event->label() }}</td><td>{{ $event->occurred_on->format('d.m.Y') }}</td><td>{{ $event->extra_months ? $event->extra_months.' ay' : '—' }}</td></tr>
                    @empty
                        <tr><td colspan="3" class="text-secondary">Olay bulunamadı.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif
