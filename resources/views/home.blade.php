@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                <symbol id="check-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                </symbol>
                <symbol id="info-fill" viewBox="0 0 16 16">
                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                </symbol>
                <symbol id="exclamation-triangle-fill" viewBox="0 0 16 16">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </symbol>
            </svg>

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
