@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">{{ trans("panel.email_forwarding_title") }}</div>
                <div class="card-body">

                    <form method="POST" action="{{ route('email-forwarding') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ trans("auth.alias") }}</label>

                            <div class="col-md-6">

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="email_alias" id="alias0" value="{{$user->name}}.{{$user->surname}}@penguen.org.tr" @if($email_redirects->email_alias == $user->name.".".$user->surname."@penguen.org.tr") checked @endif>
                                    <label class="form-check-label" for="alias0">
                                        {{$user->name}}.{{$user->surname}}@penguen.org.tr
                                    </label>
                                </div>

                                @php($count=0)
                                @foreach ($name_array as $name)
                                    @foreach ($surname_array as $surname)
                                        @php($count++)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="email_alias" id="alias{{$count}}" value="{{$name}}.{{$surname}}@penguen.org.tr" @if($email_redirects->email_alias == $name.".".$surname."@penguen.org.tr") checked @endif >
                                            <label class="form-check-label" for="alias{{$count}}">
                                                {{$name}}.{{$surname}}@penguen.org.tr
                                            </label>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>

                        <div class="separator bottom"><br></div>

                        <div class="row mb-3">
                            <label for="forwarding" class="col-md-4 col-form-label text-md-end">{{ trans("auth.forwarding") }}</label>

                            <div class="col-md-6">
                                <span class="form-control" >{{ $email_redirects->email_forwarding }}</span>
                            </div>
                        </div>

                        <div class="separator bottom"><br></div>

                        <div class="row">
                            <label class="col-md-8 offset-md-4 mb-3" for="agreement"><input name="agreement" id="agreement" value="true" type="checkbox" required> &nbsp; <a href="javascript:openModal('/email-agreement')"> E-Posta Kullanım Sözleşmesi</a>'nin koşullarını kabul ediyorum</label>
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



<div class="modal" id="modal-iframe" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document" style="border: 1px solid #ccc;">
    <div class="modal-content">
      <div class="modal-body mb-0 p-0">
          <iframe frameborder="0"  style="border:0; width:100%; height: 500px;" id="iframe-content" src="about:blank" ></iframe>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-warning"  onclick="$('#modal-iframe').hide();">Kapat</button>
      </div>
    </div>
  </div>
</div>

<script>
function isMobile() {
    if ('maxTouchPoints' in navigator) return navigator.maxTouchPoints > 0;

    const mQ = matchMedia?.('(pointer:coarse)');
    if (mQ?.media === '(pointer:coarse)') return !!mQ.matches;

    if ('orientation' in window) return true;

    return /\b(BlackBerry|webOS|iPhone|IEMobile)\b/i.test(navigator.userAgent) ||
        /\b(Android|Windows Phone|iPad|iPod)\b/i.test(navigator.userAgent);
}

function openModal(url) {
    if (isMobile()) {
        window.open(url);
    }
    else {
        $("#modal-iframe").show();
        $("#iframe-content").attr("src", url + "?iframe");
    }
}

</script>


@endsection
