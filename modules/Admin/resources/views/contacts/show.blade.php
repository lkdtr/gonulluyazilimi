@extends('layouts.admin')

@php($canManage = Auth::user()->hasPermission('contacts.manage'))
@php($canManageRoles = Auth::user()->hasPermission('roles.manage'))

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-pretitle">{{ $contact->isOrganization() ? 'Kurum' : 'Kişi' }}</div>
                <h2 class="page-title">{{ $contact->display_name ?: '—' }}</h2>
            </div>
            <div class="col-auto d-flex gap-2">
                <a href="{{ route('admin.contacts') }}" class="btn btn-outline-secondary">Listeye dön</a>
                @if (Auth::user()->canAccess([1]))
                    <a href="{{ route('admin.process-logs', ['subject' => $contact->getMorphClass(), 'subject_id' => $contact->id]) }}" class="btn btn-outline-secondary"><i class="ti ti-history icon"></i> Değişiklik geçmişi</a>
                @endif
                @if ($contact->user)
                    <a href="{{ route('admin.users.show', $contact->user->id) }}" class="btn btn-outline-primary"><i class="ti ti-user icon"></i> Hesap profili</a>
                @elseif ($canManage)
                    @if (app(\App\Support\AccountActivation::class)->eligible($contact))
                        <form method="POST" action="{{ route('admin.contacts.activation', $contact) }}" onsubmit="return confirm('{{ $contact->email }} adresine hesap etkinleştirme bağlantısı gönderilsin mi?')">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary"><i class="ti ti-mail-forward icon"></i> Etkinleştirme bağlantısı gönder</button>
                        </form>
                    @endif
                    <a href="{{ route('admin.contacts.edit', $contact) }}" class="btn btn-primary"><i class="ti ti-edit icon"></i> Düzenle</a>
                @endif
            </div>
        </div>
    </div>

    @include('admin::partials.status')

    <div class="row row-cards">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Bilgiler</h3></div>
                <div class="card-body">
                    @unless ($contact->isOrganization())
                        <div class="text-center mb-3">
                            @if ($contact->approvedPhoto)
                                <img src="{{ route('photos.show', $contact->approvedPhoto) }}" alt="{{ $contact->display_name }}" class="rounded border" style="width: 125px; height: 150px; object-fit: cover;">
                            @else
                                <span class="avatar avatar-xl">{{ mb_strtoupper(mb_substr((string) $contact->first_name, 0, 1).mb_substr((string) $contact->last_name, 0, 1)) }}</span>
                                <div class="small text-secondary mt-1">Onaylı fotoğraf yok</div>
                            @endif
                        </div>
                    @endunless
                    <dl class="row mb-0">
                        <dt class="col-5">{{ $contact->isOrganization() ? 'Vergi no' : 'TC kimlik no' }}</dt>
                        <dd class="col-7">{{ $contact->identity_number ?: '—' }}</dd>
                        <dt class="col-5">E-posta</dt>
                        <dd class="col-7 text-break">{{ $contact->email ?: '—' }}</dd>
                        <dt class="col-5">Telefon</dt>
                        <dd class="col-7">{{ $contact->phone ?: '—' }}</dd>
                        @unless ($contact->isOrganization())
                            <dt class="col-5">Doğum tarihi</dt>
                            <dd class="col-7">{{ $contact->birthday?->format('d.m.Y') ?: '—' }}</dd>
                        @endunless
                        <dt class="col-5">Hesap</dt>
                        <dd class="col-7">{{ $contact->user ? 'Var' : 'Yok' }}</dd>
                        <dt class="col-5">Kayıt</dt>
                        <dd class="col-7">{{ $contact->created_at?->format('d.m.Y') }}</dd>
                    </dl>
                </div>
            </div>

            @php($consentState = app(\App\Support\Consents::class)->current($contact))
            <div class="card mt-3">
                <div class="card-header"><h3 class="card-title">İletişim izinleri</h3></div>
                <div class="card-body">
                    @if ($canManage)
                        <form method="POST" action="{{ route('admin.contacts.consents.update', $contact) }}">
                            @csrf @method('PUT')
                            @foreach (\App\Support\Consents::CHANNELS as $channel => $label)
                                <label class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" name="consents[{{ $channel }}]" value="1" @checked($consentState[$channel])>
                                    <span class="form-check-label">{{ $label }} @if ($consentState[$channel] === null)<span class="text-secondary small">(sorulmadı)</span>@endif</span>
                                </label>
                            @endforeach
                            <div class="form-hint mb-2">Kişinin bildirdiği değişikliği kaydedin; kayıt "Yönetici" kaynağıyla ve sizin adınızla tutulur.</div>
                            <button type="submit" class="btn btn-sm btn-outline-primary">Kaydet</button>
                        </form>
                    @else
                        @foreach (\App\Support\Consents::CHANNELS as $channel => $label)
                            <div>{{ $consentState[$channel] === null ? '—' : ($consentState[$channel] ? '✓' : '✗') }} {{ $label }}</div>
                        @endforeach
                    @endif
                </div>
                @if ($contact->consentEvents()->exists())
                    <div class="list-group list-group-flush small">
                        @foreach ($contact->consentEvents()->with('actor')->limit(10)->get() as $event)
                            <div class="list-group-item">
                                <span class="{{ $event->granted ? 'text-success' : 'text-danger' }}">{{ $event->granted ? 'Verildi' : 'Geri alındı' }}</span>:
                                {{ \App\Support\Consents::CHANNELS[$event->channel] ?? $event->channel }}
                                <div class="text-secondary">{{ $event->created_at->format('d.m.Y H:i') }} · {{ \App\Support\Consents::SOURCES[$event->source] ?? $event->source }}@if ($event->actor && $event->actor->contact_id !== $contact->id) · {{ $event->actor->name }} {{ $event->actor->surname }}@endif @if ($event->ip)· {{ $event->ip }}@endif</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="col-xl-8">
            @moduleSlot('admin.contacts.show', ['contact' => $contact])

            <div class="card">
                <div class="card-header"><h3 class="card-title">Sıfatlar</h3></div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table" data-no-datatable>
                        <thead>
                            <tr>
                                <th>Sıfat</th>
                                <th>Görev</th>
                                <th>Başlangıç</th>
                                <th>Bitiş</th>
                                <th>Durum</th>
                                @if ($canManage)<th class="w-1"></th>@endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($contact->affiliations as $affiliation)
                                @php($locked = $affiliation->type->roles->isNotEmpty() && ! $canManageRoles)
                                <tr>
                                    <td>
                                        {{ $affiliation->type->name }}
                                        @if ($affiliation->type->roles->isNotEmpty())
                                            <i class="ti ti-key icon text-secondary" title="Yetki verir: {{ $affiliation->type->roles->pluck('name')->join(', ') }}"></i>
                                        @endif
                                        @if ($affiliation->note)
                                            <div class="small text-secondary">{{ $affiliation->note }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $affiliation->title ?: '—' }}</td>
                                    <td>{{ $affiliation->started_at?->format('d.m.Y') ?: '—' }}</td>
                                    <td>{{ $affiliation->ended_at?->format('d.m.Y') ?: '—' }}</td>
                                    <td>
                                        @if ($affiliation->isActive())
                                            <span class="badge bg-green-lt">Sürüyor</span>
                                        @else
                                            <span class="badge bg-secondary-lt">Sona erdi</span>
                                        @endif
                                    </td>
                                    @if ($canManage)
                                        <td class="text-nowrap">
                                            @unless ($locked)
                                                @if ($affiliation->isActive())
                                                    <form method="POST" action="{{ route('admin.contacts.affiliations.end', [$contact, $affiliation]) }}" class="d-inline" onsubmit="return confirm('Sıfat bugün itibarıyla sona erdirilsin mi?')">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-outline-warning">Sona erdir</button>
                                                    </form>
                                                @endif
                                                <form method="POST" action="{{ route('admin.contacts.affiliations.destroy', [$contact, $affiliation]) }}" class="d-inline" onsubmit="return confirm('Bu kayıt hatalı girildiyse silinsin mi? Gerçekten sona eren sıfat silinmez, sona erdirilir.')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-ghost-danger" title="Hatalı kaydı sil"><i class="ti ti-trash icon"></i></button>
                                                </form>
                                            @endunless
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-secondary py-4">Sıfat yok.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($canManage)
                    <form method="POST" action="{{ route('admin.contacts.affiliations.store', $contact) }}" class="card-footer">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label for="affiliation_type_id" class="form-label">Sıfat</label>
                                <select id="affiliation_type_id" name="affiliation_type_id" class="form-select @error('affiliation_type_id') is-invalid @enderror" required>
                                    @foreach ($types as $type)
                                        @if ($type->roles->isEmpty() || $canManageRoles)
                                            <option value="{{ $type->id }}" @selected(old('affiliation_type_id') == $type->id)>{{ $type->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="title" class="form-label">Görev</label>
                                <input id="title" name="title" class="form-control" value="{{ old('title') }}" maxlength="100" placeholder="Başkan, Asil…">
                            </div>
                            <div class="col-md-2">
                                <label for="started_at" class="form-label">Başlangıç</label>
                                <input id="started_at" name="started_at" type="date" class="form-control @error('started_at') is-invalid @enderror" value="{{ old('started_at', today()->toDateString()) }}">
                            </div>
                            <div class="col-md-2">
                                <label for="ended_at" class="form-label">Bitiş</label>
                                <input id="ended_at" name="ended_at" type="date" class="form-control @error('ended_at') is-invalid @enderror" value="{{ old('ended_at') }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100"><i class="ti ti-plus icon"></i> Sıfat ekle</button>
                            </div>
                            <div class="col-12">
                                <input name="note" class="form-control form-control-sm" value="{{ old('note') }}" maxlength="1000" placeholder="Not (isteğe bağlı), ör. karar tarihi ve sayısı" aria-label="Not">
                            </div>
                        </div>
                        @if ($errors->any())
                            <div class="text-danger small mt-2">{{ $errors->first() }}</div>
                        @endif
                        <div class="form-hint mt-2">Bitiş, sıfatın geçerli olmadığı ilk gündür. Görev süreli kurullar için dönem sonunu girin.</div>
                    </form>
                @endif
            </div>

            @php($allTags = \App\Models\Tag::orderBy('name')->get())
            @if ($allTags->isNotEmpty() || $contact->tags->isNotEmpty())
                <div class="card mt-3">
                    <div class="card-header"><h3 class="card-title">Etiketler</h3></div>
                    <div class="card-body">
                        @if ($canManage)
                            <form method="POST" action="{{ route('admin.contacts.tags.update', $contact) }}" class="d-flex flex-wrap gap-2 align-items-center">
                                @csrf @method('PUT')
                                @foreach ($allTags as $tag)
                                    <label class="form-selectgroup-item">
                                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="form-selectgroup-input" @checked($contact->tags->contains($tag))>
                                        <span class="form-selectgroup-label">{{ $tag->name }}</span>
                                    </label>
                                @endforeach
                                <button type="submit" class="btn btn-sm btn-outline-primary ms-auto">Kaydet</button>
                            </form>
                        @else
                            @forelse ($contact->tags as $tag)
                                <span class="badge bg-{{ $tag->color }}-lt">{{ $tag->name }}</span>
                            @empty
                                <span class="text-secondary">—</span>
                            @endforelse
                        @endif
                    </div>
                </div>
            @endif

            @php($customFieldList = app(\App\Support\CustomFields::class)->fieldsFor($contact))
            @if ($customFieldList->isNotEmpty())
                @php($customValues = app(\App\Support\CustomFields::class)->values($contact))
                @php($customGroups = app(\App\Support\CustomFields::class)->groups())
                <div class="card mt-3">
                    <div class="card-header"><h3 class="card-title">Ek bilgiler</h3></div>
                    <div class="card-body">
                        @if ($canManage)
                            <form method="POST" action="{{ route('admin.contacts.fields.update', $contact) }}">
                                @csrf @method('PUT')
                                <div class="row">
                                    @foreach ($customFieldList->groupBy('group') as $group => $groupFields)
                                        <div class="col-12"><div class="form-label text-secondary text-uppercase small mt-1">{{ $customGroups[$group] ?? $group }}</div></div>
                                        @foreach ($groupFields as $field)
                                            <div class="col-md-6"><x-custom-field-input :field="$field" :value="$customValues[$field->id] ?? null" bag="contactFields" /></div>
                                        @endforeach
                                    @endforeach
                                </div>
                                <button type="submit" class="btn btn-sm btn-outline-primary">Kaydet</button>
                            </form>
                        @else
                            <dl class="row mb-0">
                                @foreach ($customFieldList as $field)
                                    <dt class="col-5">{{ $field->label }}</dt>
                                    <dd class="col-7">{{ $field->display($customValues[$field->id] ?? null) ?? '—' }}</dd>
                                @endforeach
                            </dl>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
