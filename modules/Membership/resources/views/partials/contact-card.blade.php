{{-- Membership section on the admin contact page (slot admin.contacts.show). $contact --}}
@php($membership = \Modules\Membership\Models\Membership::with('events.actor')->where('contact_id', $contact->id)->first())
@php($canManage = Auth::user()->hasPermission('memberships.manage'))

<div class="card mb-3" id="membership">
    <div class="card-header">
        <h3 class="card-title">Üyelik</h3>
        @if ($membership)
            <div class="card-actions">
                <span class="badge bg-{{ \Modules\Membership\Models\Membership::STATUS_COLORS[$membership->status] }}-lt">{{ $membership->statusLabel() }}</span>
            </div>
        @endif
    </div>

    @if (! $membership)
        <div class="card-body">
            <p class="text-secondary">Bu kişinin üyelik kaydı yok.</p>
            @if ($canManage && ! $contact->trashed())
                <form method="POST" action="{{ route('admin.memberships.store', $contact) }}" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-md-3">
                        <label class="form-label" for="membership-number">Üye no</label>
                        <input id="membership-number" name="number" class="form-control" maxlength="20" value="{{ old('number', app(\Modules\Membership\Support\MembershipService::class)->nextNumber()) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="membership-joined">Katılma tarihi</label>
                        <input id="membership-joined" name="joined_at" type="date" class="form-control" value="{{ old('joined_at', today()->toDateString()) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="membership-note">Not</label>
                        <input id="membership-note" name="note" class="form-control" maxlength="500" placeholder="ör. YK kararı 2026/12">
                    </div>
                    <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Üye yap</button></div>
                </form>
            @endif
        </div>
    @else
        <div class="card-body">
            @if ($canManage)
                <form method="POST" action="{{ route('admin.memberships.update', $membership) }}" class="row g-2">
                    @csrf @method('PUT')
                    <div class="col-md-3">
                        <label class="form-label" for="m-number">Üye no</label>
                        <input id="m-number" name="number" class="form-control @error('number') is-invalid @enderror" maxlength="20" value="{{ old('number', $membership->number) }}">
                        @error('number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="m-applied">Başvuru</label>
                        <input id="m-applied" name="applied_at" type="date" class="form-control" value="{{ old('applied_at', $membership->applied_at?->toDateString()) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="m-joined">Katılma</label>
                        <input id="m-joined" name="joined_at" type="date" class="form-control" value="{{ old('joined_at', $membership->joined_at?->toDateString()) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="m-derbis">DERBİS'e ekli</label>
                        <select id="m-derbis" name="derbis_registered" class="form-select">
                            <option value="" @selected($membership->derbis_registered === null)>Bilinmiyor</option>
                            <option value="1" @selected($membership->derbis_registered === true)>Evet</option>
                            <option value="0" @selected($membership->derbis_registered === false)>Hayır</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <input name="notes" class="form-control" maxlength="2000" value="{{ old('notes', $membership->notes) }}" placeholder="Yönetim notu (yalnız yönetim görür)">
                    </div>
                    <div class="col-12"><button type="submit" class="btn btn-sm btn-outline-primary">Kaydet</button></div>
                </form>

                <hr class="my-3">

                <form method="POST" action="{{ route('admin.memberships.status', $membership) }}" class="row g-2 align-items-end">
                    @csrf @method('PATCH')
                    <div class="col-md-3">
                        <label class="form-label" for="m-status">Durumu değiştir</label>
                        <select id="m-status" name="status" class="form-select">
                            @foreach ([\Modules\Membership\Models\Membership::ACTIVE, \Modules\Membership\Models\Membership::SUSPENDED, \Modules\Membership\Models\Membership::LEFT] as $option)
                                <option value="{{ $option }}" @selected($membership->status === $option)>{{ \Modules\Membership\Models\Membership::STATUSES[$option] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="m-date">Tarih</label>
                        <input id="m-date" name="date" type="date" class="form-control" value="{{ today()->toDateString() }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="m-note">Not</label>
                        <input id="m-note" name="note" class="form-control" maxlength="500" placeholder="ör. istifa dilekçesi, YK kararı">
                    </div>
                    <div class="col-md-2"><button type="submit" class="btn btn-outline-secondary w-100">Uygula</button></div>
                </form>
            @else
                <dl class="row mb-0">
                    <dt class="col-5">Üye no</dt><dd class="col-7">{{ $membership->number ?? '—' }}</dd>
                    <dt class="col-5">Katılma</dt><dd class="col-7">{{ $membership->joined_at?->format('d.m.Y') ?? '—' }}</dd>
                    @if ($membership->left_at)<dt class="col-5">Ayrılma</dt><dd class="col-7">{{ $membership->left_at->format('d.m.Y') }}</dd>@endif
                </dl>
            @endif
        </div>

        <div class="card-header border-top"><h3 class="card-title">Üyelik tarihçesi</h3></div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table" data-no-datatable>
                <thead><tr><th>Olay</th><th>Tarih</th><th>Ek süre</th><th>Not</th></tr></thead>
                <tbody>
                    @forelse ($membership->events as $event)
                        <tr>
                            <td>{{ $event->label() }}</td>
                            <td class="text-secondary">{{ $event->occurred_on->format('d.m.Y') }}</td>
                            <td class="text-secondary">{{ $event->extra_months ? $event->extra_months.' ay' : '—' }}</td>
                            <td class="text-secondary small">{{ $event->note }}@if ($event->actor) <span class="text-muted">· {{ $event->actor->name }} {{ $event->actor->surname }}</span>@endif</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-secondary">Olay yok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($canManage)
            <div class="card-footer">
                <form method="POST" action="{{ route('admin.memberships.events.store', $membership) }}" class="row g-2">
                    @csrf
                    <div class="col-md-3"><input name="date" type="date" class="form-control form-control-sm" value="{{ today()->toDateString() }}" required></div>
                    <div class="col-md-2"><input name="extra_months" type="number" class="form-control form-control-sm" placeholder="Ek süre (ay)" min="-120" max="120"></div>
                    <div class="col-md-5"><input name="note" class="form-control form-control-sm" maxlength="500" required placeholder="Tarihçeye not"></div>
                    <div class="col-md-2"><button type="submit" class="btn btn-sm btn-outline-secondary w-100">Not ekle</button></div>
                </form>
            </div>
        @endif
    @endif
</div>
