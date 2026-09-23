@extends('layouts.admin')

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Roller ve yetkiler</h2>
                <div class="text-secondary mt-1">Hesaplar rolü doğrudan ya da kişinin sıfatındaki rol şablonu üzerinden alır.</div>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary"><i class="ti ti-plus icon"></i> Rol ekle</a>
            </div>
        </div>
    </div>

    @include('admin::partials.status')

    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Rol</th>
                        <th>Yetki</th>
                        <th>Doğrudan verilen hesap</th>
                        <th>Şablonu olduğu sıfatlar</th>
                        <th class="w-1"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td>
                                {{ $role->name }}
                                @if ($role->is_system)<span class="badge bg-secondary-lt ms-1">Sistem</span>@endif
                                @if ($role->description)<div class="small text-secondary">{{ $role->description }}</div>@endif
                            </td>
                            <td>{{ $role->isOwner() ? 'Tümü' : count($role->permissions()) }}</td>
                            <td>{{ $role->users_count }}</td>
                            <td>
                                @forelse ($role->affiliationTypes as $type)
                                    <span class="badge bg-blue-lt">{{ $type->name }}</span>
                                @empty
                                    <span class="text-secondary">—</span>
                                @endforelse
                            </td>
                            <td class="text-nowrap">
                                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
                                @unless ($role->is_system)
                                    <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="d-inline" onsubmit="return confirm('Rol silinsin mi? Bu rolü alan hesaplar yetkilerini kaybeder.')">
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
