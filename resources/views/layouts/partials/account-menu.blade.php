@php($initials = mb_strtoupper(mb_substr(Auth::user()->name, 0, 1).mb_substr(Auth::user()->surname, 0, 1)))
<div class="nav-item dropdown">
    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="{{ trans('panel.my_infos') }}">
        <span class="avatar avatar-sm bg-primary-lt">{{ $initials }}</span>
        <div class="d-none d-xl-block ps-2">
            <div>{{ Auth::user()->name }} {{ Auth::user()->surname }}</div>
            <div class="mt-1 small text-secondary">{{ trans('panel.user_'.Auth::user()->role) }}</div>
        </div>
    </a>
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
        <a class="dropdown-item" href="{{ route('my-infos') }}"><i class="ti ti-user icon dropdown-item-icon"></i>{{ trans('panel.my_infos') }}</a>
        <a class="dropdown-item" href="{{ route('password.change.edit') }}"><i class="ti ti-lock icon dropdown-item-icon"></i>Parola değiştir</a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="ti ti-logout icon dropdown-item-icon"></i>{{ trans('auth.logout') }}</a>
        @once
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        @endonce
    </div>
</div>
