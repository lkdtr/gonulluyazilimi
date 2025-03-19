@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-secondary">
                <div class="card-header text-white bg-secondary">{{ trans("panel.users_title") }}</div>

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
                                <th>{{ trans("auth.national_id") }}</th>
                                <th>{{ trans("auth.birthday") }}</th>
                                <th>{{ trans("auth.email") }}</th>
                                <th>{{ trans("auth.phone_number") }}</th>
                                <th>{{ trans("auth.city") }}</th>
                                <th>{{ trans("panel.user_role") }}</th>
                                <th>{{ trans("auth.alias") }}</th>
                                <th>{{ trans("panel.created_at") }}</th>
                                <th>{{ trans("panel.updated_at") }}</th>
                                <th>{{ trans("panel.processes") }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                            <tr>
                                <td>{{$user->id}}</td>
                                <td>{{$user->name}}</td>
                                <td>{{$user->surname}}</td>
                                <td>{{$user->national_id}}</td>
                                <td>@if($user->birthday!="") {{ date("d-m-Y", strtotime($user->birthday)) }} @endif</td>
                                <td>{{$user->email}}</td>
                                <td>{{$user->phone_number}} @if($user->getValidation()->verified) <svg style="height: 16px;width: 16px;" class="bi flex-shrink-0 me-2" role="img"><use xlink:href="#check-fill"/></svg> @endif</td>
                                <td>@if(isset($user->getCity()->city_name)) {{$user->getCity()->city_name}} @endif</td>
                                <td>{{trans("panel.user_".$user->role)}} @if($user->lkd_user_id>0) ({{trans("panel.lkd_user")}}) @endif</td>
                                <td>{{$user->getEmailRedirects()->email_alias}}</td>
                                <td>{{$user->created_at->format('d-m-Y H:i')}}</td>
                                <td>{{$user->updated_at->format('d-m-Y H:i')}}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ trans("panel.processes") }}
                                        </button>
                                        <ul class="dropdown-menu pull-left">
                                            <li><a class="dropdown-item" href="{{secure_url('/user-infos/'.$user->id)}}">{{ trans("panel.user_infos") }}</a></li>

                                            @if($user->getEmailRedirects()->email_alias!="")
                                                <hr style="margin: 5px; color: #999;">
                                                <li><a class="dropdown-item" href="{{secure_url('/send-penguen-welcome/'.$user->id)}}">{{ trans("panel.send_penguen_welcome") }}</a></li>
                                                <li><a class="dropdown-item" href="{{secure_url('/remove-penguen/'.$user->id)}}">{{ trans("panel.remove_penguen") }}</a></li>
                                            @endif

                                            @if( Auth::user()->role==1)
                                            <hr style="margin: 5px; color: #999;">
                                            <li><a class="dropdown-item" href="{{secure_url('/set-owner-role/'.$user->id)}}">{{ trans("panel.set_owner_role") }}</a></li>
                                            <li><a class="dropdown-item" href="{{secure_url('/set-manager-role/'.$user->id)}}">{{ trans("panel.set_manager_role") }}</a></li>
                                            <li><a class="dropdown-item" href="{{secure_url('/set-user-role/'.$user->id)}}">{{ trans("panel.set_user_role") }}</a></li>
                                            <hr style="margin: 5px; color: #999;">
                                            <li><a class="dropdown-item" href="{{secure_url('/remove-user/'.$user->id)}}">{{ trans("panel.remove_user") }}</a></li>
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
