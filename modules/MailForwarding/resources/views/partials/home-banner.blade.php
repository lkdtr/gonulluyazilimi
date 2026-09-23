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
    @elseif($email_redirect_is_exist->status==0)
        <div class="alert alert-danger d-flex" role="alert">
            <svg style="height: 20px;width: 20px;" class="bi flex-shrink-0 me-2" role="img" aria-label="Info:"><use xlink:href="#exclamation-triangle-fill"/></svg>
            <div>{{ trans("panel.remove_penguen_success") }}. <a href="{{secure_url('/email-redirects')}}">{{ trans("panel.try_reactive_penguen") }}</a></div>
        </div>
    @endif
@endif
