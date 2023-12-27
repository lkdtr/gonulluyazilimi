# Kurulum Aşamaları

* terminal açılır

* projenin kurulu olduğu klasöre giriş yapılır

* cp .env.example .env

* .env dosyasının içi (başta veritabanı bilgileri olmak üzere) düzenlenir 

* composer install

* php artisan migrate

* php artisan db:seed --class CitiesTableSeeder

* php artisan db:seed --class UniversitiesTableSeeder

* çalıştırmak için de php artisan serv

