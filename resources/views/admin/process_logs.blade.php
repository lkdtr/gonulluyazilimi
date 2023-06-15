@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ trans("panel.process_logs_title") }}</div>

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
                                <th>{{ trans("panel.process_type") }}</th>
                                <th>{{ trans("panel.process") }}</th>
                                <th>{{ trans("panel.process_by") }}</th>
                                <th>{{ trans("panel.process_at") }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($processLogs as $processLog)
                            <tr>
                                <td>{{$processLog->id}}</td>
                                <td>{{trans("panel.process_type_".$processLog->process_type)}}</td>
                                <td>{{$processLog->process}}</td>
                                <td>{{$processLog->getProcessBy()->name}} {{$processLog->getProcessBy()->surname}}</td>
                                <td>{{date("d-m-Y H:i:s", strtotime($processLog->created_at))}}</td>
                            @endforeach
                        </tbody>
                    </table>



                </div>
            </div>
        </div>
    </div>
</div>
@endsection
