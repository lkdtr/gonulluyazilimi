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

                        <form method="POST" action="{{ route("new-announcement") }}">
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
                                    <input type="datetime-local" id="started_at" class="wysiwyg form-control @error('started_at') is-invalid @enderror" name="started_at" autofocus>{{$announcement->started_at}}</input>

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
                                    <input type="datetime-local" id="finished_at" class="wysiwyg form-control @error('finished_at') is-invalid @enderror" name="finished_at" autofocus>{{$announcement->finished_at}}</input>

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
                                <div class="col-md-8">
                                    <input type="radio" id="status" name="status" autofocus {{ $announcement->status == 1 ? 'checked' : '' }} value="1">Aktif</input>
                                    <input type="radio" id="status"  name="status" autofocus {{ $announcement->status == 0 ? 'checked' : '' }} value="0">Pasif</input>






                                    @error('status')
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
