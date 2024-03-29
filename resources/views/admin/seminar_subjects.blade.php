@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-secondary">
                <div class="card-header text-white bg-secondary">{{ trans("panel.seminar_subjects_title") }}</div>

                <div class="card-body">
                    @if (session('success-status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success-status') }}
                        </div>
                    @endif

                    @if (session('danger-status'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('danger-status') }}
                        </div>
                    @endif

                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>{{ trans("panel.subject") }}</th>
                                <th>{{ trans("panel.type") }}</th>
                                <th>{{ trans("panel.summary") }}</th>
                                <th>{{ trans("panel.duration") }}</th>
                                <th>{{ trans("panel.created_by") }}</th>
                                <th>{{ trans("panel.updated_by") }}</th>
                                <th>{{ trans("panel.processes") }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($seminarSubjects as $seminarSubject)
                            <tr>
                                <td>{{$seminarSubject->id}}</td>
                                <td>{{$seminarSubject->subject}}</td>
                                <td>{{trans("panel.type_".$seminarSubject->type)}}</td>
                                <td>{{$seminarSubject->summary}}</td>
                                <td>{{$seminarSubject->duration}} {{ trans("panel.hour") }}</td>
                                <td>{{$seminarSubject->getCreatedBy()->name}} {{$seminarSubject->getCreatedBy()->surname}}</td>
                                <td>{{$seminarSubject->getUpdatedBy()->name}} {{$seminarSubject->getUpdatedBy()->surname}}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ trans("panel.processes") }}
                                        </button>
                                        <ul class="dropdown-menu pull-left">
                                            <li><a class="dropdown-item" href="{{secure_url('/edit-seminar-subject/'.$seminarSubject->id)}}">{{ trans("panel.edit") }}</a></li>
                                            <li><a class="dropdown-item" href="{{secure_url('/delete-seminar-subject/'.$seminarSubject->id)}}">{{ trans("panel.delete") }}</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>



                </div>
            </div>
        </div>
    </div>
</div>
@endsection
