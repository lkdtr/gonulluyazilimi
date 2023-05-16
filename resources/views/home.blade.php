@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            @if (session('forwarding-success'))
                <div class="alert alert-success d-flex" role="alert">
                    <svg style="height: 20px;width: 20px;" class="bi flex-shrink-0 me-2" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                    <div>{{ session('forwarding-success') }}</div>
                </div>
            @else
                @if($email_redirect_is_exist==null)
                    <a href="{{secure_url('/email-redirects')}}" role="alert">
                        <div class="alert alert-info d-flex">
                            <svg style="height: 20px;width: 20px;" class="bi flex-shrink-0 me-2" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
                            <div>{{ trans("panel.email_redirects_info") }}</div>
                        </div>
                    </a>
                    <br/>
                @else
                    <a href="{{secure_url('/email-redirects')}}" role="alert">
                        <div class="alert alert-info d-flex">
                            <svg style="height: 20px;width: 20px;" class="bi flex-shrink-0 me-2" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
                            <div>{{ trans("panel.email_redirects_exist_info") }}</div>
                        </div>
                    </a>
                    <br/>
                @endif
            @endif

            <div class="card">
                <div class="card-header">{{ trans("panel.available_announcements_title") }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success d-flex" role="alert">
                            <svg style="height: 20px;width: 20px;" class="bi flex-shrink-0 me-2" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                            <div>{{ session('status') }}</div>
                        </div>
                    @endif

                    @foreach ($events as $event)
                        <button style="width: 100%;text-align: left;" class="btn btn-lg btn-secondary rounded-0" type="button" data-bs-toggle="collapse" href="#eventCollapse{{$event->id}}" role="button" aria-expanded="false" aria-controls="eventCollapse{{$event->id}}">
                            {{$event->event_name}} <!-- ({{date("d-m-Y", strtotime($event->started_at))}} / {{date("d-m-Y", strtotime($event->finished_at))}} ) -->
                        </button>

                        <div class="collapse" id="eventCollapse{{$event->id}}">
                            <div class="card card-body text-dark bg-light rounded-0">

                                <!-- form method="POST">
                                    @csrf
                                    <input type="hidden" name="event_id" value="{{$event->id}}">
                                    @if(isset($joined_events[$event->id]))
                                    <span class="btn btn-lg btn-primary disabled">Etklinğine kaydınız daha önce alınmış</span>
                                    @else
                                    <button class="btn btn-lg btn-primary" type="submit">Katın</button>
                                    @endif
                                </form>

                                <hr  -->

                                <div>{!! $event->event_detail !!}</div>

                                <!-- p>Etkinlik konumu: {{$event->event_location}}</p -->

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
