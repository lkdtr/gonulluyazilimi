@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ trans("auth.become_a_volunteer_title") }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}" id="register_form">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ trans("auth.name") }} <span style="color:red">*</span></label>

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
                            <label for="surname" class="col-md-4 col-form-label text-md-end">{{ trans("auth.surname") }} <span style="color:red">*</span></label>

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
                            <label for="national_id" class="col-md-4 col-form-label text-md-end">{{ trans("auth.national_id") }} <span style="color:red">*</span></label>

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
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ trans("auth.email") }} <span style="color:red">*</span></label>

                            <div class="col-md-6">
                                <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="phone_number" class="col-md-4 col-form-label text-md-end">{{ trans("auth.phone_number") }} <span style="color:red">*</span></label>

                            <div class="col-md-6" id="phone_number_block">
                                <input id="phone_number" type="text" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number') }}" required autocomplete="phone_number">
                                <span style="display: none" id="label_phone_number" class="form-control" >Telefon numaranıza gönderilen doğrulama kodunu, alt kısma yazın</span>
                                <input id="hidden_phone_number" type="hidden" name="phone_number">

                                @error('phone_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3" id="phone_number_validation_block" style="display: none">
                            <label for="phone_number_validation" class="col-md-4 col-form-label text-md-end">{{ trans("auth.phone_number_validation") }}</label>

                            <div class="col-md-3">
                                <input id="phone_number_validation" type="text" class="form-control" name="phone_number_validation">
                            </div>
                            <div class="col-md-3">
                                <button type="button" id="validate_button" class="btn btn-secondary">{{ trans("auth.validate") }}</button>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ trans("auth.password") }} <span style="color:red">*</span></label>

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
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ trans("auth.password_confirm") }} <span style="color:red">*</span></label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="separator bottom"><br></div>

                        <div class="row">
                            <label class="col-md-8 offset-md-4 mb-3" for="agreement"><input name="agreement" id="agreement" value="true" type="checkbox" required> &nbsp; <a href="javascript:openModal('/user-agreement')"> Kişisel Verilerin Korunması ve İşlenmesi Politikası</a> koşullarını kabul ediyorum</label>
                        </div>

                        <div class="separator bottom"><br></div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button id="register_button" type="submit" class="btn btn-lg btn-primary" disabled="">
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
