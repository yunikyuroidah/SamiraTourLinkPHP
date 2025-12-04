<?php
require_once 'models/TourLeader.php';
require_once 'config/database.php';

// Ambil data tour leader dari database
$database = new Database();
$db = $database->getConnection();
$tourLeader = new TourLeader($db);
$stmt = $tourLeader->read();

// Set default values
$nama = "Sri Wahyuningsih";
$telepon = "+6285707007870";

if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $nama = $row['nama'];
    $telepon = $row['telepon'];
    $imgSrc = '';
    if (!empty($row['gambar_base64'])) {
        $raw = $row['gambar_base64'];
        $decoded = base64_decode($raw);
        $mime = 'image/jpeg';
        if (function_exists('finfo_open')) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $detected = $finfo->buffer($decoded);
            if ($detected) $mime = $detected;
        }
        $imgSrc = 'data:' . $mime . ';base64,' . $raw;
    }
}

$achievements = [
    ["icon" => "users", "label" => "2000+ Jamaah", "description" => "Telah mendampingi lebih dari 2000 jamaah"],
    ["icon" => "award", "label" => "Bersertifikat", "description" => "Memiliki sertifikat resmi dari Kemenag"],
    ["icon" => "star", "label" => "Rating 4.9/5", "description" => "Mendapat rating tinggi dari jamaah"],
    ["icon" => "shield", "label" => "Amanah", "description" => "Terpercaya dan bertanggung jawab penuh"]
];
?>

<!-- Floating Background Elements -->
<div class="absolute inset-0 overflow-hidden pointer-events-none">
    <div class="absolute top-20 right-20 w-24 h-24 bg-primary-200/20 rounded-full blur-xl animate-pulse"></div>
    <div class="absolute bottom-32 left-16 w-32 h-32 bg-skyblue-200/20 rounded-full blur-xl animate-bounce"></div>
    <div class="absolute top-1/3 left-1/3 w-16 h-16 bg-primary-300/10 rounded-full blur-lg animate-pulse"></div>
</div>

