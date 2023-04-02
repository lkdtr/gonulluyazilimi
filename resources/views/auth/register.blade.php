@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ trans("auth.register_title") }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ trans("auth.name") }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="surname" class="col-md-4 col-form-label text-md-end">{{ trans("auth.surname") }}</label>

                            <div class="col-md-6">
                                <input id="surname" type="text" class="form-control @error('surname') is-invalid @enderror" name="surname" value="{{ old('surname') }}" required autocomplete="surname" autofocus>

                                @error('surname')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="national_id" class="col-md-4 col-form-label text-md-end">{{ trans("auth.national_id") }}</label>

                            <div class="col-md-6">
                                <input id="national_id" type="text" class="form-control @error('national_id') is-invalid @enderror" name="national_id" value="{{ old('national_id') }}" required autocomplete="national_id" autofocus>

                                @error('national_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ trans("auth.email") }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="phone_number" class="col-md-4 col-form-label text-md-end">{{ trans("auth.phone_number") }}</label>

                            <div class="col-md-6">
                                <input id="phone_number" type="phone_number" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ old('phone_number') }}" required autocomplete="email">

                                @error('phone_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ trans("auth.password") }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ trans("auth.password_confirm") }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="separator bottom"><br></div>

                        <div class="row">
                            <label class="col-md-8 offset-md-4 mb-3" for="user_agreement"><input name="user_agreement" id="user_agreement" value="true" type="checkbox" required> &nbsp; <a href="javascript:openModal('/user-agreement')"> Üyelik Sözleşmesinin</a> koşullarını kabul ediyorum</label>
                            <label class="col-md-8 offset-md-4 mb-3" for="kvkk_policy"><input name="kvkk_policy" id="kvkk_policy" value="true" type="checkbox" required> &nbsp; <a href="javascript:openModal('/kvkk-policy')"> Kişisel Verilerin Korunması Kanununa İlişkin, Açık Rıza Onay Metni</a> 'ni onayladım ve kabul ediyorum.</label>
                        </div>

                        <div class="separator bottom"><br></div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-lg btn-primary">
                                    {{ trans("auth.register") }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="modal-iframe" tabindex="-1" role="dialog" aria-hidden="true" style="top: 150px;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-body mb-0 p-0">
          <iframe frameborder="0"  style="border:0; width:100%; height: 500px;" id="iframe-content" src="about:blank" ></iframe>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="submit-btn"  onclick="$('#modal-iframe').hide();">Kapat</button>
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
