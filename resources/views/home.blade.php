@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ trans("panel.available_events_title") }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @foreach ($events as $event)
                        <h3>{{$event->event_name}} ({{date("d-m-Y", strtotime($event->started_at))}} / {{date("d-m-Y", strtotime($event->finished_at))}} )</h3>
                        <div>{!! $event->event_detail !!}</div>
                        <p>Etkinlik konumu: {{$event->event_location}}</p>
                        <hr>
                        <form method="POST">
                            @csrf
                            <input type="hidden" name="event_id" value="{{$event->id}}">
                            @if(isset($joined_events[$event->id]))
                            <span class="btn btn-lg btn-primary disabled">Etklinğine kaydınız daha önce alınmış</span>
                            @else
                            <button class="btn btn-lg btn-primary" type="submit">Katın</button>
                            @endif
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
