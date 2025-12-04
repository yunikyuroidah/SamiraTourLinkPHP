<?php
require_once __DIR__ . '/../../models/ProfilTravel.php';
require_once __DIR__ . '/../../models/TourLeader.php';

// Get company profile
$profilTravel = new ProfilTravel();
$profil = $profilTravel->getById();

// Get tour leader (use first/primary tour leader for contact)
$tl = new TourLeader();
$tlStmt = $tl->read();
$tourLeader = null;
if ($tlStmt && $row = $tlStmt->fetch(PDO::FETCH_ASSOC)) {
    $tourLeader = $row;
}
// prepare wa url from tour leader phone
$rawWa = trim($tourLeader['telepon'] ?? ($profil['telepon'] ?? ''));
$waDigits = preg_replace('/[^0-9]/', '', $rawWa);
if (empty($waDigits)) $waDigits = '6285707007870';
if (strpos($waDigits, '0') === 0) $waDigits = '62' . substr($waDigits, 1);
elseif (strpos($waDigits, '8') === 0) $waDigits = '62' . $waDigits;
$waUrl = 'https://wa.me/' . ltrim($waDigits, '+/');
?>

<!-- About Section -->
<section id="about-section" class="py-20 bg-gradient-to-br from-slate-50 to-blue-50 relative overflow-hidden">
    <!-- Background Decorations -->
    <div class="absolute inset-0">
        <div class="absolute top-10 left-10 w-32 h-32 bg-blue-200 opacity-20 rounded-full blur-xl animate-pulse"></div>
        <div class="absolute bottom-10 right-10 w-40 h-40 bg-green-200 opacity-20 rounded-full blur-xl animate-pulse" style="animation-delay: 1s;"></div>
    </div>

    <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 items-center px-6 relative z-10">
        <!-- Video kiri -->
        <div class="flex justify-center opacity-0 transform -translate-x-10 animate-slide-right">
            <div class="relative group">
                <div class="absolute -inset-4 bg-gradient-to-r from-blue-500 to-green-500 rounded-2xl blur-lg opacity-25 group-hover:opacity-40 transition-all duration-500"></div>
                <video
                    src="src/assets/profilvideo.mp4"
                    autoplay
                    loop
                    muted
                    class="relative w-[280px] h-[480px] object-cover rounded-2xl shadow-2xl transform group-hover:scale-105 transition-all duration-500 border-4 border-white/50">
                </video>
                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent rounded-2xl pointer-events-none"></div>
            </div>
        </div>

        <!-- Teks kanan -->
        <div class="opacity-0 transform translate-x-10 animate-slide-left">
            <div class="space-y-6">
                <div class="animate-slide-up">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                        Tentang <span class="text-blue-600">Samira</span> <span class="text-green-600">Travel</span>
                    </h2>
                </div>

                <div class="animate-slide-up">
                    <p class="text-lg text-slate-700 leading-relaxed mb-8">
                        <?php echo htmlspecialchars($profil['deskripsi'] ?? 'Samira Travel hadir untuk melayani perjalanan ibadah Umrah dan Haji Anda dengan pelayanan terpercaya, fasilitas lengkap, dan bimbingan profesional yang berpengalaman.'); ?>
                    </p>
                </div>

                <!-- Vision & Mission -->
                <div class="grid gap-6 animate-slide-up">
                    <div class="bg-white rounded-lg p-6 shadow-lg">
                        <h3 class="text-xl font-semibold text-gray-800 mb-3">
                            <span class="text-2xl mr-2">🎯</span>
                            Visi Kami
                        </h3>
                        <p class="text-gray-600">
                            <?php echo htmlspecialchars($profil['visi'] ?? 'Menjadi agen travel terpercaya yang memberikan pelayanan terbaik untuk ibadah Umrah dan Haji.'); ?>
                        </p>
                    </div>

                    <div class="bg-white rounded-lg p-6 shadow-lg">
                        <h3 class="text-xl font-semibold text-gray-800 mb-3">
                            <span class="text-2xl mr-2">🚀</span>
                            Misi Kami
                        </h3>
                        <p class="text-gray-600">
                            <?php echo htmlspecialchars($profil['misi'] ?? 'Memberikan pelayanan terbaik dengan harga terjangkau dan kualitas premium untuk jamaah.'); ?>
                        </p>
                    </div>
                </div>

                <div class="animate-slide-up">
                    <a
                        href="<?php echo htmlspecialchars($waUrl); ?>"
                        target="_blank" rel="noopener noreferrer" onclick="window.open('<?php echo htmlspecialchars($waUrl); ?>','_blank')"
                        class="inline-flex items-center bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold px-8 py-4 rounded-full shadow-xl transition-all duration-300 transform hover:scale-105 hover:shadow-green-500/25 group">
                        <svg class="w-5 h-5 mr-3 animate-bounce-gentle" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.425 3.585" />
                        </svg>
                        <span class="group-hover:text-green-100 transition-colors duration-200">Konsultasi Gratis</span>
                    </a>
                </div>

                <!-- Contact Info -->
                <div class="mt-8 animate-slide-up bg-white rounded-lg p-6 shadow-lg">
                    <h3 class="text-xl font-semibold mb-4 text-gray-800">Hubungi Kami</h3>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <span class="text-blue-600 mr-3">📧</span>
                            <span class="text-gray-700"><?php echo htmlspecialchars($profil['email'] ?? 'info@samiratravel.com'); ?></span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-green-600 mr-3">📱</span>
                            <span class="text-gray-700"><?php echo htmlspecialchars($profil['telepon'] ?? '+62 857-0700-7870'); ?></span>
                        </div>
                        <div class="flex items-start">
                            <span class="text-red-600 mr-3 mt-1">📍</span>
                            <span class="text-gray-700"><?php echo htmlspecialchars($profil['alamat'] ?? 'Jakarta, Indonesia'); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Sponsor -->
                <div class="mt-12 animate-slide-up">
                    <h3 class="text-xl font-semibold mb-6 text-slate-800">Didukung oleh:</h3>
                    <div class="flex space-x-6 items-center">
                        <div>
                            <img
                                src="src/assets/sponsor.jpg"
                                alt="Sponsor"
                                class="h-12 object-contain" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    @keyframes slide-right {
        from {
            opacity: 0;
            transform: translateX(-50px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slide-left {
        from {
            opacity: 0;
            transform: translateX(50px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slide-up {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes bounce-gentle {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-5px);
        }
    }

    .animate-slide-right {
        animation: slide-right 1s ease-out forwards;
        animation-delay: 0.3s;
    }

    .animate-slide-left {
        animation: slide-left 1s ease-out forwards;
        animation-delay: 0.5s;
    }

    .animate-slide-up {
        animation: slide-up 0.8s ease-out forwards;
    }

    .animate-bounce-gentle {
        animation: bounce-gentle 2s ease-in-out infinite;
    }
</style>