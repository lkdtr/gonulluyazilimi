<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="ti ti-speakerphone icon me-2 text-primary"></i>{{ trans("panel.available_announcements_title") }}</h3>
    </div>

    @if ($announcements->isEmpty())
        <div class="card-body text-secondary">Şu anda yayında duyuru yok.</div>
    @else
        <div class="accordion accordion-flush" id="announcements">
            @foreach ($announcements as $announcement)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="announcementHeading{{ $announcement->id }}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#announcementCollapse{{ $announcement->id }}" aria-expanded="false" aria-controls="announcementCollapse{{ $announcement->id }}">
                            <span class="flex-fill">{{ $announcement->subject }}</span>
                            <span class="text-secondary small me-3">{{ $announcement->created_at?->format('d.m.Y') }}</span>
                            <div class="accordion-button-toggle"><i class="ti ti-chevron-down icon"></i></div>
                        </button>
                    </h2>
                    <div id="announcementCollapse{{ $announcement->id }}" class="accordion-collapse collapse" aria-labelledby="announcementHeading{{ $announcement->id }}" data-bs-parent="#announcements">
                        <div class="accordion-body">
                            {!! app(\App\Support\HtmlSanitizer::class)->sanitize($announcement->detail) !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if ($announcements->hasPages())
        <div class="card-footer d-flex justify-content-center">
            {{ $announcements->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
