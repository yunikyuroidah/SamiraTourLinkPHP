<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../models/ProfilTravel.php';
require_once __DIR__ . '/../../models/TourLeader.php';

$profilTravel = new ProfilTravel();
$profil = $profilTravel->getById();
// Get tour leader phone (use first/only row)
$tourLeader = new TourLeader();
$tlStmt = $tourLeader->read();
$tlRow = $tlStmt ? $tlStmt->fetch(PDO::FETCH_ASSOC) : null;
$tourPhone = $tlRow['telepon'] ?? null;

$footerLinks = [
    'Bantuan' => [
        ['name' => 'FAQ', 'href' => '#faq'],
        ['name' => 'Panduan', 'href' => '#guide'],
        ['name' => 'Kontak Support', 'href' => '#contact'],
        ['name' => 'Syarat & Ketentuan', 'href' => '#terms'],
    ]
];

$socialMedia = [
    [
        'name' => 'Instagram',
        'icon' => asset('src/assets/instagram.png'),
        'url'  => 'https://www.instagram.com/bundachika_samiratravel/',
    ],
    [
        'name' => 'TikTok',
        'icon' => asset('src/assets/tiktok.png'),
        'url'  => 'https://www.tiktok.com/@bundachika_samiratravel',
    ],
];
?>

<!-- Footer -->
<footer class="bg-gradient-to-br from-gray-900 to-blue-900 text-white relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-96 h-96 bg-blue-500 rounded-full transform -translate-x-48 -translate-y-48"></div>
        <div class="absolute bottom-0 right-0 w-64 h-64 bg-green-500 rounded-full transform translate-x-32 translate-y-32"></div>
    </div>

    <div class="relative z-10">
        <!-- Main Footer Content -->
        <div class="container mx-auto max-w-7xl px-6 pl-16 py-16">
            <div class="grid lg:grid-cols-3 md:grid-cols-1 gap-8">
                <!-- Company Info -->
                <div class="lg:col-span-1">
                    <div class="flex items-center mb-6">
                        <img src="<?php echo asset('src/assets/logo.png'); ?>" alt="<?php echo htmlspecialchars($profil['nama'] ?? 'Samira Travel'); ?>" class="h-12 w-auto mr-4">
                        <div>
                            <h3 class="text-2xl font-bold text-white">
                                <?php echo htmlspecialchars($profil['nama'] ?? 'Samira Travel'); ?>
                            </h3>
                            <p class="text-blue-200 text-sm">Sahabat Perjalanan Spiritual Anda</p>
                        </div>
                    </div>

                    <p class="text-gray-300 leading-relaxed mb-6 max-w-md">
                        <?php echo htmlspecialchars($profil['deskripsi'] ?? 'Samira Travel adalah mitra terpercaya untuk perjalanan spiritual Anda. Dengan pengalaman bertahun-tahun, kami berkomitmen memberikan pelayanan terbaik untuk ibadah Umrah dan Haji yang berkesan.'); ?>
                    </p>
                </div>

                <!-- Contact (centered) -->
                <div class="text-center">
                    <h4 class="text-lg font-semibold mb-4 text-white">Kontak Kami</h4>
                    <div class="space-y-4 max-w-sm mx-auto">
                        <div class="flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-300 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            <p class="text-gray-300"><?php echo htmlspecialchars($profil['alamat'] ?? 'Perum Graha Kota Blok D4 No.16, Suko, Sidoarjo'); ?></p>
                        </div>
                        <div class="flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-300 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                            </svg>
                            <p class="text-white font-semibold"><?php echo htmlspecialchars($tourPhone ?? ($profil['telepon'] ?? '+6285707007870')); ?></p>
                        </div>
                        <div class="flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-300 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                            <p class="text-white font-semibold"><?php echo htmlspecialchars($profil['email'] ?? 'samiratravel@gmail.com'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Ikuti Kami (right) -->
                <div class="flex items-start justify-center lg:justify-end">
                    <div>
                        <h4 class="text-lg font-semibold mb-4 text-white">Ikuti Kami</h4>
                        <div class="flex items-center space-x-4">
                            <?php foreach ($socialMedia as $social): ?>
                                <a href="<?php echo htmlspecialchars($social['url']); ?>"
                                    target="_blank"
                                    title="<?php echo htmlspecialchars($social['name']); ?>"
                                    class="bg-white/10 p-3 rounded-full hover:bg-white/20 transition-all duration-300 flex items-center justify-center">
                                    <img src="<?php echo $social['icon']; ?>"
                                        alt="<?php echo htmlspecialchars($social['name']); ?> Icon"
                                        class="w-6 h-6 object-contain">
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="bg-black/30 border-t border-gray-700">
            <div class="container mx-auto px-6 pl-16 py-4">
                <div class="flex flex-col md:flex-row justify-center items-center">
                    <div class="text-gray-400 text-sm mb-4 md:mb-0 text-center">
                        <?php
                        if (session_status() === PHP_SESSION_NONE) {
                            @session_start();
                        }
                        $adminHref = (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'])
                            ? asset('src/pages/admin-packages.php')
                            : asset('src/pages/admin-login.php');
                        ?>
                        <p>&copy; <?php echo date('Y'); ?>
                            <a href="<?php echo $adminHref; ?>" class="hover:underline">
                                <?php echo htmlspecialchars($profil['nama'] ?? 'Samira Travel'); ?>
                            </a>. All rights reserved.
                        </p>
                    </div>
                    <!-- Footer links removed as requested -->
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Top Button -->
    <button
        id="back-to-top"
        onclick="scrollToTop()"
        class="fixed bottom-20 right-6 bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 opacity-0 invisible"
        title="Kembali ke atas">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>
</footer>

<script>
    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }
    window.addEventListener('scroll', function() {
        const btn = document.getElementById('back-to-top');
        if (window.pageYOffset > 300) {
            btn.classList.remove('opacity-0', 'invisible');
            btn.classList.add('opacity-100', 'visible');
        } else {
            btn.classList.add('opacity-0', 'invisible');
            btn.classList.remove('opacity-100', 'visible');
        }
    });
    document.querySelectorAll('footer a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        });
    });
</script>

<style>
    footer .group:hover svg {
        transform: translateX(4px);
    }

    footer input:focus {
        background: rgba(255, 255, 255, 0.15);
    }

    footer a[title]:hover {
        transform: translateY(-2px) scale(1.1);
    }

    footer * {
        transition: all 0.3s ease;
    }
</style>