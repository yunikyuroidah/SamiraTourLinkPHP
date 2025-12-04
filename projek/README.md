# 🌟 Samira Travel - PHP MySQL Version

Proyek website travel agent yang telah dikonversi dari TypeScript/Firebase ke PHP/MySQL.

## 📋 Persyaratan Sistem

- **Web Server**: XAMPP, WAMP, atau LAMP
- **PHP**: Version 7.4 atau lebih tinggi
- **MySQL**: Version 5.7 atau lebih tinggi
- **Browser**: Chrome, Firefox, Safari, atau Edge terbaru

## 🚀 Instalasi & Setup

### 1. Setup Database

1. Buka **phpMyAdmin** atau MySQL client
2. Buat database baru dengan nama: `samira_travel`
3. Jalankan SQL berikut:

```sql
CREATE DATABASE IF NOT EXISTS samira_travel DEFAULT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
USE samira_travel;

-- Tabel paket
CREATE TABLE IF NOT EXISTS paket (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nama_paket VARCHAR(255),
  deskripsi TEXT,
  fasilitas JSON,
  features JSON
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert data paket
INSERT INTO paket (nama_paket,deskripsi,fasilitas,features) VALUES
('Paket Haji Furoda 2025 (Haji Tanpa Antri)',
 'Haji tanpa antri dengan visa Mujamalah, keberangkatan lebih cepat dan pasti.',
 '["Tiket pesawat PP (Jakarta)","Visa Haji Mujamalah","Hotel sesuai program","Transportasi Arab Saudi","Konsumsi 3x sehari","Asuransi perjalanan"]',
 '["Keberangkatan: 12 Juni - 1 Juli 2025 (20 hari)","Maskapai: Saudia Airlines","Harga Quad: USD 29.500 | Triple: USD 31.500 | Double: USD 34.500","Uang Muka: USD 15.000"]'
),
('Paket Haji Khusus (Sesuai Porsi Kemenag)',
 'Haji reguler eksklusif sesuai porsi Kemenag, dengan fasilitas bintang 4-5.',
 '["Tiket pesawat ekonomi PP","Hotel bintang 4/5","Visa haji","Makan 3x sehari","Handling airport","Asuransi perjalanan"]',
 '["Estimasi Harga: USD 17.000","Uang Muka: USD 5.000","Maskapai: Garuda / Saudia","Hotel: Al Aqeeg & Royal Dar El Eiman/setaraf"]'
),
('Program Haji Plus (Ambil Kuota Dulu, Bayar Belakangan)',
 'Daftar haji plus tanpa langsung bayar penuh. Kuota diamankan, pembayaran fleksibel.',
 '["Pendampingan pendaftaran","Program sesuai regulasi Kemenag","Hotel & transportasi sesuai paket"]',
 '["Proses pendaftaran mudah & cepat","Kuota haji plus langsung diamankan","Didukung Samira Berkah Indonesia"]'
),
('Umroh Plus Turkey 16 Hari (27 November 2025)',
 'Gabungan ibadah umroh dan wisata religi ke Turki.',
 '["Hotel bintang 4/5 di Turki & Arab Saudi","Transportasi bus AC","Makan 3x sehari","City tour Istanbul","Perlengkapan umroh gratis"]',
 '["Keberangkatan: 27 Nov 2025","Durasi: 16 hari","Rute: Jakarta - Istanbul - Jeddah","Maskapai: Saudi/Turkish Airlines"]'
),
('Umroh Oktober Ceria 2025',
 'Umroh reguler dengan harga terjangkau, berangkat dari Surabaya.',
 '["Hotel bintang 4/5","Transportasi bus AC","Makan 3x sehari","Perlengkapan umroh lengkap","Manasik & pembimbing ibadah"]',
 '["Keberangkatan: 13 & 20 Oktober 2025","Durasi: 12 hari","Maskapai: Garuda Indonesia","Harga mulai: 31 juta-an"]'
);

-- Tabel profil_travel
CREATE TABLE IF NOT EXISTS profil_travel (
  id VARCHAR(100) NOT NULL PRIMARY KEY,
  alamat TEXT,
  email VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO profil_travel (id,alamat,email) VALUES
('profil_id',
 'Perum Graha Kota Blok D4 No.16, Suko, Sidoarjo',
 'samiratravel@gmail.com'
);

-- Tabel tour_leader
CREATE TABLE IF NOT EXISTS tour_leader (
  id VARCHAR(100) NOT NULL PRIMARY KEY,
  nama VARCHAR(255),
  telepon VARCHAR(50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tour_leader (id,nama,telepon) VALUES
('tour_leader_id','Sri Wahyuningsih','+6285707007870');

-- Tabel users (admin)
CREATE TABLE users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password CHAR(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (username, password) VALUES
('Admin1', SHA2('123', 256)),
('Admin2', SHA2('321', 256)),
('Admin3', SHA2('124', 256));
```

