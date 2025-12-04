<?php
require_once 'config/database.php';

// Database Installation Script
class DatabaseInstaller {
    private $pdo;
    
    public function __construct() {
        try {
            // Connect without selecting database first
            $dsn = "mysql:host=localhost";
            $this->pdo = new PDO($dsn, 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ]);
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    
    public function install() {
        try {
            // Create database
            $this->pdo->exec("CREATE DATABASE IF NOT EXISTS samira_travel CHARACTER SET utf8 COLLATE utf8_general_ci");
            echo "✓ Database 'samira_travel' created successfully\n";
            
            // Use database
            $this->pdo->exec("USE samira_travel");
            
            // Create tables
            $this->createTables();
            $this->insertSampleData();
            
            echo "✓ Database installation completed successfully!\n";
            echo "Admin login: admin@samiratravel.com / admin123\n";
            
        } catch(PDOException $e) {
            echo "✗ Installation failed: " . $e->getMessage() . "\n";
        }
    }
    
    private function createTables() {
        // Users table
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            nama VARCHAR(255) NOT NULL,
            role ENUM('admin', 'user') DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->pdo->exec($sql);
        echo "✓ Table 'users' created\n";
        
        // Profil Travel table
        $sql = "CREATE TABLE IF NOT EXISTS profil_travel (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nama VARCHAR(255) NOT NULL,
            deskripsi TEXT,
            alamat TEXT,
            telepon VARCHAR(20),
            email VARCHAR(255),
            website VARCHAR(255),
            logo VARCHAR(255),
            visi TEXT,
            misi TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->pdo->exec($sql);
        echo "✓ Table 'profil_travel' created\n";
        
        // Tour Leader table
        $sql = "CREATE TABLE IF NOT EXISTS tour_leader (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nama VARCHAR(255) NOT NULL,
            email VARCHAR(255),
            telepon VARCHAR(20),
            pengalaman TEXT,
            bahasa VARCHAR(255),
            spesialisasi TEXT,
            foto VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->pdo->exec($sql);
        echo "✓ Table 'tour_leader' created\n";
        
        // Paket table
        $sql = "CREATE TABLE IF NOT EXISTS paket (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nama VARCHAR(255) NOT NULL,
            deskripsi TEXT,
            harga DECIMAL(15,2) NOT NULL,
            durasi VARCHAR(100),
            destinasi VARCHAR(255),
            foto VARCHAR(255),
            fasilitas JSON,
            jadwal JSON,
            tersedia BOOLEAN DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->pdo->exec($sql);
        echo "✓ Table 'paket' created\n";
    }
    
    private function insertSampleData() {
        // Insert admin user
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (email, password, nama, role) VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE password = VALUES(password)";
        $this->pdo->prepare($sql)->execute([
            'admin@samiratravel.com',
            $password,
            'Admin Samira Travel',
            'admin'
        ]);
        echo "✓ Admin user created\n";
        
        // Insert company profile
        $sql = "INSERT INTO profil_travel (nama, deskripsi, alamat, telepon, email, visi, misi) VALUES (?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE nama = VALUES(nama)";
        $this->pdo->prepare($sql)->execute([
            'Samira Travel',
            'Agen perjalanan terpercaya dengan pengalaman bertahun-tahun dalam mengatur perjalanan wisata domestik dan internasional.',
            'Jl. Raya Jakarta No. 123, Jakarta Pusat',
            '+62 21 1234 5678',
            'info@samiratravel.com',
            'Menjadi agen perjalanan terdepan yang memberikan pengalaman wisata tak terlupakan.',
            'Memberikan pelayanan terbaik dengan harga terjangkau dan kualitas premium.'
        ]);
        echo "✓ Company profile inserted\n";
        
        // Insert sample tour leader
        $sql = "INSERT INTO tour_leader (nama, email, telepon, pengalaman, bahasa, spesialisasi) VALUES (?, ?, ?, ?, ?, ?)";
        $this->pdo->prepare($sql)->execute([
            'Ahmad Rizki',
            'ahmad@samiratravel.com',
            '+62 812 3456 7890',
            '5 tahun sebagai tour guide profesional',
            'Indonesia, English, Mandarin',
            'Wisata budaya dan sejarah'
        ]);
        echo "✓ Sample tour leader inserted\n";
        
        // Insert sample packages
        $packages = [
            [
                'Paket Bali 4D3N',
                'Paket wisata Bali lengkap dengan kunjungan ke tempat-tempat menarik',
                2500000,
                '4 Hari 3 Malam',
                'Bali',
                json_encode(['Hotel bintang 4', 'Transportasi AC', 'Makan 3x sehari', 'Tour guide', 'Tiket wisata']),
                json_encode([
                    'Hari 1: Tiba di Bali, check-in hotel',
                    'Hari 2: Kintamani - Ubud',
                    'Hari 3: Tanah Lot - Bedugul',
                    'Hari 4: Check-out, transfer airport'
                ])
            ],
            [
                'Paket Yogyakarta 3D2N',
                'Wisata budaya dan kuliner di Yogyakarta',
                1500000,
                '3 Hari 2 Malam',
                'Yogyakarta',
                json_encode(['Hotel bintang 3', 'Transportasi', 'Sarapan', 'Tour guide lokal']),
                json_encode([
                    'Hari 1: Malioboro - Tugu Jogja',
                    'Hari 2: Borobudur - Prambanan',
                    'Hari 3: Kraton - Taman Sari'
                ])
            ]
        ];
        
        $sql = "INSERT INTO paket (nama, deskripsi, harga, durasi, destinasi, fasilitas, jadwal) VALUES (?, ?, ?, ?, ?, ?, ?)";
        foreach ($packages as $package) {
            $this->pdo->prepare($sql)->execute($package);
        }
        echo "✓ Sample packages inserted\n";
    }
}

// Run installation if accessed directly
if (basename($_SERVER['PHP_SELF']) === 'install.php') {
    echo "<pre>";
    echo "=== SAMIRA TRAVEL DATABASE INSTALLER ===\n\n";
    $installer = new DatabaseInstaller();
    $installer->install();
    echo "\n=== INSTALLATION COMPLETE ===\n";
    echo "</pre>";
}
?>