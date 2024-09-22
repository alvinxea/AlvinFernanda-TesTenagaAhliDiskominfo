# AlvinFernanda-TesTenagaAhliDiskominfo

# API Produk dan Pesanan
## Prasyarat

Sebelum memulai, pastikan Anda telah menginstal hal-hal berikut:

1. **PHP 7.3 atau lebih baru**: Pastikan PHP terinstal dan dikonfigurasi dengan benar.
2. **Composer**: Alat untuk mengelola dependensi PHP.
3. **MySQL/MariaDB**: Sistem manajemen basis data untuk menyimpan data.
4. **Postman** atau alat sejenis untuk menguji API (opsional).
5. **Docker** (opsional): Untuk menjalankan aplikasi dalam kontainer.

## Langkah Instalasi

Clone repository ini ke lokal Anda:

git clone https://github.com/alvinxea/AlvinFernanda-TesTenagaAhliDiskominfo.git
cd repo-name

Pindah ke folder repo:
cd laravel-realworld-example-app

Instal semua dependensi menggunakan Composer:
composer install

Salin file env contoh dan lakukan perubahan konfigurasi yang diperlukan di file .env:
cp .env.example .env

Jalankan migrasi database (Atur koneksi database di .env sebelum melakukan migrasi):
php artisan migrate

Mulai server pengembangan lokal:
php artisan serve

Anda sekarang dapat mengakses server di: http://localhost:800
