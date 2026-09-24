# Proje: LKD Gönüllü Sistemi

## Genel Bilgiler
- **Framework:** Laravel 13 (`composer.lock`: v13.26.1)
- **PHP:** 8.4 (`composer.json`: `"php": "^8.4"`)
- **Veritabanı:** MySQL
- **Repo:** `git@github.com:bmericc/dernekyazilimi.git` (SSH)

## Yerel PHP (Docker)
- Yerel makinede PHP kurulu değil; PHP 8.4, Docker'daki `php:8.4-cli` imajıyla çalıştırılır (7.4 için `php:7.4-cli` de mevcut):
  ```bash
  docker run --rm -v "$PWD":/app -w /app php:8.4-cli php <komut>
  ```
- Yerel `.env` dosyasında `APP_KEY` yok. Uygulamayı boot eden testlerde geçici bir anahtar ve veritabanı gerektirmeyen sürücüler ver:
  ```bash
  docker run --rm -v "$PWD":/app -w /app -e APP_KEY="base64:$(head -c32 /dev/urandom | base64)" -e SESSION_DRIVER=array -e CACHE_DRIVER=array -e VIEW_COMPILED_PATH=/tmp php:8.4-cli php <script>
  ```
- `php:8.4-cli` imajında `pdo_mysql` ve `zip` eklentileri yok; veritabanına dokunan istekler bu imajla çalışmaz.

## Composer (Docker)
- Resmi `composer:2` imajı kullanılır. İmajdaki PHP 8.5'tir ve `ext-intl` içermez; proje ise PHP 8.4 hedefler (`laminas/laminas-math` 8.5'i desteklemiyor, `bahricanli/tckimlik` `ext-intl` ister).
- Lock dosyasından kurulum (`install`): lock zaten 8.4'e göre çözülmüş olduğundan bu iki kontrol atlanır. `--ignore-platform-req=php` `vendor/composer/platform_check.php` dosyasını üretmediği için ardından `dump-autoload` ile yeniden üretilir:
  ```bash
  docker run --rm -v "$PWD":/app -w /app composer:2 sh -c 'composer install --ignore-platform-req=php --ignore-platform-req=ext-intl && composer config -g platform.php 8.4.25 && composer dump-autoload'
  ```
- Bağımlılık çözen komutlar (`update`, `require`, `remove`): çözümlemenin 8.4'e göre yapılması için container içinde global `platform` ayarı verilir (proje `composer.json`'ı değişmez):
  ```bash
  docker run --rm -v "$PWD":/app -w /app composer:2 sh -c 'composer config -g platform.php 8.4.25 && composer config -g platform.ext-intl 8.4.25 && composer update <paket>'
  ```
- Host'taki `/usr/local/bin/composer` Docker'a mount edilemez (`/usr/local/bin` Docker file sharing kapsamında değil).

## Önemli Paketler
| Paket | Versiyon | Notlar |
|---|---|---|
| `bahricanli/tckimlik` | ^1.0.6 | TC Kimlik doğrulama |
| `bahricanli/netgsm` | ^1.0.3 | SMS gönderimi |
| `bahricanli/whatsapp-bridge` | ^1.2.1 | WhatsApp entegrasyonu |

## tckimlik Yapılandırması
Config dosyası: `config/tckimlik.php`

```php
'base_url'       => env('TCKIMLIK_BASE_URL', 'https://tckimlik.linux.org.tr'),
'soap_namespace' => env('TCKIMLIK_SOAP_NAMESPACE', 'http://tckimlik.linux.org.tr/WS'),
'tor_enabled'    => env('TCKIMLIK_TOR_ENABLED', false),
'tor_proxy'      => env('TCKIMLIK_TOR_PROXY', 'socks5h://127.0.0.1:9050'),
```

## .env Değişkenleri
```
TCKIMLIK_BASE_URL=https://tckimlik.linux.org.tr
TCKIMLIK_SOAP_NAMESPACE=http://tckimlik.linux.org.tr/WS
TCKIMLIK_TOR_ENABLED=false
TCKIMLIK_TOR_PROXY=socks5h://127.0.0.1:9050
```

