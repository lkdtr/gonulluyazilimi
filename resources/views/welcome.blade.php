@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-primary">
                <div class="card-header text-white bg-primary">Linux Kullanıcıları Derneği Gönüllü Sistemi Nedir?</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif


                    <ul>
                        <li>Linux Kullanıcıları Derneği ile ilgili duyuruları e-posta ile alabilirsiniz.</li>
                        <li>Sisteme üye olup bilgilerini doğrulayan herkese ad.soyad@penguen.org.tr uzantılı eposta yönlendirmesi alabilirler.</li>
                        <li>Linux Kullanıcıları Derneği Referans ile üye kabul etmektedir. LKD Gönüllüsü olarak sistem üzerinden talepte bulunup daha hızlı referans bulabilirsiniz.</li>
                        <li>Dernek etkinliklerinde organizasyon sürecinde görev alabilirsiniz.</li>
                        <li>Derneğimizin üniveresite oluşumu olan LKD Genç'e katılabilir. Üniveresitenizde Linux ve Özgür Yazılım Topluluğu kurmak konusunda destek alabilirsiniz.</li>
                        <li>Üniversitenizde seminer verilmesi için talepte bulunabilirsiniz.</li>
                    </ul>

                    <p>Şimdi Linux Kullanıcıları Derneği Gönüllüsü Olmak için <a href="/register">tıklayın</a>.</p>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