### 2. Konfigurasi Database

Edit file `config/database.php` sesuai setting MySQL Anda:

```php
private $host = "localhost";
private $db_name = "samira_travel";
private $username = "root"; // sesuaikan dengan username MySQL
private $password = ""; // sesuaikan dengan password MySQL
```

### 3. Test Koneksi

1. Buka browser dan akses: `http://localhost/projek/test-db.php`
2. Pastikan semua test berhasil (✅)

## 🎯 Akses Website

### Frontend (Customer)
- **URL**: `http://localhost/projek/index.php`
- **Fitur**: 
  - Tampilan paket travel
  - Info tour leader
  - Kontak WhatsApp
  - Design responsif

### Admin Panel
- **URL**: `http://localhost/projek/src/pages/admin-login.php`
- **Login**:
  - Username: `Admin1` | Password: `123`
  - Username: `Admin2` | Password: `321` 
  - Username: `Admin3` | Password: `124`

### Fitur Admin
- ✅ **Dashboard**: Statistik dan overview
- ✅ **Kelola Paket**: CRUD paket travel
- ✅ **Profil Travel**: Edit kontak dan alamat
- 🔄 **Tour Leader**: Edit info guide (coming soon)

## 📁 Struktur Proyek

```
projek/
├── config/
│   └── database.php          # Konfigurasi database
├── models/
│   ├── Paket.php            # Model paket travel
│   ├── ProfilTravel.php     # Model profil travel
│   ├── TourLeader.php       # Model tour leader
│   └── User.php             # Model admin user
├── src/
│   ├── components/
│   │   ├── Packages.php     # Komponen paket travel
│   │   └── Leader.php       # Komponen tour leader
│   └── pages/
│       ├── admin-login.php      # Login admin
│       ├── admin-dashboard.php  # Dashboard admin
│       ├── admin-packages.php   # Kelola paket
│       ├── admin-logout.php     # Logout
│       └── admin-profile-travel-new.php
├── index.php                # Halaman utama
└── test-db.php             # Test koneksi database
```

## 🔧 Troubleshooting

### Error "Call to undefined function mysqli_connect()"
- Pastikan ekstensi `php_mysqli` dan `php_pdo_mysql` aktif di `php.ini`

### Error "Access denied for user"
- Periksa username/password MySQL di `config/database.php`
- Pastikan user memiliki privilege untuk database `samira_travel`

### Path/Include errors
- Pastikan file path relatif sudah benar
- Periksa case-sensitive pada sistem Linux/Unix

## 🚀 Development Roadmap

- [ ] Konversi komponen: Hero, About, Keunggulan, Gallery, Footer
- [ ] Halaman admin tour leader yang lengkap
- [ ] Upload gambar untuk paket travel
- [ ] Dashboard analytics yang lebih detail
- [ ] Sistem backup database
- [ ] Email notification system

## 📞 Support

Jika ada pertanyaan atau masalah:
- Email: samiratravel@gmail.com
- WhatsApp: +6285707007870

---
**© 2024 Samira Travel - Haji & Umroh Terpercaya**