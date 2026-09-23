{{-- Horizontal menu of a Menu section: single-item groups are links, larger groups dropdowns. --}}
@foreach (app(\App\Modules\Menu::class)->groups($section, Auth::user()) as $group)
    @php($active = collect($group['items'])->contains(fn ($item) => request()->routeIs($item['route'])))
    @if (count($group['items']) === 1)
        <li class="nav-item">
            <a class="nav-link @if($active) active @endif" href="{{ route($group['items'][0]['route']) }}">{{ __($group['items'][0]['label']) }}</a>
        </li>
    @else
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle @if($active) active @endif" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">{{ __($group['label']) }}</a>
            <ul class="dropdown-menu">
                @foreach ($group['items'] as $item)
                    <li><a class="dropdown-item @if(request()->routeIs($item['route'])) active @endif" href="{{ route($item['route']) }}">{{ __($item['label']) }}</a></li>
                @endforeach
            </ul>
        </li>
    @endif
@endforeach
