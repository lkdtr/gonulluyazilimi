{{-- Privacy and settings tab: consents, agreements, password, deletion. --}}
@isset($consents)
    @include('profile.partials.consents', $consents)
@endisset

@isset($acceptances)
    @include('profile.partials.agreements', ['acceptances' => $acceptances])
@endisset

@if ($user->is(Auth::user()))
    <div class="card mt-3">
        <div class="card-header"><h3 class="card-title">Parola</h3></div>
        <div class="card-body">
            <a href="{{ route('password.change.edit') }}" class="btn btn-outline-primary"><i class="ti ti-lock icon"></i> Parola değiştir</a>
        </div>
    </div>
@endif

@isset($deletion)
    @include('profile.partials.deletion', $deletion)
@endisset
