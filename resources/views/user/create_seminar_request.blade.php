@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-secondary">
                <div class="card-header text-white bg-secondary">{{ trans("panel.create_seminar_request_title") }}</div>
                <div class="card-body">

                    @if (session('status'))
                        <div class="alert alert-success d-flex" role="alert">
                            <svg style="height: 20px;width: 20px;" class="bi flex-shrink-0 me-2" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                            <div>{{ session('status') }}</div>
                        </div>
                    @endif

                    <p>Seminer konusu seçin</p>
                    <p>Seminer tarih aralığı seçin</p>
                    <p>Seminer verilecek kurum/kuruluş</p>
                    <p>Seminer Verilecek adres, adres, ilçe, il</p>
                    <p>İletişim bilgileri
                        <ul>
                            <li>Ad Soyad</li>
                            <li>Eposta</li>
                            <li>Telefon</li>
                        </ul>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
