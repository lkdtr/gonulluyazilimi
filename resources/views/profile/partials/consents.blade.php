{{--
    Communication consents on the profile page. $current (channel => ?bool),
    $editable: the signed-in person's own profile.
--}}
<div class="card mt-3" id="consents">
    <div class="card-header"><h3 class="card-title">İletişim izinleri</h3></div>
    <div class="card-body">
        @if (session('consent-status'))
            <div class="alert alert-success" role="alert">{{ session('consent-status') }}</div>
        @endif

        @if ($editable)
            <p class="text-secondary small">{{ $organization->name() }} sizinle hangi kanallardan iletişime geçebilir? İzinlerinizi istediğiniz zaman değiştirebilirsiniz. Hesabınızla ilgili zorunlu bildirimler (parola sıfırlama, başvuru sonuçları) bu izinlerden bağımsız gönderilir.</p>
            <form method="POST" action="{{ route('my-consents.update') }}">
                @csrf @method('PUT')
                @foreach (\App\Support\Consents::CHANNELS as $channel => $label)
                    <label class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" name="consents[{{ $channel }}]" value="1" @checked($current[$channel])>
                        <span class="form-check-label">{{ $label }}</span>
                    </label>
                @endforeach
                <button type="submit" class="btn btn-primary mt-2">İzinleri kaydet</button>
            </form>
        @else
            <ul class="list-unstyled mb-0">
                @foreach (\App\Support\Consents::CHANNELS as $channel => $label)
                    <li>
                        @if ($current[$channel] === true)
                            <i class="ti ti-circle-check icon text-success"></i>
                        @elseif ($current[$channel] === false)
                            <i class="ti ti-circle-x icon text-danger"></i>
                        @else
                            <i class="ti ti-circle-dashed icon text-secondary"></i>
                        @endif
                        {{ $label }}
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
