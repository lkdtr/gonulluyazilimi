{{-- Contact tab of the profile page: account email and phone. --}}
<div class="card">
    <div class="card-header"><h3 class="card-title">İletişim</h3></div>
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-md-4">{{ trans('auth.email') }}</dt>
            <dd class="col-md-8">
                {{ $user->email }}
                @if ($user->is(Auth::user()))
                    @module('email-change')
                        <a href="{{ route('email-change-requests.create') }}" class="ms-2 small">Değiştir</a>
                    @endmodule
                @endif
            </dd>
            <dt class="col-md-4">{{ trans('auth.phone_number') }}</dt>
            <dd class="col-md-8">{{ $user->phone_number ?: '—' }}</dd>
            <dt class="col-md-4">{{ trans('auth.city') }}</dt>
            <dd class="col-md-8">{{ $user->city_id ? ($user->getCity()->city_name ?? '—') : '—' }}</dd>
        </dl>
    </div>
</div>
