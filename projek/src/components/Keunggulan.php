<?php
// Keunggulan data
$items = [
    ['text' => 'Legalitas resmi & terpercaya', 'icon' => '🏛️'],
    ['text' => 'Pembimbing berpengalaman', 'icon' => '👨‍🏫'],
    ['text' => 'Fasilitas hotel terbaik', 'icon' => '🏨'],
    ['text' => 'Pesawat langsung tanpa transit', 'icon' => '✈️'],
    ['text' => 'Harga terjangkau', 'icon' => '💰'],
    ['text' => 'Jadwal fleksibel', 'icon' => '📅'],
    ['text' => 'Layanan customer care 24/7', 'icon' => '🛎️'],
    ['text' => 'Ribuan jamaah puas bersama kami', 'icon' => '⭐'],
];

// Statistics data
$stats = [
    ['number' => '500+', 'label' => 'Jamaah Terlayani', 'color' => 'from-blue-500 to-blue-600'],
    ['number' => '15+', 'label' => 'Tahun Pengalaman', 'color' => 'from-green-500 to-green-600'],
    ['number' => '98%', 'label' => 'Tingkat Kepuasan', 'color' => 'from-yellow-500 to-yellow-600'],
];
?>

<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/TourLeader.php';

// default wa url fallback
$waUrl = 'https://wa.me/6285707007870';
try {
    $database = new Database();
    $db = $database->getConnection();
    $tl = new TourLeader($db);
    $stmt = $tl->read();
    if ($stmt instanceof PDOStatement) {
        $leader = $stmt->fetch(PDO::FETCH_ASSOC);
        $raw = $leader['telepon'] ?? '';
        $digits = preg_replace('/\D+/', '', $raw);
        if (!empty($digits)) {
            $waUrl = 'https://wa.me/' . $digits;
        }
    }
} catch (Exception $e) {
    // ignore and use fallback
}
?>

<!-- Keunggulan Section -->
<section id="keunggulan-section" class="py-20 bg-white relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0">
        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-blue-50/30 to-green-50/30"></div>
        <div class="absolute top-10 right-10 w-32 h-32 bg-blue-200/20 rounded-full blur-xl animate-pulse"></div>
        <div class="absolute bottom-10 left-10 w-40 h-40 bg-green-200/20 rounded-full blur-xl animate-pulse" style="animation-delay: 1s;"></div>
    </div>

    <div class="max-w-7xl mx-auto px-6 relative z-10">
        <div class="text-center mb-16 opacity-0 transform -translate-y-10 animate-slide-down">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                Keunggulan <span class="text-blue-600">Samira</span> <span class="text-green-600">Travel</span>
            </h2>
            <p class="text-lg text-slate-600 max-w-3xl mx-auto leading-relaxed">
                Alasan mengapa ribuan jamaah mempercayakan perjalanan ibadahnya kepada kami
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($items as $i => $item): ?>
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-6 hover:shadow-2xl transition-all duration-500 transform hover:scale-105 hover:-translate-y-2 border border-white/50 group opacity-0 animate-fade-in"
                    style="animation-delay: <?php echo $i * 0.1; ?>s;">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="relative">
                                <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-green-500/20 rounded-full blur group-hover:blur-md transition-all duration-300"></div>
                                <div class="relative bg-gradient-to-r from-blue-500 to-green-500 p-3 rounded-full">
                                    <svg class="text-white w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <span class="text-2xl mr-2">
                                    <?php echo $item['icon']; ?>
                                </span>
                            </div>
                            <p class="text-slate-700 leading-relaxed group-hover:text-slate-900 transition-colors duration-300">
                                <?php echo htmlspecialchars($item['text']); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Hover Effect Border -->
                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-blue-500/10 to-green-500/10 opacity-0 group-hover:opacity-100 transition-all duration-300 -z-10"></div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Statistics Section -->
        <div class="mt-16 grid md:grid-cols-3 gap-8 opacity-0 transform translate-y-10 animate-slide-up-delayed">
            <?php foreach ($stats as $index => $stat): ?>
                <div class="text-center p-6 bg-gradient-to-br <?php echo $stat['color']; ?> rounded-2xl text-white shadow-xl transform hover:scale-105 transition-all duration-300">
                    <div class="text-3xl font-bold mb-2 animate-pulse-gentle" style="animation-delay: <?php echo $index * 0.5; ?>s;">
                        <?php echo htmlspecialchars($stat['number']); ?>
                    </div>
                    <div class="text-white/80">
                        <?php echo htmlspecialchars($stat['label']); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Call to Action -->
        <div class="mt-16 text-center opacity-0 transform translate-y-10 animate-fade-in-cta">
            <div class="bg-gradient-to-r from-blue-600 to-green-600 rounded-3xl p-12 text-white shadow-2xl">
                <h3 class="text-3xl font-bold mb-6">
                    Siap Memulai Perjalanan Spiritual Anda?
                </h3>
                <p class="text-xl mb-8 opacity-90">
                    Hubungi kami sekarang untuk konsultasi gratis dan dapatkan paket terbaik
                </p>
                <a
                    href="<?php echo htmlspecialchars($waUrl); ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center bg-white text-blue-600 font-bold px-8 py-4 rounded-full shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 group">
                    <svg class="w-6 h-6 mr-3 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.425 3.585" />
                    </svg>
                    <span class="group-hover:text-green-600 transition-colors duration-200">
                        Konsultasi Sekarang
                    </span>
                </a>
            </div>
        </div>
    </div>
</section>

<style>
    @keyframes slide-down {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slide-up {
        from {
            opacity: 0;
            transform: translateY(50px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pulse-gentle {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }
    }

    .animate-slide-down {
        animation: slide-down 1s ease-out forwards;
        animation-delay: 0.2s;
    }

    .animate-fade-in {
        animation: fade-in 0.8s ease-out forwards;
    }

    .animate-slide-up-delayed {
        animation: slide-up 1s ease-out forwards;
        animation-delay: 0.8s;
    }

    .animate-fade-in-cta {
        animation: fade-in 1s ease-out forwards;
        animation-delay: 1.2s;
    }

    .animate-pulse-gentle {
        animation: pulse-gentle 2s ease-in-out infinite;
    }
</style>