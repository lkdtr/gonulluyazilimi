@extends('layouts.admin')

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <h2 class="page-title">{{ trans("panel.process_logs_title") }}</h2>
        <div class="text-secondary mt-1">Kim, hangi kaydı, ne zaman değiştirdi. Değişiklik ayrıntısı olan satırlarda eski ve yeni değerler gösterilir; kimlik numaraları gizlenir.</div>
    </div>

    @include('admin::partials.status')

    <form method="GET" class="card card-body mb-3">
        <div class="row g-2">
            <div class="col-md-4">
                <input name="q" value="{{ $filters['q'] }}" class="form-control" placeholder="İşlem metninde ara">
            </div>
            <div class="col-md-2">
                <select name="type" class="form-select">
                    <option value="">Tüm işlemler</option>
                    @foreach (['create', 'change', 'delete', 'other'] as $type)
                        <option value="{{ $type }}" @selected($filters['type'] === $type)>{{ trans('panel.process_type_'.$type) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="subject" class="form-select">
                    <option value="">Tüm kayıtlar</option>
                    @foreach (\App\Support\Audit::SUBJECTS as $class => $label)
                        <option value="{{ $class }}" @selected($filters['subject'] === $class)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><input type="date" name="from" value="{{ $filters['from']?->format('Y-m-d') }}" class="form-control" title="Başlangıç"></div>
            <div class="col-md-2"><input type="date" name="to" value="{{ $filters['to']?->format('Y-m-d') }}" class="form-control" title="Bitiş"></div>
        </div>
        <div class="d-flex gap-2 mt-2 align-items-center">
            @if ($filters['subject_id'])<input type="hidden" name="subject_id" value="{{ $filters['subject_id'] }}"><span class="badge bg-blue-lt">Kayıt #{{ $filters['subject_id'] }}</span>@endif
            @if ($byUser)<input type="hidden" name="by" value="{{ $byUser->id }}"><span class="badge bg-blue-lt">{{ $byUser->name }} {{ $byUser->surname }}</span>@endif
            <button type="submit" class="btn btn-primary ms-auto">Filtrele</button>
            <a href="{{ route('admin.process-logs') }}" class="btn btn-ghost-secondary">Temizle</a>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table" data-no-datatable>
                <thead>
                    <tr>
                        <th>Zaman</th>
                        <th>İşlem</th>
                        <th>Kayıt</th>
                        <th>Yapan</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        @php($user = $users[$log->process_by] ?? null)
                        <tr>
                            <td class="text-secondary text-nowrap">{{ $log->created_at?->format('d.m.Y H:i:s') }}</td>
                            <td>
                                <span class="badge bg-{{ ['create' => 'green', 'change' => 'blue', 'delete' => 'red'][$log->process_type] ?? 'secondary' }}-lt me-1">{{ trans('panel.process_type_'.$log->process_type) }}</span>
                                {{ $log->process }}
                                @if ($log->changes)
                                    <details class="mt-1">
                                        <summary class="small text-secondary">{{ count($log->changes) }} alan</summary>
                                        <table class="table table-sm mb-0 mt-1 small">
                                            @foreach ($log->changes as $field => [$old, $new])
                                                <tr>
                                                    <td class="text-secondary"><code>{{ $field }}</code></td>
                                                    <td class="text-danger text-break">{{ $old === null ? '—' : (is_bool($old) ? ($old ? 'evet' : 'hayır') : $old) }}</td>
                                                    <td class="text-success text-break">{{ $new === null ? '—' : (is_bool($new) ? ($new ? 'evet' : 'hayır') : $new) }}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </details>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                @if ($log->subject_type)
                                    <a href="{{ route('admin.process-logs', ['subject' => $log->subject_type, 'subject_id' => $log->subject_id]) }}" class="text-reset">{{ \App\Support\Audit::subjectLabel($log->subject_type) }} #{{ $log->subject_id }}</a>
                                @else
                                    <span class="text-secondary">—</span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                @if ($user)
                                    <a href="{{ route('admin.process-logs', ['by' => $user->id]) }}" class="text-reset">{{ $user->name }} {{ $user->surname }}</a>
                                @else
                                    <span class="text-secondary">{{ $log->process_by ? 'Silinmiş hesap' : 'Sistem' }}</span>
                                @endif
                            </td>
                            <td class="text-secondary"><code>{{ $log->request_ip ?? '—' }}</code></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary py-4">Kayıt yok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($logs->hasPages())
            <div class="card-footer">{{ $logs->links('pagination::bootstrap-5') }}</div>
        @endif
    </div>
</div>
@endsection
