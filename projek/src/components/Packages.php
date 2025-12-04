<?php
require_once __DIR__ . '/../../models/Paket.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/TourLeader.php';

// Ambil data paket dari database
$database = new Database();
$db = $database->getConnection();
$paket = new Paket($db);
$packages = [];

// Preferensi: gunakan read() yang mengembalikan PDOStatement (kompatibel)
$stmt = $paket->read();
if ($stmt instanceof PDOStatement) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row['fasilitas'] = json_decode($row['fasilitas'], true);
        $row['features'] = json_decode($row['features'], true);
        $packages[] = $row;
    }
} else {
    // fallback: gunakan getAll() yang mengembalikan array
    $rows = $paket->getAll();
    foreach ($rows as $row) {
        $row['fasilitas'] = json_decode($row['fasilitas'], true);
        $row['features'] = json_decode($row['features'], true);
        $packages[] = $row;
    }
}

// Debug banner: jika tidak ada paket, tunjukkan alasan dasar (bisa dimatikan nanti)
if (count($packages) === 0) {
    $debugMessages = [];
    if (!$db) {
        $debugMessages[] = 'Database connection is not available (\$db is null). Check config/database.php and database server.';
    } else {
        // jika koneksi ada tetapi tidak ada paket
        $debugMessages[] = 'Database connected, but no packages found in the `paket` table.';
    }

    // ensure tmp directory exists
    $tmpDir = __DIR__ . '/../../tmp';
    if (!is_dir($tmpDir)) {
        @mkdir($tmpDir, 0777, true);
    }
    $logLine = date('Y-m-d H:i:s') . ' - Packages empty - ' . implode(' | ', $debugMessages) . PHP_EOL;
    @file_put_contents($tmpDir . '/debug_packages.log', $logLine, FILE_APPEND);

    // show friendly notice on the page (visible to developer only)
    echo '<div class="container mx-auto px-6 py-6">';
    echo '<div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">';
    echo '<p class="font-bold">Debug: Paket tidak ditemukan</p>';
    foreach ($debugMessages as $m) {
        echo '<p class="text-sm">' . htmlspecialchars($m) . '</p>';
    }
    echo '<p class="text-xs text-gray-500 mt-2">Log ditulis ke tmp/debug_packages.log</p>';
    echo '</div></div>';
}

// Ambil nomor telepon dari data TourLeader (admin)
$waUrl = 'https://wa.me/6285707007870'; // fallback
try {
    $tourLeader = new TourLeader($db);
    $stmtLeader = $tourLeader->read();
    if ($stmtLeader instanceof PDOStatement) {
        $leaderRow = $stmtLeader->fetch(PDO::FETCH_ASSOC);
        $phoneRaw = $leaderRow['telepon'] ?? '';
        $digits = preg_replace('/\D+/', '', $phoneRaw);
        if (!empty($digits)) {
            $waUrl = 'https://wa.me/' . $digits;
        }
    }
} catch (Exception $e) {
    // keep fallback
}
?>

<section id="packages" class="py-20 bg-gradient-to-br from-slate-50 to-blue-50 relative overflow-hidden">
    <!-- Background Decorations -->
    <div class="absolute inset-0">
        <div class="absolute top-20 left-20 w-40 h-40 bg-primary-200/10 rounded-full blur-2xl animate-pulse-slow"></div>
        <div class="absolute bottom-20 right-20 w-48 h-48 bg-skyblue-200/10 rounded-full blur-2xl animate-pulse-slow" style="animation-delay: 2s;"></div>
    </div>

    <div class="max-w-7xl mx-auto px-6 relative z-10">
        <div class="text-center mb-16 animate-slide-down">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                Paket <span class="text-primary-600">Umroh</span> <span class="text-skyblue-600">&amp; Haji</span>
            </h2>
            <p class="text-lg text-slate-600 max-w-3xl mx-auto leading-relaxed">
                Pilih paket sesuai kebutuhan Anda. Semua paket didukung fasilitas
                terbaik & pembimbing berpengalaman.
            </p>
        </div>

        <div class="overflow-x-auto pb-4">
            <div class="flex space-x-8 pb-6" style="scroll-behavior: smooth;">
                <?php foreach ($packages as $index => $pkg): ?>
                    <div class="min-w-[380px] bg-white/70 backdrop-blur-sm rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 flex-shrink-0 p-8 border border-white/50 group hover:scale-105 transform animate-fade-in"
                        style="animation-delay: <?php echo $index * 0.2; ?>s;">
                        <!-- Header with gradient -->
                        <div class="bg-gradient-to-r from-primary-500 to-skyblue-500 text-white p-4 rounded-xl mb-6 group-hover:shadow-lg transition-all duration-300">
                            <h3 class="text-2xl font-bold">
                                <?php echo htmlspecialchars($pkg['nama_paket']); ?>
                            </h3>
                        </div>

                        <p class="text-slate-600 mb-6 leading-relaxed"><?php echo htmlspecialchars($pkg['deskripsi']); ?></p>

                        <!-- Features Section -->
                        <div class="mb-6">
                            <h4 class="font-bold text-slate-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-primary-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Fitur Utama:
                            </h4>
                            <div class="space-y-2">
                                <?php if (is_array($pkg['features'])): ?>
                                    <?php foreach ($pkg['features'] as $j => $feature): ?>
                                        <div class="flex items-center space-x-3 animate-slide-right"
                                            style="animation-delay: <?php echo ($index * 0.2) + ($j * 0.1); ?>s;">
                                            <div class="w-2 h-2 bg-primary-400 rounded-full flex-shrink-0"></div>
                                            <span class="text-slate-700"><?php echo htmlspecialchars($feature); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Facilities Section -->
                        <div class="mb-8">
                            <h4 class="font-bold text-slate-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-skyblue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Fasilitas:
                            </h4>
                            <div class="space-y-2">
                                <?php if (is_array($pkg['fasilitas'])): ?>
                                    <?php foreach ($pkg['fasilitas'] as $j => $fasilitas): ?>
                                        <div class="flex items-center space-x-3 animate-slide-right"
                                            style="animation-delay: <?php echo ($index * 0.2) + ($j * 0.1) + 0.3; ?>s;">
                                            <div class="w-2 h-2 bg-skyblue-400 rounded-full flex-shrink-0"></div>
                                            <span class="text-slate-700"><?php echo htmlspecialchars($fasilitas); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <a href="<?php echo htmlspecialchars($waUrl); ?>"
                            class="w-full inline-flex items-center justify-center bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold px-6 py-4 rounded-full shadow-xl transition-all duration-300 transform hover:scale-105 hover:shadow-green-500/25 group/btn">
                            <svg class="w-5 h-5 mr-3 animate-bounce-gentle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span class="group-hover/btn:text-green-100 transition-colors duration-200">Konsultasi via WhatsApp</span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Scroll Hint -->
        <?php if (count($packages) > 2): ?>
            <div class="text-center mt-8 animate-bounce">
                <p class="text-slate-500 text-sm flex items-center justify-center space-x-2">
                    <span>Geser untuk melihat paket lainnya</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </p>
            </div>
        <?php endif; ?>
    </div>
</section>