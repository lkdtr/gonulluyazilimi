# Proje: LKD Gönüllü Sistemi

## Genel Bilgiler
- **Framework:** Laravel 8 (v8.83.29)
- **PHP:** 8.4 (Homebrew: `/usr/local/Cellar/php@8.4/8.4.20/bin/php`)
- **Veritabanı:** MySQL
- **Repo:** `git@github.com:bmericc/gonulluyazilimi.git` (SSH)

## Composer
- Composer komutlarını PHP 8.4 ile çalıştır:
  ```bash
  /usr/local/Cellar/php@8.4/8.4.20/bin/php /usr/local/bin/composer <komut>
  ```
- Yerel sistemde PHP 7.4 de yüklü (`/usr/local/opt/php@7.4`), terminalde `php` komutu eski sürümü gösterebilir.

## Önemli Paketler
| Paket | Versiyon | Notlar |
|---|---|---|
| `bahricanli/tckimlik` | ^1.0.4 | TC Kimlik doğrulama |
| `bahricanli/netgsm` | ^1.0.2 | SMS gönderimi |
| `bahricanli/whatsapp-bridge` | ^1.0.1 | WhatsApp entegrasyonu |

## tckimlik Yapılandırması
Config dosyası: `config/tckimlik.php`

```php
'base_url'      => env('TCKIMLIK_BASE_URL', 'https://tckimlik.linux.org.tr'),
'soap_namespace'=> env('TCKIMLIK_SOAP_NAMESPACE', 'http://tckimlik.linux.org.tr/WS'),
```

## .env Değişkenleri
```
TCKIMLIK_BASE_URL=https://tckimlik.linux.org.tr
TCKIMLIK_SOAP_NAMESPACE=http://tckimlik.linux.org.tr/WS
```

## Bilinen Uyarılar
- Laravel 8, PHP 8.4 ile tam uyumlu değil — `Deprecated: Implicitly marking parameter as nullable` uyarıları beklenen durum, hata değil.
- `fruitcake/laravel-cors`, `laminas/laminas-loader`, `swiftmailer/swiftmailer` abandoned uyarıları var, kritik değil.
- `App\Console\Commands\TriggerException` PSR-4 uyumsuzluğu mevcut (`triggerException.php` → `TriggerException.php` olmalı).
