<footer class="footer footer-transparent d-print-none">
    <div class="container-xl">
        <div class="row text-center align-items-center">
            @if ($organization->get('contact_email'))
                <div class="col-12 col-lg-auto mt-3 mt-lg-0 text-secondary">
                    Soru, şikâyet ve önerileriniz için <a href="mailto:{{ $organization->get('contact_email') }}">{{ $organization->get('contact_email') }}</a>
                </div>
            @endif
            @if ($socials = $organization->socialLinks())
                <div class="col-12 col-lg-auto mt-2 mt-lg-0">
                    @foreach ($socials as $social)
                        <a href="{{ $social['url'] }}" class="link-secondary me-2" target="_blank" rel="noopener" title="{{ $social['label'] }}" aria-label="{{ $social['label'] }}"><i class="ti ti-{{ $social['icon'] }} icon"></i></a>
                    @endforeach
                </div>
            @endif
            <div class="col-lg-auto ms-lg-auto text-secondary">
                {{ $organization->name() }} portalı özgür yazılımdır. <a href="{{ $organization->sourceUrl() }}">Kaynak kodu</a>
            </div>
        </div>
    </div>
</footer>
