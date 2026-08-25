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

                    <h2>LKD Genç</h2>

                    <p>Üniversite öğrencisiyseniz LKD Genç ağına katılabilir, kampüsünüzdeki özgür yazılım çalışmalarına destek olabilir ve üniversitenizin LKD Genç temsilcisi olmak için aday olabilirsiniz.</p>

                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-success" href="{{ route('join-lkd-young') }}">LKD Genç'e katıl</a>
                    </div>

                    <p class="mt-3 text-muted">Başvuru için giriş yapmanız gerekir. Temsilcilik adaylığı YK onayıyla değerlendirilir.</p>

                    <h2>LKD Temsilcilikleri</h2>

                    <p>Bulunduğunuz şehirdeki LKD temsilciliğiyle iletişim kurabilir, yerel çalışmalara gönüllü olarak katılabilir veya temsilci adaylığınızı iletebilirsiniz.</p>

                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-primary" href="https://www.lkd.org.tr/hakkimizda/temsilcilikler/" target="_blank" rel="noopener noreferrer">Temsilcilikleri görüntüle</a>
                        <a class="btn btn-outline-primary" href="{{ route('representations.candidate') }}">Temsilci adayı ol</a>
                    </div>

                    <p class="mt-3 text-muted">Temsilcilik iletişim paylaşımı ve adaylık işlemleri için giriş yapmanız gerekir.</p>

                    <h2>Seminer Talepleri</h2>

                    <p>Özgür yazılım, Linux ve ilgili konularda kurumunuzda seminer düzenlemek veya bilgi ve deneyiminizi toplulukla paylaşmak için aşağıdaki formlardan yararlanabilirsiniz.</p>

                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-primary" href="{{ route('create-seminar-request') }}">Seminer talebi oluştur</a>
                        <a class="btn btn-outline-primary" href="{{ route('create-seminar-offer') }}">Seminer vermek istiyorum</a>
                    </div>

                    <p class="mt-3 mb-0 text-muted">Talep oluşturmak ve seminer verme başvurusu göndermek için üyelik gereklidir.</p>



                </div>
            </div>
        </div>
    </div>
</div>
@endsection
