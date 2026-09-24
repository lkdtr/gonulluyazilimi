@if($user->activeEmailRedirect)
    <hr style="margin: 5px; color: #999;">
    @if(Auth::user()->isOwner())
        <li><form method="POST" action="{{ route('admin.forwarding.welcome', $user) }}">@csrf<button class="dropdown-item" type="submit">{{ trans("panel.send_penguen_welcome") }}</button></form></li>
        <li><form method="POST" action="{{ route('admin.forwarding.remove', $user) }}">@csrf @method('DELETE')<button class="dropdown-item" type="submit">{{ trans("panel.remove_penguen") }}</button></form></li>
    @endif
@endif
