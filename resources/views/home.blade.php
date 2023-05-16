@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @if($email_redirect_is_exist==null)
                <a href="{{secure_url('/email-redirects')}}"><div class="alert alert-info">{{ trans("panel.email_redirects_info") }}</div></a>
                <br/>
            @endif
            @if (session('forwarding-success'))
                <div class="alert alert-success">
                    {{ session('forwarding-success') }}
                </div>
            @endif

            <!--
            @if($events)
            <div class="card">
                <div class="card-header">{{ trans("panel.available_announcements_title") }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @foreach ($events as $event)
                        <button style="width: 100%;text-align: left;" class="btn btn-lg btn-secondary rounded-0" type="button" data-bs-toggle="collapse" href="#eventCollapse{{$event->id}}" role="button" aria-expanded="false" aria-controls="eventCollapse{{$event->id}}">
                            {{$event->event_name}} ({{date("d-m-Y", strtotime($event->started_at))}} / {{date("d-m-Y", strtotime($event->finished_at))}} )
                        </button>

                        <div class="collapse" id="eventCollapse{{$event->id}}">
                            <div class="card card-body text-dark bg-light rounded-0">

                                <form method="POST">
                                    @csrf
                                    <input type="hidden" name="event_id" value="{{$event->id}}">
                                    @if(isset($joined_events[$event->id]))
                                    <span class="btn btn-lg btn-primary disabled">Etklinğine kaydınız daha önce alınmış</span>
                                    @else
                                    <button class="btn btn-lg btn-primary" type="submit">Katın</button>
                                    @endif
                                </form>

                                <hr>

                                <div>{!! $event->event_detail !!}</div>
                                <p>Etkinlik konumu: {{$event->event_location}}</p>

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
            -->

        </div>
    </div>
</div>
@endsection
