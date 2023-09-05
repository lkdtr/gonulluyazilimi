@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-secondary">
                <div class="card-header text-white bg-secondary">Linux Kullanıcıları Derneği Gönüllüsü Nedir?</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif


                    <ul>
                        <li>Linux Kullanıcıları Derneği ile ilgili duyuruları öncelikli olarak e-posta ile alabilirsiniz.</li>
                        <li>Sisteme üye olup bilgilerini doğrulayan herkese ad.soyad@penguen.org.tr uzantılı eposta yönlendirmesi alabilirler.</li>
                        <li>Linux Kullanıcıları Derneği Referans ile üye kabul etmektedir. LKD Gönüllüsü olarak sistem üzerinden talepte bulunup daha hızlı referans bulabilirsiniz.</li>
                        <li>Dernek etkinliklerinde organizasyon sürecinde görev alabilirsiniz.</li>
                        <li>Derneğimizin üniversite oluşumu olan LKD Genç'e katılabilir. Üniversitenizde Linux ve Özgür Yazılım Topluluğu kurmak konusunda destek alabilirsiniz.</li>
                        <li>Üniversitenizde/Kurumunuzda seminer verilmesi için talepte bulunabilirsiniz.</li>
                    </ul>

                    <p>Soru, Şikayet, Önerileriniz için <a href="mailto:gonullu@lkd.org.tr">gonullu@lkd.org.tr</a> adresine eposta gönderebilirsiniz.</p>

                    <p>Şimdi Linux Kullanıcıları Derneği Gönüllüsü Olmak için <a href="/register">tıklayın</a>.</p>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
