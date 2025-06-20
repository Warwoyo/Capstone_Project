# 🎨 KANA - Sistem Manajemen PAUD Kartika Pradana 🧸

![KANA Banner](https://via.placeholder.com/1200x300/4dabf7/ffffff?text=KANA+-+Kartika+Pradana)

[![Status: Active](https://img.shields.io/badge/Status-Active-success.svg)](https://github.com/Warwoyo/Capstone_Project)
[![Made with: Laravel](https://img.shields.io/badge/Made%20with-Laravel-ff2d20.svg)](https://laravel.com)
[![Made with: PHP](https://img.shields.io/badge/PHP-61.5%25-8892bf.svg)](https://www.php.net/)
[![Made with: Blade](https://img.shields.io/badge/Blade-38.1%25-f05340.svg)](https://laravel.com/docs/blade)

> ### "Pantau tumbuh kembang, wujudkan anak hebat"

## 📑 Daftar Isi
- [Tentang Proyek](#-tentang-proyek)
- [Fitur Utama](#-fitur-utama)
- [Keunggulan Teknis & Keamanan](#-keunggulan-teknis--keamanan)
- [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
- [Instalasi dan Konfigurasi](#-instalasi-dan-konfigurasi)
- [Penggunaan Aplikasi](#-penggunaan-aplikasi)
- [Struktur Proyek](#-struktur-proyek)
- [Kontributor](#-kontributor)
- [Lisensi](#-lisensi)
- [Kontak](#-kontak)

## 🌟 Tentang Proyek

KANA adalah platform digital berbasis website yang dirancang khusus untuk Pendidikan Anak Usia Dini (PAUD) Kartika Pradana. Aplikasi ini berfungsi untuk menyederhanakan pengelolaan kegiatan harian, mendokumentasikan aktivitas anak secara real-time, serta meningkatkan komunikasi dan kolaborasi antara pendidik dan orang tua.

Dikembangkan sebagai bagian dari tugas mata kuliah Capstone Project, KANA hadir sebagai solusi komprehensif yang mengatasi tantangan administrasi manual di PAUD, meningkatkan efisiensi, dan memperkuat kolaborasi antara pihak sekolah dan orang tua dalam mendukung tumbuh kembang anak.

## 🚀 Fitur Utama

### 👨‍👩‍👧‍👦 Manajemen Data Siswa
- **Input dan Edit Data Siswa**: Admin dan guru dapat mengelola data lengkap siswa
- **Pengelompokan Kelas**: Kemampuan untuk membuat kelas dan mengatur anggotanya
- **Profil Siswa**: Detail informasi siswa yang komprehensif

### 📆 Manajemen Kegiatan Harian
- **Jadwal Utama**: Penyusunan dan pengelolaan jadwal harian PAUD secara terstruktur
- **Sub Jadwal**: Pemecahan jadwal utama ke dalam aktivitas yang lebih spesifik
- **Pengingat Kegiatan**: Notifikasi untuk jadwal dan kegiatan penting

### 📋 Pencatatan Kehadiran
- **Presensi Digital**: Sistem absensi yang akurat dan dapat diakses secara real-time
- **Laporan Kehadiran**: Ringkasan presensi untuk periode tertentu
- **Notifikasi Kehadiran**: Informasi kehadiran yang dapat diakses orang tua

### 📊 Dokumentasi & Evaluasi Perkembangan Anak
- **Pencatatan Observasi**: Dokumentasi aktivitas dan perkembangan anak berdasarkan jadwal
- **Dokumentasi Media**: Unggah foto dan video kegiatan anak
- **Penilaian Perkembangan**: Evaluasi kemajuan anak dalam berbagai aspek
- **Pembuatan Rapor**: Pembuatan rapor digital berdasarkan hasil observasi

### 📢 Pengumuman Kelas
- **Pengumuman Terstruktur**: Admin dan guru dapat membuat pengumuman untuk kelas tertentu
- **Notifikasi**: Pemberitahuan ke orang tua saat ada pengumuman baru
- **Histori Pengumuman**: Arsip pengumuman yang telah dipublikasikan

### 👤 Manajemen Akun
- **Pengelolaan Pengguna**: Admin dapat mengelola akun guru dan orang tua
- **Pemberian Akses**: Pengaturan hak akses sesuai peran
- **Keamanan Akun**: Perlindungan data pengguna dan enkripsi password

### 👪 Portal Orang Tua & Komunikasi Terintegrasi
- **Dashboard Orang Tua**: Tampilan ringkasan informasi anak
- **Akses Informasi**: Orang tua dapat memantau perkembangan anak kapan saja dan di mana saja
- **Komunikasi Dua Arah**: Orang tua dapat memberikan tanggapan terhadap laporan atau dokumentasi
- **Pelaporan Transparan**: Akses real-time ke informasi perkembangan anak

## 🛡️ Keunggulan Teknis & Keamanan

### Arsitektur Client-Server Tiga Tingkat
- **Presentation Layer**: Blade Template Engine, JavaScript DOM, AJAX
- **Application Layer**: Laravel Framework
- **Data Layer**: MySQL

### Keamanan Data Defense in Depth
- **Enkripsi Data**: 
  - At Rest: bcrypt dan AES-256
  - In Transit: SSL/TLS
- **Autentikasi & Otorisasi**: Role-Based Access Control (RBAC)
- **Validasi Input**: Pencegahan XSS dan injeksi
- **Manajemen Sesi**: Penanganan sesi aman dengan regenerasi ID
- **Pencegahan SQL Injection**: Penggunaan prepared statements

### Kepatuhan Regulasi
- **UU Perlindungan Data Pribadi**: Sesuai dengan UU PDP Indonesia
- **Referensi GDPR**: Mengadopsi prinsip-prinsip keamanan data internasional
- **Data Minimization**: Hanya mengumpulkan data yang diperlukan
- **Consent-Based Access**: Akses data berdasarkan persetujuan

### Jaminan Kualitas
- **Model ISO/IEC 25010**: Standar kualitas perangkat lunak internasional
- **Pengujian Komprehensif**: Unit testing, integration testing, system testing, dan UAT
- **Aspek Kualitas**: Fungsionalitas, kegunaan, kinerja, keandalan, keamanan, pemeliharaan, dan portabilitas

### Desain UX Berpusat pada Pengguna
- **Metodologi Design Thinking**: Empathize, Define, Ideate, Prototype, Test
- **User-Centered Design**: Antarmuka intuitif yang disesuaikan untuk guru dan orang tua
- **Responsif**: Dapat diakses dari berbagai perangkat (desktop dan mobile)

## 💻 Teknologi yang Digunakan

### Frontend
- HTML5, CSS3, JavaScript
- Blade Template Engine
- Bootstrap 5
- AJAX

### Backend
- PHP 8.x
- Laravel Framework 10.x
- RESTful API

### Database
- MySQL 8.x

### Deployment & DevOps
- Git
- Composer
- Web Server (Apache/Nginx)

### Keamanan
- Laravel Sanctum/Fortify
- bcrypt Password Hashing
- CSRF Protection
- XSS Protection

## 🔧 Instalasi dan Konfigurasi

### Prasyarat
```bash
# Pastikan telah menginstal
- PHP >= 8.0
- Composer
- MySQL 8.x
- Web Server (Apache/Nginx)
- Node.js & NPM (untuk kompilasi aset)
```

### Langkah-langkah Instalasi

1. Clone repositori ini
```bash
git clone https://github.com/Warwoyo/Capstone_Project.git
cd Capstone_Project
```

2. Instal dependensi PHP
```bash
composer install
```

3. Instal dependensi JavaScript (jika ada)
```bash
npm install
```

4. Konfigurasi environment
```bash
cp .env.example .env
# Edit file .env sesuai konfigurasi database lokal
```

5. Generate application key
```bash
php artisan key:generate
```

6. Migrasi dan seed database
```bash
php artisan migrate --seed
```

7. Kompilasi aset (jika diperlukan)
```bash
npm run dev
# atau
npm run build
```

8. Jalankan aplikasi
```bash
php artisan serve
```

## 📱 Penggunaan Aplikasi

### Akses Admin
1. Login menggunakan kredensial admin
2. Kelola data siswa, guru, dan orang tua
3. Buat dan atur kelas
4. Manajemen jadwal dan presensi
5. Buat pengumuman dan rapor
6. Kelola akun pengguna

### Akses Guru
1. Login menggunakan kredensial guru
2. Kelola data siswa di kelasnya
3. Catat presensi harian
4. Buat jadwal dan sub jadwal
5. Lakukan pencatatan observasi
6. Buat rapor siswa
7. Publikasi pengumuman kelas

### Akses Orang Tua
1. Login menggunakan kredensial yang diberikan oleh PAUD
2. Lihat data lengkap anak
3. Pantau presensi dan observasi
4. Akses rapor digital
5. Baca pengumuman dari kelas
6. Berikan tanggapan terhadap laporan dan dokumentasi

## 📂 Struktur Proyek

```
Capstone_Project/
├── app/                  # Core application code
│   ├── Http/             # Controllers, Middleware, Requests
│   ├── Models/           # Eloquent models
│   ├── Providers/        # Service providers
│   └── Services/         # Business logic services
├── config/               # Configuration files
├── database/             # Migrations and seeders
│   ├── migrations/       # Database migrations
│   └── seeders/         # Database seeders
├── public/               # Publicly accessible files
│   ├── css/              # Compiled CSS
│   ├── js/               # Compiled JavaScript
│   └── images/           # Image assets
├── resources/            # Views and raw assets
│   ├── views/            # Blade templates
│   ├── js/               # JavaScript sources
│   └── css/              # CSS/SASS sources
├── routes/               # Application routes
│   ├── web.php           # Web routes
│   └── api.php           # API routes
├── storage/              # Application storage
│   ├── app/              # User-generated files
│   └── logs/             # Application logs
└── tests/                # Automated tests
    ├── Unit/             # Unit tests
    └── Feature/          # Feature tests
```

## 👥 Kontributor

| Nama | NIM | Role |
|------|-----|------|
| Anita Margareth D Silalahi | 225150701111019 | UI/UX Design |
| Chyntia Wadi Karini Tamba | 225150707111005 | UI/UX Design |
| Lando King Sihotang | 225150707111022 | Frontend Programmer |
| Naura Istitah Muhtasyam | 225150701111021 | Project Manager |
| Putu Divakara Mataram | 225150207111046 | Backend Programmer |


## 📞 Kontak

Untuk pertanyaan atau informasi lebih lanjut tentang proyek ini, silakan hubungi:
- Email: [filkom@ub.ac.id]
- Repository: [https://github.com/Warwoyo/Capstone_Project](https://github.com/Warwoyo/Capstone_Project)

---

<p align="center">
  <em>"Pantau tumbuh kembang, wujudkan anak hebat"</em>
</p>

<p align="center">
  © 2025 - KANA Team - All Rights Reserved
</p>
