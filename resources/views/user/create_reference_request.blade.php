@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card border-primary">
                <div class="card-header text-white bg-primary">{{ trans("panel.create_reference_request_title") }}</div>
                <div class="card-body">

                    @if($user->lkd_user_id > 0)
                        <div class="alert alert-warning d-flex">
                            <svg style="height: 20px;width: 20px;" class="bi flex-shrink-0 me-2" role="img" aria-label="Info:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                            <div>{{ trans("panel.lkd_user_info") }}</div>
                        </div>
                    @else
                        @if($referenceRequest==null)

                            <div class="alert alert-info d-flex">
                                <svg style="height: 20px;width: 20px;" class="bi flex-shrink-0 me-2" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
                                <div>{{ trans("panel.reference_request_info_required") }}</div>
                            </div>

                            <div class="separator bottom"><br></div>

                            <form method="POST" action="{{ route('create-reference-request') }}">
                                @csrf

                                <div class="row mb-3">
                                    <label for="name" class="col-md-4 col-form-label text-md-end">{{ trans("auth.name") }}</label>

                                    <div class="col-md-6">
                                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $user->name }}" required autocomplete="name" autofocus>

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
                                        <input id="surname" type="text" class="form-control @error('surname') is-invalid @enderror" name="surname" value="{{ $user->surname }}" required autocomplete="surname" autofocus>

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
                                        <input id="national_id" type="text" class="form-control @error('national_id') is-invalid @enderror" name="national_id" value="{{ $user->national_id }}" required autocomplete="national_id" autofocus>

                                        @error('national_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="birthday" class="col-md-4 col-form-label text-md-end">{{ trans("auth.birthday") }}</label>

                                    <div class="col-md-6">
                                        <input id="birthday" type="text" class="form-control @error('birthday') is-invalid @enderror" name="birthday" value="{{ $user->birthday }}" required autocomplete="birthday" autofocus>

                                        @error('birthday')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="separator bottom"><br></div>

                                <div class="row">
                                    <label class="col-md-8 offset-md-4 mb-3" for="agreement"><input name="agreement" id="agreement" value="true" type="checkbox" required> &nbsp; <a href="javascript:openModal('/user-agreement')"> Kişisel Verilerin Korunması ve İşlenmesi Politikası</a>'nın koşullarını kabul ediyorum</label>
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

                        @else

                            <div class="alert alert-info d-flex">
                                <svg style="height: 20px;width: 20px;" class="bi flex-shrink-0 me-2" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
                                <div>{{ trans("panel.reference_request_saved_info") }}</div>
                            </div>

                        @endif
                    @endif

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
