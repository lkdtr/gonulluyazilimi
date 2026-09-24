@extends('layouts.admin')

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Sıfatlar</h2>
                <div class="text-secondary mt-1">Kişilerin dernekle ilişkisi. Bir kişi aynı anda birden fazla sıfat taşıyabilir; rol şablonu olan sıfat, hesabına o rolleri verir.</div>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.affiliation-types.create') }}" class="btn btn-primary"><i class="ti ti-plus icon"></i> Sıfat ekle</a>
            </div>
        </div>
    </div>

    @include('admin::partials.status')

    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Sıra</th>
                        <th>Sıfat</th>
                        <th>Anahtar</th>
                        <th>Rol şablonu</th>
                        <th>Sürenler</th>
                        <th class="w-1"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($types as $type)
                        <tr>
                            <td class="text-secondary">{{ $type->sort }}</td>
                            <td>
                                {{ $type->name }}
                                @if ($type->is_system)<span class="badge bg-secondary-lt ms-1">Sistem</span>@endif
                                @if ($type->has_term)<span class="badge bg-purple-lt ms-1">Görev süreli</span>@endif
                                @if ($type->description)<div class="small text-secondary">{{ $type->description }}</div>@endif
                            </td>
                            <td><code>{{ $type->key }}</code></td>
                            <td>
                                @forelse ($type->roles as $role)
                                    <span class="badge bg-orange-lt">{{ $role->name }}</span>
                                @empty
                                    <span class="text-secondary">—</span>
                                @endforelse
                            </td>
                            <td>
                                <a href="{{ route('admin.contacts', ['affiliation' => $type->key]) }}">{{ $type->active_count }}</a>
                            </td>
                            <td class="text-nowrap">
                                <a href="{{ route('admin.affiliation-types.edit', $type) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
                                @unless ($type->is_system || $type->active_count > 0)
                                    <form method="POST" action="{{ route('admin.affiliation-types.destroy', $type) }}" class="d-inline" onsubmit="return confirm('Sıfat silinsin mi?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-ghost-danger" title="Sil"><i class="ti ti-trash icon"></i></button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