<div class="max-w-7xl mx-auto px-6 relative">
    <!-- Header Section -->
    <div class="text-center mb-16 animate-slide-down">
        <span class="text-sm font-semibold tracking-widest text-primary-600 uppercase mb-2 block">
            🎯 Pemimpin Perjalanan
        </span>
        <h2 class="text-5xl md:text-6xl font-bold mb-6">
            <span class="text-gray-900">Tour </span>
            <span class="bg-gradient-to-r from-primary-600 via-primary-500 to-skyblue-500 bg-clip-text text-transparent">
                Leader
            </span>
        </h2>
        <div class="w-24 h-1 bg-gradient-to-r from-primary-500 to-skyblue-500 mx-auto mb-6 rounded-full"></div>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
            Didampingi oleh tour leader berpengalaman yang akan memastikan perjalanan spiritual Anda berjalan dengan lancar dan berkesan.
        </p>
    </div>

    <div class="grid lg:grid-cols-2 gap-16 items-center">
        <!-- Profile Section -->
        <div class="animate-slide-right">
            <div class="relative group">
                <!-- Profile Image with Floating Effects -->
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-primary-400 to-skyblue-400 rounded-3xl blur-2xl opacity-30 group-hover:opacity-50 transition-opacity duration-500 animate-pulse"></div>
                    <div class="relative bg-white p-8 rounded-3xl shadow-2xl">
                        <div class="relative mx-auto w-64 h-64 mb-8">
                            <div class="absolute inset-0 bg-gradient-to-r from-primary-500 to-skyblue-500 rounded-full animate-pulse"></div>
                            <img src="<?php echo isset($imgSrc) && $imgSrc ? htmlspecialchars($imgSrc) : 'src/assets/profiltourleader.jpg'; ?>" alt="Tour Leader"
                                class="relative w-full h-full object-cover rounded-full border-4 border-white shadow-lg transform group-hover:scale-105 transition-transform duration-500">
                            <!-- Verification Badge -->
                            <div class="absolute -bottom-2 -right-2 bg-gradient-to-r from-primary-500 to-skyblue-500 p-3 rounded-full shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>

                        <!-- Profile Info -->
                        <div class="text-center">
                            <h3 class="text-3xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($nama); ?></h3>
                            <p class="text-primary-600 font-semibold mb-4 text-lg">Tour Leader Bersertifikat</p>
                            <div class="flex items-center justify-center space-x-4 mb-6">
                                <div class="flex items-center space-x-1 text-gray-600">
                                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="font-medium">15+ Tahun Pengalaman</span>
                                </div>
                                <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                                <div class="flex items-center space-x-1 text-gray-600">
                                    <svg class="w-5 h-5 text-skyblue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <span class="font-medium">Siap 24/7</span>
                                </div>
                            </div>
                            <p class="text-gray-600 leading-relaxed mb-6">
                                Tour Leader berpengalaman dengan dedikasi tinggi dalam melayani jamaah umrah. Telah mendampingi ribuan jamaah dengan penuh kesabaran dan keikhlasan.
                            </p>

                            <!-- Contact Button -->
                            <div class="flex justify-center">
                                <a href="<?php echo htmlspecialchars('https://wa.me/' . preg_replace('/\D+/', '', $telepon)); ?>" target="_blank" rel="noopener noreferrer"
                                    class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-8 py-3 rounded-full font-semibold hover:shadow-lg transform hover:scale-105 transition-all duration-300 flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    <span>Chat WhatsApp</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Achievements Grid -->
        <div class="animate-slide-left">
            <div class="space-y-6">
                <div class="mb-8">
                    <h3 class="text-3xl font-bold text-gray-900 mb-4">
                        Mengapa Memilih Tour Leader Kami?
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Kepercayaan ribuan jamaah menjadi bukti dedikasi dan profesionalisme dalam melayani perjalanan spiritual Anda.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($achievements as $index => $achievement): ?>
                        <div class="group bg-white/70 backdrop-blur-sm p-6 rounded-2xl shadow-lg hover:shadow-xl border border-primary-100/50 transform transition-all duration-500 hover:-translate-y-2 animate-fade-in">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-gradient-to-r from-primary-500 to-skyblue-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <?php
                                        $icons = [
                                            'users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-.5a4 4 0 110-8 4 4 0 010 8z" />',
                                            'award' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />',
                                            'star' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />',
                                            'shield' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />'
                                        ];
                                        ?>
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <?php echo $icons[$achievement['icon']]; ?>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-primary-600 transition-colors">
                                        <?php echo htmlspecialchars($achievement['label']); ?>
                                    </h4>
                                    <p class="text-gray-600 text-sm leading-relaxed">
                                        <?php echo htmlspecialchars($achievement['description']); ?>
                                    </p>
                                </div>
                            </div>
                            <!-- Hover Effect Line -->
                            <div class="h-1 bg-gradient-to-r from-primary-500 to-skyblue-500 rounded-full mt-4 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonial Section -->
    <div class="mt-20 text-center animate-slide-up">
        <div class="bg-white/70 backdrop-blur-sm p-8 rounded-2xl shadow-lg border border-primary-100/50 max-w-4xl mx-auto">
            <div class="flex items-center justify-center mb-6">
                <?php for ($i = 0; $i < 5; $i++): ?>
                    <svg class="w-6 h-6 text-yellow-400 fill-current" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                    </svg>
                <?php endfor; ?>
            </div>
            <blockquote class="text-xl text-gray-700 italic mb-6 leading-relaxed">
                "Alhamdulillah, perjalanan umrah bersama <?php echo htmlspecialchars($nama); ?> sangat berkesan. Beliau sangat sabar dan membantu kami memahami setiap rukun dan sunnah umrah dengan baik."
            </blockquote>
            <div class="flex items-center justify-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-r from-primary-500 to-skyblue-500 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <div class="text-left">
                    <p class="font-semibold text-gray-900">Ibu Siti Aminah</p>
                    <p class="text-gray-600 text-sm">Jamaah Umrah 2023</p>
                </div>
            </div>
        </div>
    </div>
</div>
</section>