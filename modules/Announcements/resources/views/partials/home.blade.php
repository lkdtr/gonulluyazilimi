<div class="card border-secondary">
    <div class="card-header text-white bg-secondary">{{ trans("panel.available_announcements_title") }}</div>

    <div class="card-body">
        @foreach ($announcements as $announcement)
            <button style="width: 100%;text-align: left;" class="btn btn-lg btn-success rounded-0" type="button" data-bs-toggle="collapse" href="#announcementCollapse{{$announcement->id}}" role="button" aria-expanded="false" aria-controls="announcementCollapse{{$announcement->id}}">
                {{$announcement->subject}}
            </button>
            <div class="collapse" id="announcementCollapse{{$announcement->id}}">
                <div class="card card-body text-dark bg-light rounded-0">
                    <div>{!! app(\App\Support\HtmlSanitizer::class)->sanitize($announcement->detail) !!}</div>
                </div>
            </div>
            <small>&nbsp;</small>
        @endforeach

        @if ($announcements->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $announcements->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
