@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-secondary">
                <div class="card-header text-white bg-secondary">Linux Kullanıcıları Derneği Gönüllüsü Nedir?</div>

                < class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div style="text-align: center;">
                        <img src="/images/lkd-gonullusu.png?v3" alt="Linux Kullanıcıları Derneği Gönüllüsü" style="width:100%; max-width: 450px;">
                    </div>

                    <p>Linux Kullanıcıları Derneği (LKD) Gönüllüsü, derneğin özgür yazılım ve Linux ekosistemini destekleme misyonuna katkıda bulunmak isteyen bireylerdir. Gönüllüler, dernek faaliyetlerine katılarak topluluğun büyümesine ve gelişmesine yardımcı olurlar.</p>
<h2>LKD Gönüllüsü Olmanın Avantajları</h2>
<ul>
<li>
    <strong>Öncelikli Bilgilendirme:</strong> Dernek ile ilgili duyuruları ve etkinlikleri öncelikli olarak e-posta ile alabilirsiniz.
</li>
<li>
    <strong>E-posta Yönlendirmesi:</strong> Sisteme üye olup bilgilerini doğrulayan herkese @penguen.org.tr uzantılı e-posta yönlendirmesi sağlanmaktadır.
</li>
<li>
    <strong>Üyelik İçin Referans:</strong> LKD, referans ile üye kabul etmektedir. Gönüllü olarak sistem üzerinden talepte bulunup daha hızlı referans bulabilirsiniz.
</li>
<li>
    <strong>Organizasyon Görevleri:</strong> Dernek etkinliklerinin organizasyon süreçlerinde aktif rol alabilirsiniz.
</li>
<li>
    <strong>LKD Genç Katılımı:</strong> Derneğin üniversite oluşumu olan LKD Genç'e katılabilir ve üniversitenizde Linux ve Özgür Yazılım Topluluğu kurma konusunda destek alabilirsiniz.
</li>
<li>
    <strong>Seminer Talepleri:</strong> Üniversitenizde veya kurumunuzda seminer verilmesi için talepte bulunabilirsiniz.
</li>

</ul>


<h2>Nasıl Gönüllü Olunur?</h2>

<p>Gönüllü olmak için aşağıdaki adımları izleyebilirsiniz:</p>

<ul>
    <li><strong>Kayıt Olun:</strong> <a href="/register">Gönüllü Ol</a> sayfasından gerekli bilgileri doldurarak sisteme üye olun.</li>

    <li><strong>Bilgilerinizi Doğrulayın:</strong> Üyelik sırasında verdiğiniz bilgileri doğrulayarak sisteme giriş yapın.</li>

    <li><strong>Profilinizi Tamamlayın:</strong> İlgi alanlarınızı ve yeteneklerinizi belirterek profilinizi güncelleyin.</li>

    <li><strong>Etkinliklere Katılın:</strong> Dernek tarafından düzenlenen etkinliklere katılarak topluluğa katkı sağlayın.</li>

</ul>



                </div>
            </div>
        </div>
    </div>
</div>
@endsection
