<li class="nav-item dropdown">
    <a id="accountDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
        {{ Auth::user()->name }} {{ Auth::user()->surname }}
    </a>
    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
        <a class="dropdown-item" href="{{ route('my-infos') }}">
            {{trans('panel.my_infos')}}
        </a>
        <a class="dropdown-item" href="{{ route('password.change.edit') }}">Parola değiştir</a>
        <hr style="margin: 5px; color: #999;">
        <a class="dropdown-item" href="{{ route('logout') }}"
           onclick="event.preventDefault();
                         document.getElementById('logout-form').submit();">
            {{ trans("auth.logout") }}
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</li>
