@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ trans("panel.users_title") }}</div>

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
                                <th>{{ trans("auth.name") }}</th>
                                <th>{{ trans("auth.surname") }}</th>
                                <th>{{ trans("auth.email") }}</th>
                                <th>{{ trans("auth.phone_number") }}</th>
                                <th>{{ trans("panel.user_role") }}</th>
                                <th>{{ trans("auth.alias") }}</th>
                                <th>{{ trans("panel.processes") }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                            <tr>
                                <td>{{$user->id}}</td>
                                <td>{{$user->name}}</td>
                                <td>{{$user->surname}}</td>
                                <td>{{$user->email}}</td>
                                <td>{{$user->phone_number}}</td>
                                <td>{{trans("panel.user_".$user->role)}}</td>
                                <td>{{$user->getEmailRedirects()->email_alias}}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ trans("panel.processes") }}
                                        </button>
                                        <ul class="dropdown-menu pull-left">
                                            <li><a class="dropdown-item" href="{{secure_url('/user-infos/'.$user->id)}}">{{ trans("panel.user_infos") }}</a></li>

                                            @if( Auth::user()->role==1)
                                            <hr>
                                            <li><a class="dropdown-item" href="{{secure_url('/set-owner-role/'.$user->id)}}">{{ trans("panel.set_owner_role") }}</a></li>
                                            <li><a class="dropdown-item" href="{{secure_url('/set-manager-role/'.$user->id)}}">{{ trans("panel.set_manager_role") }}</a></li>
                                            <li><a class="dropdown-item" href="{{secure_url('/set-user-role/'.$user->id)}}">{{ trans("panel.set_user_role") }}</a></li>
                                            @endif

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
