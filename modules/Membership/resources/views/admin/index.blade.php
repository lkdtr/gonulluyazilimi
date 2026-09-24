@extends('layouts.admin')

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <h2 class="page-title">Üyeler</h2>
        <div class="text-secondary mt-1">Üyelik kayıtları. Bir kişiyi üye yapmak veya üyeliğini değiştirmek için kişi sayfasındaki "Üyelik" bölümünü kullanın.</div>
    </div>

    @include('admin::partials.status')

    <ul class="nav nav-tabs mb-3">
        @foreach (\Modules\Membership\Models\Membership::STATUSES as $key => $label)
            <li class="nav-item">
                <a class="nav-link @if ($status === $key) active @endif" href="{{ route('admin.memberships', ['status' => $key]) }}">{{ $label }} <span class="badge bg-secondary-lt ms-1">{{ $counts[$key] ?? 0 }}</span></a>
            </li>
        @endforeach
    </ul>

    <form method="GET" class="mb-3 d-flex gap-2">
        <input type="hidden" name="status" value="{{ $status }}">
        <input name="q" value="{{ $search }}" class="form-control" placeholder="Üye no, ad, soyad veya e-posta">
        <button type="submit" class="btn btn-primary">Ara</button>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table" data-no-datatable>
                <thead><tr><th>Üye no</th><th>Ad soyad</th><th>Katılma</th><th>Durum</th></tr></thead>
                <tbody>
                    @forelse ($memberships as $membership)
                        <tr>
                            <td><code>{{ $membership->number ?? '—' }}</code></td>
                            <td>
                                @if (Auth::user()->hasPermission('contacts.view') && ! $membership->contact->trashed())
                                    <a href="{{ route('admin.contacts.show', $membership->contact) }}#membership">{{ $membership->contact->display_name }}</a>
                                @else
                                    {{ $membership->contact->display_name }}
                                @endif
                            </td>
                            <td class="text-secondary">{{ $membership->joined_at?->format('d.m.Y') ?? '—' }}</td>
                            <td><span class="badge bg-{{ \Modules\Membership\Models\Membership::STATUS_COLORS[$membership->status] }}-lt">{{ $membership->statusLabel() }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-secondary py-4">Kayıt yok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($memberships->hasPages())
            <div class="card-footer">{{ $memberships->links('pagination::bootstrap-5') }}</div>
        @endif
    </div>
</div>
@endsection