## iframe Modu
- Seminer talep/teklif sayfaları ile giriş/kayıt sayfaları `?in-iframe=1` ile `layouts.iframe` düzeninde açılır ve `frame-ancestors` CSP başlığı gönderir; izinli siteler kurum ayarlarındadır (`Organization::frameAncestorsPolicy()`).
- `in-iframe` parametresi auth yönlendirmesinde (`Authenticate::redirectTo`, `Handler::unauthenticated`) ve form action'larında taşınır; yeni bir sayfa iframe akışına eklenirse aynı deseni izle.

## Modüler Yapı
- Çekirdek `app/` altındadır ve hep açıktır: giriş/kayıt/parola, telefon doğrulama (`phone_verifications`, eski adı `contact_permissions`), iletişim izinleri (`consent_events`: kanal başına son olay geçerli izindir; `App\Support\Consents::set()` yalnız değişeni kaydeder, gönderimden önce `allows($contact, 'email'|'sms'|'whatsapp'|'phone')`; profil sayfası, kayıt formu ve yönetimde kişi sayfası), profil, profil fotoğrafı (`contact_photos`: kişi profil sayfasından (`/my-infos`, `#photo`) yükler, `photos.review` yetkilisi `/admin/photos`'ta onaylar; dosyalar gizli `local` diskte, yalnız `photos.show` route'uyla yetkiliye sunulur), kullanıcı/rol yönetimi, TC doğrulama, işlem kayıtları (`process_logs`; `App\Models\Concerns\Auditable` takılı modeller oluşturma/değişiklik/silmeyi eski-yeni değerle kendiliğinden kaydeder, kimlik no maskelenir, parola kaydedilmez; model olayı olmayan değişiklik için `App\Support\Audit::record()`; yönetim `/admin/process-logs`), sözleşmeler (`agreements` / `agreement_versions` / `agreement_acceptances`: yönetim `/admin/agreements`, `agreements.manage`; taslak → yayınla ile yeni sürüm, yayınlanan sürüm değişmez; formlarda `<x-agreement-checkbox key="kvkk" />`, doğrulama `Agreements::rules('kvkk')`, kayıt `Agreements::accept($user, '<bağlam>', 'kvkk')`; yayında sürüm yoksa kutu gösterilmez; herkese açık metin `/agreements/{anahtar}`), kurum ayarları (`settings` tablosu, `App\Support\Organization`; view'larda `$organization`: ad, kısa ad, logo, favicon, ana renk, iletişim, sosyal bağlantılar, bildirim adresi, duyuru göndericisi, iframe `frame-ancestors` siteleri, Google Analytics/Tag Manager kimlikleri, editörle düzenlenen ana sayfa; yönetim: `/admin/settings/organization`, `settings.manage` yetkisi), ortak servisler (`HtmlSanitizer`, `MailgunMailingList`, `AnnouncementMailing`).
- Kişi/kurum kaydı (`App\Models\Contact`, `contacts`) çekirdektedir: üyelik, bağış, gönüllülük gibi modüller kişiye bağlanır. Hesap (`User`) kişiden ayrıdır ve `users.contact_id` ile ona işaret eder; hesabı olmayan kişi (ör. kurum, içe aktarılan kayıt) olabilir. Profil alanları kişiye taşınana kadar hesap esas kaynaktır: `User` her kaydedildiğinde `syncContact()` kişiyi günceller. Hesapları yalnız Eloquent ile kaydet; sorgu oluşturucuyla `users` güncellemesi kişiye yansımaz.
- Sıfatlar (gönüllü, üye, yönetim/denetleme/disiplin kurulu üyesi...) `affiliation_types` tablosunda veridir; kişinin sıfatları `contact_affiliations` satırlarıdır. Bir kişi aynı anda birden fazla sıfat taşıyabilir; biten sıfat silinmez, `ended_at` (sıfatın geçerli olmadığı ilk gün) ile tarihçede kalır. `$contact->affiliate('<anahtar>')`, `endAffiliation()`, `hasAffiliation()`. Koddan yalnız `is_system` türlere (`volunteer`, `member`) başvur. Geçiş döneminde `lkd_user_id` üye sıfatını açar/kapatır; Volunteer modülü kayıtta gönüllü sıfatı verir.
- Rol ve yetki: `roles` tablosu, yetki anahtarları `permission_role`'de. Hesabın rolleri = doğrudan verilenler (`role_user`) + kişisinin sürmekte olan sıfatlarının rol şablonları (`affiliation_type_role`); sıfat bitince yetki düşer. Sistem rolleri `owner` (Sahip, bütün yetkiler) ve `manager` (Yönetici). Yetki anahtarları kodda kayıtlıdır: çekirdek `admin.access`, modüller `$this->permissions()->register('<anahtar>', '<Türkçe etiket>', '<grup>', <sıra>)`. Kontrol: `$user->hasPermission()`, `isOwner()`, rota için `permission:<anahtar>` middleware. Eski `users.role` sütunu (1 sahip, 2 yönetici, 3 kullanıcı) hâlâ doğrudan owner/manager rolünü belirler ve kaydedilince `role_user`'a yansır; eski ekranlar `accessLevel()` ile aynı 1/2/3 değerini okur, `role:1` middleware'i de buna bakar. Yeni kodda `->role` okuma, yetki anahtarı kullan.
- Diğer her özellik `modules/<Ad>/` altında bir modüldür (`Modules\<Ad>\` PSR-4). Modüller `config/modules.php`'de. Bir modül yalnız `.env`'de `MODULE_<AD>=true` eklenmişse açıktır; satır yoksa kapalıdır (yeni kurulum yalnız çekirdek + Admin ile başlar). Testlerde modüller `phpunit.xml` ile açılır; env'i değiştiren test eski değerleri geri yüklemeli:
  | Modül | Anahtar | Not |
  |---|---|---|
  | Admin | `admin` | `/admin` paneli ana sayfası, kişi & kurumlar ve sıfatları, sıfat türleri, roller ve yetkiler, kullanıcılar, TC doğrulama, işlem kayıtları. `locked`: kapatılamaz |
  | Volunteer | `volunteer` | Gönüllü tanıtım metinleri, "Gönüllü Ol" etiketi, gönüllü Mailgun listesi. `requires: mail-forwarding, reference` |
  | MailForwarding | `mail-forwarding` | `ad.soyad@<domain>` yönlendirmesi (PostfixAdmin). Domain `MAIL_FORWARDING_DOMAIN` (gönüllü: penguen.org.tr, üyeler için linux.org.tr planlanıyor) |
  | Reference | `reference` | Referans talebi |
  | EmailChange | `email-change` | Hesap e-postası değişikliği talebi |
  | Announcements | `announcements` | Duyurular, ana sayfa duyuru kartı |
  | Seminar | `seminar` | Seminer konuları, talepleri, verme başvuruları |
  | LkdYoung | `lkd-young` | LKD Genç. `requires: mail-forwarding` |
  | Representation | `representation` | Temsilcilikler |
  | IdCard | `id-card` | Sanal kimlik kartı: sıfat türü başına şablon (`/admin/id-cards`), kişi başına her aktif sıfat için kart (`/my-cards`, hesap menüsünde "Kimlik kartlarım"), herkese açık QR doğrulama `/kart/{token}` (maskeli ad). Geçerlilik sıfattan gelir |
- Bir modül, açık olan başka bir modülün `requires` listesindeyse kendi bayrağı olmasa da otomatik açılır (ör. `mail-forwarding`, `reference`); onu isteyen modül kapanınca o da kapanır.
- Kapalı modülün route'ları, menüleri, view'ları ve listener'ları yüklenmez; migration'ları ise her zaman yüklenir (şema modül durumuna bağlı değildir).
- Modül dizini: `<Ad>ServiceProvider.php` (`App\Modules\ModuleServiceProvider`'dan türer), `config.php` (`config('<anahtar>')`), `routes/web.php` (web middleware), `routes/admin.php` (yönetim sayfaları), `resources/views` (`<anahtar>::view`), `database/migrations`, `Http`, `Models`, `Mail`, `Listeners`, `Tests/Feature` (`Modules\<Ad>\Tests\Feature`).
- Bağımlılık kuralı: çekirdek hiçbir modüle referans vermez. Modül çekirdeği ve `requires` listesindeki modülleri doğrudan kullanabilir; diğer modüllerle yalnızca şunlar üzerinden konuşur:
  - Menü: `$menu->add('user'|'admin', <grup>, <etiket/çeviri anahtarı>, <route adı>, <erişim>, <sıra>)`; `<erişim>` eski seviyeler (1, 2) ve/veya yetki anahtarları, boşsa herkes
  - Slot: `$slots->push('<slot>', '<view>')`; çekirdek view'larda `@moduleSlot('home.top' | 'home.main' | 'welcome.intro' | 'welcome.sections' | 'account.menu' | 'admin.users.head' | 'admin.users.cell' | 'admin.users.actions', [...])`
  - Yönetim paneli ana ekranı (`/admin`): `$this->dashboard()->stat(<etiket>, <ikon>, fn () => <sayı>, <route adı|null>, <roller>, <sıra>, <not>)` ve `->chart(<başlık>, fn () => [<etiket> => <sayı>], 'bar'|'line', <roller>, <sıra>, <açıklama>)`; aylık seri için `Dashboard::monthly($query, 12, cumulative: false)`. Grafikler sunucu tarafında SVG olarak çizilir (`admin::partials.chart`)
  - Kişi alanları (kimlik kartı gibi profil dışı gösterimler): `$this->contactFields()->register(<anahtar>, <etiket>, fn (Contact $contact) => ?string, <sıra>)`; çekirdek ad, soyad, e-posta, telefon, il, üye no'yu kaydeder
  - Çekirdek olaylar (`app/Events`): `DashboardVisited`, `ProfileUpdated` (listener `$redirect` atayabilir), `UserEmailChanging` (listener `App\Exceptions\ActionBlocked` fırlatarak işlemi iptal eder), `UserEmailChanged`
  - View içinde isteğe bağlı içerik: `@module('<anahtar>') ... @endmodule`

## Kullanıcı ve Yönetim Arayüzü
- Site (`layouts.app`) ile yönetim paneli (`layouts.admin`, `/admin`) ayrıdır; ikisinin de yatay menüsü `Menu` kayıt defterinden gelir (`user` ve `admin` bölümleri). Tek öğeli grup bağlantı, çok öğeli grup `$menu->label(...)` başlıklı açılır menü olur.
- Yönetim sayfaları modülün `routes/admin.php` dosyasında tanımlanır: otomatik olarak `/admin` önekli, `admin.` adlı ve `auth` + `permission:admin.access` korumalıdır; sayfa kendi yetki anahtarıyla (`permission:<anahtar>`) ayrıca sarılır, eski sahip-yalnız sayfalar `role:1` kullanır. Controller'ları `Http/Controllers/Admin/`, view'ları `resources/views/admin/` altındadır ve `layouts.admin`'i genişletir. `resources/js/jquery.js` `<thead>`'li her tabloya DataTables uygular; sunucuda sayfalanan/filtrelenen tablolara `data-no-datatable` ekle (JS değişince varlıkları Docker'da `node:20` ile `npm run production` derle; yerel Node 26 Mix'i çalıştıramıyor).
- Eski yönetim adresleri (`/users`, `/announcements`, `/seminar-subjects` vb.) ilgili modülün `routes/web.php` dosyasında 301 ile `/admin/...` karşılığına yönlenir.

## PostfixAdmin XML-RPC
- E-posta yönlendirmeleri `POSTFIXADMIN_SERVER` üzerindeki PostfixAdmin 3.2.1'in XML-RPC arayüzüyle yönetilir: `server3.linux.org.tr` (10.10.10.23, `192.168.0.34` üzerinden SSH), dosya `/usr/share/postfixadmin/public/xmlrpc.php`.
- Projedeki `mailserver/xmlrpc_server.php` bu dosyanın birebir kopyasıdır ve her zaman güncel tutulmalıdır. Uygulamanın çağırdığı her `alias.*` metodu burada tanımlı olmalı (`create`, `update`); yeni bir metot kullanılacaksa önce bu dosyaya eklenir, sonra sunucuya aynı dosya kopyalanır. Canlı dosyanın md5'i proje kopyasıyla eşleşmelidir.

## Canlı (Prod)
- Uygulama `server1.linux.org.tr` (`192.168.0.34` üzerinden SSH) `/var/www/portal.lkd.org.tr` dizininde (24 Eylül 2026'dan önce `/var/www/gonullu.lkd.org.tr`); Apache vhost'u `portal.lkd.org.tr`, `gonullu.lkd.org.tr` onun takma adıdır. TLS Cloudflare'de sonlanır, origin'e düz HTTP gelir; `lkdtr/dernekyazilimi` reposunun `main` dalını çeker (değişiklikler bmericc fork'undan lkdtr'ye PR ile gelir). Repo 24 Eylül 2026'da `gonulluyazilimi` → `dernekyazilimi` olarak yeniden adlandırıldı; GitHub eski adresi yönlendirir ama sunucudaki uzak adres `sudo git remote set-url origin <yeni adres>` ile güncellenmeli. Dosyalar root'a aittir.
- Web PHP 8.4 php-fpm ile çalışır; sunucudaki varsayılan `php` CLI 8.5'tir. Composer ve artisan komutları `php8.4` ile çalıştırılmalı:
  ```bash
  sudo php8.4 /usr/bin/composer install --no-dev --optimize-autoloader
  sudo php8.4 artisan optimize:clear
  ```
- Kodda derneğe özgü değer (ad, e-posta, alan adı, logo) yazılmaz; `Organization` üzerinden okunur. LKD'nin değerleri `LkdOrganizationSeeder`'dadır (yalnız boş ayarları doldurur; LKD sözleşmelerini de `LkdAgreementSeeder` ile 1. sürüm olarak yayınlar ve `users.agreement_at` kabullerini aktarır): `sudo -u www-data php8.4 artisan db:seed --class=LkdOrganizationSeeder --force`.
- `storage` içine dosya yazan artisan komutları (seeder, tinker) `www-data` olarak çalıştırılmalı: `sudo -u www-data php8.4 artisan ...`. `sudo` ile root olarak çalışırsa oluşan klasörler root'a ait kalır, web tarafı okuyamaz/yazamaz; düzeltmek için `sudo chown -R www-data:www-data storage`.
- Modüller yalnızca `.env`'de `MODULE_<AD>=true` ile açılır; yeni bir modül canlıya çıkmadan önce satırı `.env`'e eklenmeli.
- Migration'dan önce veritabanı yedeği: root'un MySQL parolası yok, `/etc/mysql/debian.cnf` kullanılır:
  ```bash
  sudo sh -c 'mysqldump --defaults-file=/etc/mysql/debian.cnf --single-transaction --routines gonullulkdorgtr | gzip > /var/backups/gonullulkdorgtr-$(date +%Y%m%d-%H%M%S).sql.gz' && sudo chmod 600 /var/backups/gonullulkdorgtr-*.sql.gz
  ```

## Bilinen Uyarılar
- `laminas/laminas-loader` ve `laminas/laminas-math` abandoned uyarıları var, kritik değil. `laminas-math` PHP 8.5'i desteklemediği için PHP yükseltmesinin önünde engel.
