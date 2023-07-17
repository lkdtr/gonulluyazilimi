@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ trans("panel.join_lkd_young_title") }}</div>
                <div class="card-body">

                    @if (session('status'))
                        <div class="alert alert-success d-flex" role="alert">
                            <svg style="height: 20px;width: 20px;" class="bi flex-shrink-0 me-2" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                            <div>{{ session('status') }}</div>
                        </div>
                    @endif


                    <h3><strong>LKD GENÇ OLMA ŞARTI NEDİR?</strong></h3>

                    <p>LKD Genç olmak i&ccedil;in tek şart var; &ldquo;&uuml;niversite &ouml;ğrencisi olmak. Hazırlık okuyor olabilirsiniz veya 4. sınıf &ouml;ğrencisi olabilirsiniz. Hi&ccedil; fark etmez. &ouml;nlisans, lisans veya lisans&uuml;st&uuml; (y&uuml;ksek lisans, doktora) &ouml;ğrencisi olmanız da sorun değil. &Uuml;niversite &ouml;ğrencisiyseniz ve Linux Kullanıcıları Derneği&rsquo;nin bir par&ccedil;ası olmak istiyorsanız &ldquo;LKD Genç&rdquo; olabilirsiniz.</p>

                    <h3><strong>LKD GENÇ'İN SORUMLULUKLARI NELERDİR?</strong></h3>

                    <p>LKD Genç olmanız i&ccedil;in aktif bir &ouml;ğrenci olmak ve &uuml;niversitede herhangi bir kul&uuml;be bağlı olmak (başkan, başkan yardımcısı, y&ouml;netim kurulu &uuml;yesi, kul&uuml;p &uuml;yesi vs.) gerekmektedir.</p>

                    <h3><strong>LKD GENÇ SOSYAL MEDYAYI AKTİF KULLANMALI MI?</strong></h3>

                    <p>Linux Kullanıcıları Derneği, dijitalin dinamiklerine uygun bir yayın politikasına sahiptir. Dolayısıyla Linux Kullanıcıları Derneği&rsquo;yi &uuml;niversitesinde temsil edecek olan marka el&ccedil;ilerinin en az 3 sosyal ağı aktif bi&ccedil;imde kullanıyor olması gerekmektedir. Kısacası &uuml;niversiteler odağında ger&ccedil;ekleştirilen proje ve etkinliklerin daha geniş bir kitleye duyurulması i&ccedil;in &uuml;niversite temsilcilerinin aktif bi&ccedil;imde sosyal medyayı kullanıyor olması gerekmektedir.</p>

                    <h3><strong>TEMSİLCİNİN &Uuml;NİVERSİTEDEKİ ROL&Uuml; NEDİR?</strong></h3>

                    <p>Linux Kullanıcıları Derneği&rsquo;nin etkinlik odaklı alt markası olan &ldquo;Linux Kullanıcıları Derneği Etkinlik&rdquo; her ay farklı bir &uuml;niversitede etkinlik ve zirveler d&uuml;zenlemektedir. LKD Genç, &ldquo;Linux Kullanıcıları Derneği Etkinlik&rdquo; tarafından ger&ccedil;ekleştirilecek organizasyonlarda ekip &ccedil;alışmasına uyum sağlamalı ve Gönüllüsü olduğu &uuml;niversitede bir etkinlik / zirve / eğitim ger&ccedil;ekleştirileceği zaman t&uuml;m s&uuml;reci y&ouml;netmelidir.</p>

                    <h3><strong>KAZAN&Ccedil; MODELİ NEDİR?</strong></h3>

                    <p>LKD Genç temelde g&ouml;n&uuml;ll&uuml;l&uuml;k esasına dayanmaktadır. &Ccedil;&uuml;nk&uuml; Linux Kullanıcıları Derneği Etkinlik tarafından ger&ccedil;ekleştirilen &uuml;niversite etkinlikleri (zirve, panel, eğitim, konferans vb.) tamamen &uuml;cretsizdir. Bu y&uuml;zden &uuml;niversite temsilcilerine bu sorumluluk i&ccedil;in herhangi bir &uuml;cret &ouml;denmez.</p>



                </div>
            </div>
        </div>
    </div>
</div>
@endsection
