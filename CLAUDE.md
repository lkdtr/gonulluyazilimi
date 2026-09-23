# Proje: LKD Gönüllü Sistemi

## Genel Bilgiler
- **Framework:** Laravel 13 (`composer.lock`: v13.26.1)
- **PHP:** 8.4 (`composer.json`: `"php": "^8.4"`)
- **Veritabanı:** MySQL
- **Repo:** `git@github.com:bmericc/gonulluyazilimi.git` (SSH)

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
- Seminer talep/teklif sayfaları ile giriş/kayıt sayfaları `?in-iframe=1` ile `layouts.iframe` düzeninde açılır ve `frame-ancestors` CSP başlığı (lkd.org.tr) gönderir.
- `in-iframe` parametresi auth yönlendirmesinde (`Authenticate::redirectTo`, `Handler::unauthenticated`) ve form action'larında taşınır; yeni bir sayfa iframe akışına eklenirse aynı deseni izle.

## PostfixAdmin XML-RPC
- E-posta yönlendirmeleri `POSTFIXADMIN_SERVER` üzerindeki PostfixAdmin 3.2.1'in XML-RPC arayüzüyle yönetilir: `server3.linux.org.tr` (10.10.10.23, `192.168.0.34` üzerinden SSH), dosya `/usr/share/postfixadmin/public/xmlrpc.php`.
- Projedeki `mailserver/xmlrpc_server.php` bu dosyanın birebir kopyasıdır ve her zaman güncel tutulmalıdır. Uygulamanın çağırdığı her `alias.*` metodu burada tanımlı olmalı (`create`, `update`); yeni bir metot kullanılacaksa önce bu dosyaya eklenir, sonra sunucuya aynı dosya kopyalanır. Canlı dosyanın md5'i proje kopyasıyla eşleşmelidir.

## Bilinen Uyarılar
- `laminas/laminas-loader` ve `laminas/laminas-math` abandoned uyarıları var, kritik değil. `laminas-math` PHP 8.5'i desteklemediği için PHP yükseltmesinin önünde engel.
