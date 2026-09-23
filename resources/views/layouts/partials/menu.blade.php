{{--
    Menu of a Menu section in Tabler navbar markup.
    Single-item groups are links, larger groups dropdowns; $vertical opens the
    active group's dropdown inside the sidebar.
--}}
@php($vertical = $vertical ?? false)
@foreach (app(\App\Modules\Menu::class)->groups($section, Auth::user()) as $group)
    @php($active = collect($group['items'])->contains(fn ($item) => request()->routeIs($item['route'])))
    @if (count($group['items']) === 1)
        <li class="nav-item @if($active) active @endif">
            <a class="nav-link" href="{{ route($group['items'][0]['route']) }}">
                <span class="nav-link-icon"><i class="ti ti-{{ $group['icon'] }} icon"></i></span>
                <span class="nav-link-title">{{ __($group['items'][0]['label']) }}</span>
            </a>
        </li>
    @else
        <li class="nav-item dropdown @if($active) active @endif">
            <a class="nav-link dropdown-toggle @if($vertical && $active) show @endif" href="#" role="button" data-bs-toggle="dropdown" @if($vertical) data-bs-auto-close="false" @endif aria-expanded="{{ $vertical && $active ? 'true' : 'false' }}">
                <span class="nav-link-icon"><i class="ti ti-{{ $group['icon'] }} icon"></i></span>
                <span class="nav-link-title">{{ __($group['label']) }}</span>
            </a>
            <div class="dropdown-menu @if($vertical && $active) show @endif">
                @foreach ($group['items'] as $item)
                    <a class="dropdown-item @if(request()->routeIs($item['route'])) active @endif" href="{{ route($item['route']) }}">{{ __($item['label']) }}</a>
                @endforeach
            </div>
        </li>
    @endif
@endforeach
