@extends('layouts.admin')

@section('content')
@php($memberContacts = \App\Models\ContactAffiliation::active()->ofType('member')->pluck('contact_id')->flip())
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h3 class="card-title">{{ trans("panel.users_title") }}</h3></div>

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
                                @moduleSlot('admin.users.head')
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
                                <td>{{trans("panel.user_".$user->accessLevel())}} @if(isset($memberContacts[$user->contact_id])) ({{trans("panel.lkd_user")}}) @endif</td>
                                @moduleSlot('admin.users.cell', ['user' => $user])
                                <td>{{$user->created_at->format('d-m-Y H:i')}}</td>
                                <td>{{$user->updated_at->format('d-m-Y H:i')}}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ trans("panel.processes") }}
                                        </button>
                                        <ul class="dropdown-menu pull-left">
                                            <li><a class="dropdown-item" href="{{ route('admin.users.show', $user->id) }}">{{ trans("panel.user_infos") }}</a></li>
                                            @if(Auth::user()->isOwner())
                                                <li><form method="POST" action="{{ route('admin.users.tc-kimlik', $user) }}">@csrf<button class="dropdown-item" type="submit">TC Kimlik Doğru mu?</button></form></li>
                                            @endif

                                            @moduleSlot('admin.users.actions', ['user' => $user])

                                            @if( Auth::user()->isOwner())
                                            <hr style="margin: 5px; color: #999;">
                                            <li><form method="POST" action="{{ route('admin.users.owner-role', $user) }}">@csrf @method('PATCH')<button class="dropdown-item" type="submit">{{ trans("panel.set_owner_role") }}</button></form></li>
                                            <li><form method="POST" action="{{ route('admin.users.manager-role', $user) }}">@csrf @method('PATCH')<button class="dropdown-item" type="submit">{{ trans("panel.set_manager_role") }}</button></form></li>
                                            <li><form method="POST" action="{{ route('admin.users.user-role', $user) }}">@csrf @method('PATCH')<button class="dropdown-item" type="submit">{{ trans("panel.set_user_role") }}</button></form></li>
                                            <hr style="margin: 5px; color: #999;">
                                            <li><form method="POST" action="{{ route('admin.users.destroy', $user) }}">@csrf @method('DELETE')<button class="dropdown-item" type="submit">{{ trans("panel.remove_user") }}</button></form></li>
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
