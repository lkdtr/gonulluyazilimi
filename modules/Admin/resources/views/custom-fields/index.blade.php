@extends('layouts.admin')

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Özel alanlar</h2>
                <div class="text-secondary mt-1">Derneğinize özgü kişi bilgileri (ör. takma ad, işyeri adresi). Kişi sayfasında gösterilir; erişim ayarına göre kişi kendi profilinde görür veya düzenler. Kimlik kartına da basılabilir.</div>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.custom-fields.create') }}" class="btn btn-primary"><i class="ti ti-plus icon"></i> Alan ekle</a>
            </div>
        </div>
    </div>

    @include('admin::partials.status')

    @forelse ($groups as $group => $groupLabel)
        @continue(! isset($fields[$group]))
        <div class="card mb-3">
            <div class="card-header"><h3 class="card-title">{{ $groupLabel }}</h3></div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table" data-no-datatable>
                    <thead><tr><th>Sıra</th><th>Alan</th><th>Tür</th><th>Kişinin erişimi</th><th>Değer</th><th class="w-1"></th></tr></thead>
                    <tbody>
                        @foreach ($fields[$group] as $field)
                            <tr @class(['text-secondary' => ! $field->is_active])>
                                <td>{{ $field->sort }}</td>
                                <td>
                                    {{ $field->label }}
                                    @if ($field->is_required)<span class="text-danger" title="Zorunlu">*</span>@endif
                                    @unless ($field->is_active)<span class="badge bg-secondary-lt ms-1">Kapalı</span>@endunless
                                    <div class="small text-secondary"><code>{{ $field->key }}</code> · {{ \App\Models\CustomField::APPLIES_TO[$field->applies_to] }}</div>
                                </td>
                                <td>{{ \App\Models\CustomField::TYPES[$field->type] ?? $field->type }}</td>
                                <td>{{ \App\Models\CustomField::ACCESS[$field->member_access] }}</td>
                                <td>{{ $field->values_count }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('admin.custom-fields.edit', $field) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
    @endforelse

    @if ($fields->isEmpty())
        <div class="card"><div class="empty"><p class="empty-title">Henüz özel alan yok</p></div></div>
    @endif
</div>
@endsection
