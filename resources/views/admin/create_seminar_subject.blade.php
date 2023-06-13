@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ trans("panel.create_seminar_subject_title") }}</div>
                <div class="card-body">

                    @if (session('status'))
                        <div class="alert alert-success d-flex" role="alert">
                            <svg style="height: 20px;width: 20px;" class="bi flex-shrink-0 me-2" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                            <div>{{ session('status') }}</div>
                        </div>
                    @endif

                    <form method="POST" action="">
                    @csrf

                        <div class="row mb-3">
                            <label for="subject" class="col-md-2 col-form-label text-md-end">
                                {{ trans("panel.subject") }}
                            </label>
                            <div class="col-md-8">
                                <input id="subject" type="text" class="form-control @error('subject') is-invalid @enderror" name="subject" value="" required autofocus>

                                @error('subject')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="subject" class="col-md-2 col-form-label text-md-end">
                                {{ trans("panel.type") }}
                            </label>
                            <div class="col-md-8">
                                <select id="type" name="type" class="form-control form-select" style="width: 100%; border: 1px solid #004153;" required>
                                    <option value="awareness">{{ trans("panel.type_awareness") }}</option>
                                    <option value="education">{{ trans("panel.type_education") }}</option>
                                </select>

                                @error('type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="summary" class="col-md-2 col-form-label text-md-end">
                                {{ trans("panel.summary") }}
                            </label>
                            <div class="col-md-8">
                                <textarea id="summary" class="form-control @error('summary') is-invalid @enderror" name="summary" autofocus required></textarea>

                                @error('subject')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="syllabus" class="col-md-2 col-form-label text-md-end">
                                {{ trans("panel.syllabus") }}
                            </label>
                            <div class="col-md-8">
                                <textarea id="syllabus" class="wysiwyg form-control @error('syllabus') is-invalid @enderror" name="syllabus" autofocus></textarea>

                                @error('syllabus')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="subject" class="col-md-2 col-form-label text-md-end">
                                {{ trans("panel.duration") }} ({{ trans("panel.hour") }})
                            </label>
                            <div class="col-md-8">
                                <input id="duration" type="number" class="form-control @error('duration') is-invalid @enderror" name="duration" value="" required autofocus>

                                @error('duration')
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
