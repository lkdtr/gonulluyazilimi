@extends($layout ?? 'layouts.app')

@section('content')
<div class="container">
    @if (session('photo-status'))
        <div class="alert alert-success" role="alert">{{ session('photo-status') }}</div>
    @endif

    <div class="row row-cards">
        @isset($photo)
            <div class="col-lg-4">
                @include('profile.partials.photo', $photo)
            </div>
        @endisset

        <div class="{{ isset($photo) ? 'col-lg-8' : 'col-12' }}">
            @php($profileTabs = app(\App\Modules\ProfileTabs::class))
            @php($tabs = $profileTabs->all())
            @php($fieldsByTab = isset($fields) ? $fields['fields']->groupBy(fn ($field) => $profileTabs->tabForGroup($field->group)) : collect())

            <ul class="nav nav-tabs mb-3" role="tablist" data-profile-tabs>
                @foreach ($tabs as $index => $tab)
                    <li class="nav-item" role="presentation">
                        <a href="#tab-{{ $tab['key'] }}" class="nav-link @if ($index === 0) active @endif" data-bs-toggle="tab" role="tab" aria-controls="tab-{{ $tab['key'] }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                            @if ($tab['icon'])<i class="ti ti-{{ $tab['icon'] }} icon me-1"></i>@endif{{ $tab['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content">
                @foreach ($tabs as $index => $tab)
                    <div class="tab-pane @if ($index === 0) active show @endif" id="tab-{{ $tab['key'] }}" role="tabpanel">
                        @if ($tab['view'])
                            @include($tab['view'], ['user' => $user, 'contact' => $user->contact])
                        @endif

                        @if (isset($fields) && isset($fieldsByTab[$tab['key']]))
                            @include('profile.partials.fields', ['fields' => $fieldsByTab[$tab['key']], 'values' => $fields['values'], 'editable' => $fields['editable'], 'anchor' => 'fields-'.$tab['key']])
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<script>
    // Open the tab named in the address (#tab-privacy) or the tab holding the
    // anchored section (#consents after saving), and keep the chosen tab in the address.
    document.addEventListener('DOMContentLoaded', function () {
        var hash = window.location.hash;
        var target = hash && document.querySelector(hash);
        var pane = target && (target.classList.contains('tab-pane') ? target : target.closest('.tab-pane'));
        if (pane) {
            // Bootstrap's data API opens the tab on click.
            document.querySelector('[data-profile-tabs] a[href="#' + pane.id + '"]').click();
            if (target !== pane) { target.scrollIntoView(); }
        }
        document.querySelectorAll('[data-profile-tabs] a[data-bs-toggle="tab"]').forEach(function (link) {
            link.addEventListener('shown.bs.tab', function () { history.replaceState(null, '', link.getAttribute('href')); });
        });
    });
</script>
@endsection
