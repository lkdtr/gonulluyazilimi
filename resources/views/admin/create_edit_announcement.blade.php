@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-secondary">
                <div class="card-header text-white bg-secondary">{{ trans("panel.new_announcement") }}</div>
                <div class="card-body">

                    @if (session('status'))
                        <div class="alert alert-success d-flex" role="alert">
                            <svg style="height: 20px;width: 20px;" class="bi flex-shrink-0 me-2" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                            <div>{{ session('status') }}</div>
                        </div>
                    @endif

                        <form method="POST">
                            @csrf

                            <div class="row mb-3">
                                <label for="subject" class="col-md-2 col-form-label text-md-end">
                                    {{ trans("panel.announcement_title") }}
                                </label>
                                <div class="col-md-8">
                                    <input id="subject" type="text" class="form-control @error('subject') is-invalid @enderror" name="subject" value="{{$announcement->subject}}" required autofocus>

                                    @error('subject')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>




                            <div class="row mb-3">
                                <label for="detail" class="col-md-2 col-form-label text-md-end">
                                    {{ trans("panel.announcement_detail") }}
                                </label>
                                <div class="col-md-8">
                                    <textarea id="detail" class="wysiwyg form-control @error('detail') is-invalid @enderror" name="detail" autofocus>{{$announcement->detail}}</textarea>

                                    @error('detail')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="started_at" class="col-md-2 col-form-label text-md-end">
                                    {{ trans("panel.announcement_started_at") }}
                                </label>
                                <div class="col-md-8">
                                    <input type="datetime-local" id="started_at" class="form-control @error('started_at') is-invalid @enderror" name="started_at" value="{{$announcement->started_at}}">

                                    @error('started_at')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="row mb-3">
                                <label for="started_at" class="col-md-2 col-form-label text-md-end">
                                    {{ trans("panel.announcement_finished_at") }}
                                </label>
                                <div class="col-md-8">
                                    <input type="datetime-local" id="finished_at" class="form-control @error('finished_at') is-invalid @enderror" name="finished_at" value="{{$announcement->finished_at}}">

                                    @error('finished_at')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="status" class="col-md-2 col-form-label text-md-end">
                                    {{ trans("panel.status") }}
                                </label>
                                <div class="col-md-8 mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="status_1" name="status" autofocus {{ $announcement->status == 1 ? 'checked' : '' }} value="1">
                                        <label class="form-check-label" for="status_1">{{ trans("panel.active") }}</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="status_0" name="status" autofocus {{ $announcement->status == 0 ? 'checked' : '' }} value="0">
                                        <label class="form-check-label" for="status_0">{{ trans("panel.passive") }}</label>
                                    </div>

                                    @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="status" class="col-md-2 col-form-label text-md-end">
                                </label>
                                <div class="col-md-8 mt-2">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label" for="is_send_email">{{ trans("panel.send_email") }}</label>
                                        <input class="form-check-input" type="checkbox" id="is_send_email" name="is_send_email" autofocus {{ $announcement->send_mailing == 1 ? 'checked' : '' }}>
                                    </div>

                                    @error('send_mailing')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="separator bottom"><br></div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button id="save_button" type="submit" class="btn btn-lg btn-primary">
                                        {{ trans("panel.save") }}
                                    </button>
                                </div>
                            </div>

                        </form>



                </div>
            </div>
        </div>
    </div>
</div>
@endsection
