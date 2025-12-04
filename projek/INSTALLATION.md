# 🚀 Panduan Instalasi Samira Travel PHP/MySQL

## 📋 Prasyarat
- XAMPP/WAMP/LAMPP (PHP 7.4+, MySQL 5.7+, Apache)
- Web browser modern (Chrome, Firefox, Safari, Edge)

## 🛠️ Langkah Instalasi

### 1. Setup Environment
```bash
# Download dan install XAMPP dari https://www.apachefriends.org/
# Jalankan Apache dan MySQL dari XAMPP Control Panel
```

### 2. Clone/Copy Project
```bash
# Copy folder projek ke htdocs (untuk XAMPP)
C:\xampp\htdocs\samira-travel\
```

### 3. Database Setup
```bash
# Buka browser dan akses
http://localhost/samira-travel/install.php

# Atau setup manual:
# 1. Buka phpMyAdmin (http://localhost/phpmyadmin)
# 2. Buat database 'samira_travel'
# 3. Import schema dari install.php
```

### 4. Konfigurasi Database
Edit `config/database.php` sesuai environment Anda:
```php
private $host = "localhost";       // Database host
private $db_name = "samira_travel"; // Database name  
private $username = "root";        // Database username
private $password = "";            // Database password (kosong untuk XAMPP)
```

### 5. Test Koneksi
```bash
# Akses test database
http://localhost/samira-travel/test-db.php
```

## 🌐 Akses Website

### Frontend (Public)
- **URL**: http://localhost/samira-travel/
- **File**: index.php
- **Fitur**: Hero, About, Packages, Tour Leaders, Gallery, Contact

### Admin Panel
- **URL**: http://localhost/samira-travel/admin
- **Login**: admin@samiratravel.com / admin123
- **Fitur**: Dashboard, Manage Packages, Profile Travel, Tour Leaders

### Admin Pages Direct Access
- Dashboard: http://localhost/samira-travel/src/pages/admin-dashboard.php
- Packages: http://localhost/samira-travel/src/pages/admin-packages.php
- Profile: http://localhost/samira-travel/src/pages/admin-profile-travel.php
- Tour Leader: http://localhost/samira-travel/src/pages/admin-tour-leader.php

## 📁 Struktur File

```
projek/
├── config/
│   └── database.php           # Database configuration
├── models/
│   ├── Database.php          # Database connection class
│   ├── Paket.php             # Package model (CRUD)
│   ├── ProfilTravel.php      # Travel profile model
│   ├── TourLeader.php        # Tour leader model
│   └── User.php              # User authentication model
├── src/
│   ├── assets/               # Images, videos, static files
│   ├── components/           # PHP components (Hero, About, etc.)
│   │   ├── Hero.php
│   │   ├── About.php
│   │   ├── Packages.php
│   │   ├── Leader.php
│   │   ├── Keunggulan.php
│   │   ├── Gallery.php
│   │   └── Footer.php
│   └── pages/               # Admin pages
│       ├── admin-login.php
│       ├── admin-dashboard.php
│       ├── admin-packages.php
│       ├── admin-profile-travel.php
│       └── admin-tour-leader.php
├── index.php               # Main website entry point
├── install.php             # Database installer
├── test-db.php            # Database connection tester
├── .htaccess              # Web server configuration
└── README.md              # This file
```

## 🔧 Troubleshooting

### Database Connection Error
```php
// Cek config/database.php
// Pastikan MySQL running di XAMPP
// Cek username/password database
```

### File Not Found Error
```bash
# Pastikan struktur folder sesuai
# Cek path relatif di include statements
# Pastikan Apache running
```

### PHP Error Display
```php
// Untuk debugging, aktifkan error display di .htaccess:
php_flag display_errors On
php_flag log_errors Off
```

## 📱 Features

### Public Website
✅ Responsive Hero section dengan video background
✅ About section dengan animasi
✅ Travel packages display dari database
✅ Tour leaders showcase
✅ Image gallery
✅ WhatsApp integration
✅ Contact information

### Admin Panel
✅ Secure login system
✅ Dashboard with statistics
✅ CRUD operations for packages
✅ Travel profile management
✅ Tour leader management
✅ Session management

## 🛡️ Security Features
- Password hashing dengan PHP password_hash()
- SQL injection protection dengan PDO prepared statements
- XSS protection dengan htmlspecialchars()
- Session management untuk admin
- File access restrictions via .htaccess

## 📊 Database Schema

### Tables
1. **users** - Admin authentication
2. **profil_travel** - Company profile
3. **paket** - Travel packages
4. **tour_leader** - Tour guide information

### Sample Data
- Admin user: admin@samiratravel.com / admin123
- Sample travel packages
- Company profile data
- Sample tour leader

## 🚀 Deployment

### Production Setup
1. Upload files ke web hosting
2. Update database config untuk production
3. Set proper file permissions
4. Update .htaccess untuk security
5. Disable error display
6. Setup SSL certificate

### Performance Tips
- Enable PHP OPcache
- Optimize images
- Use CDN for static assets
- Enable gzip compression
- Setup database indexing

## 📞 Support

Jika ada masalah atau pertanyaan:
1. Cek log error di XAMPP
2. Test database connection
3. Verify file permissions
4. Check Apache/PHP configuration

## 🔄 Update & Maintenance
- Backup database secara regular
- Update PHP ke versi terbaru
- Monitor security patches
- Optimize database queries
- Clean up session files

---

**Samira Travel PHP/MySQL Project**  
Converted from TypeScript/Firebase to PHP/MySQL  
Ready for production deployment