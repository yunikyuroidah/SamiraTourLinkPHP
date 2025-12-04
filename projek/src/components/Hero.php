<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../models/ProfilTravel.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/TourLeader.php';

// Get company profile and fall back to the first available row if ID lookup fails
$profilTravel = new ProfilTravel();
$profil = $profilTravel->getById();
if (!$profil) {
    $stmt = $profilTravel->read();
    if ($stmt instanceof PDOStatement) {
        $profil = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}

function hero_image_src($value)
{
    if (empty($value)) {
        return null;
    }

    if (stripos($value, 'data:image') === 0) {
        return $value;
    }

    $maybeBase64 = preg_replace('/\s+/', '', (string)$value);
    if (strlen($maybeBase64) > 40 && strlen($maybeBase64) % 4 === 0 && base64_decode($maybeBase64, true) !== false) {
        $mime = 'image/jpeg';
        $decoded = base64_decode($maybeBase64, true);
        if ($decoded !== false && function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $detected = finfo_buffer($finfo, $decoded);
                if ($detected) {
                    $mime = $detected;
                }
                finfo_close($finfo);
            }
        }
        return 'data:' . $mime . ';base64,' . $maybeBase64;
    }

    $normalized = str_replace('\\', '/', $value);
    if (preg_match('~^https?://~i', $normalized)) {
        return $normalized;
    }

    $normalized = ltrim($normalized, '/');

    if (strpos($normalized, '../') === 0) {
        $normalized = preg_replace('~^\.\./+~', '', $normalized);
    }
    if (strpos($normalized, './') === 0) {
        $normalized = preg_replace('~^(\./)+~', '', $normalized);
    }

    if (strpos($normalized, 'src/') !== 0) {
        if (strpos($normalized, 'assets/') === 0) {
            $normalized = 'src/' . $normalized;
        } elseif (strpos($normalized, 'uploads/') === 0) {
            $normalized = 'src/assets/' . $normalized;
        }
    }

    $candidates = [__DIR__ . '/../../' . $normalized];
    if (strpos($normalized, 'src/') !== 0) {
        $candidates[] = __DIR__ . '/../../src/' . $normalized;
    }

    $absolute = null;
    foreach ($candidates as $candidate) {
        $real = realpath($candidate);
        if ($real && is_file($real)) {
            $absolute = $real;
            break;
        }
    }

    if ($absolute && is_file($absolute)) {
        $docRoot = realpath(__DIR__ . '/../../');
        if ($docRoot && strpos($absolute, $docRoot) === 0) {
            $relativePath = ltrim(str_replace('\\', '/', substr($absolute, strlen($docRoot))), '/');
            return asset($relativePath);
        }
    }

    return null;
}

// build wa.me url from TourLeader admin phone (fallback to profile phone)
$waUrl = 'https://wa.me/6285707007870';
try {
    $database = new Database();
    $db = $database->getConnection();
    $tl = new TourLeader($db);
    $stmt = $tl->read();
    $raw = '';
    if ($stmt instanceof PDOStatement) {
        $leader = $stmt->fetch(PDO::FETCH_ASSOC);
        $raw = $leader['telepon'] ?? '';
    }
    if (empty($raw)) {
        $raw = $profil['telepon'] ?? '';
    }
    $digits = preg_replace('/[^0-9]/', '', $raw);
    if (!empty($digits)) {
        $waUrl = 'https://wa.me/' . $digits;
    }
} catch (Exception $e) {
    // keep fallback
}
?>

<!-- Hero Section -->
<section class="relative bg-black/30 text-white overflow-hidden min-h-screen flex items-center">
    <!-- Background Image (replaced video with static image) -->
    <div class="absolute inset-0 w-full h-full overflow-hidden">
        <?php
        // Use profile-managed banner if present, otherwise fall back to default
        $heroSrc = hero_image_src($profil['image'] ?? '') ?? asset('src/assets/profiltravel1.jpg');
        ?>
        <img src="<?php echo htmlspecialchars($heroSrc, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($profil['nama'] ?? 'Samira Travel'); ?>" class="w-full h-full object-cover opacity-30">
    </div>

    <!-- Dark Overlay -->
    <div class="absolute inset-0 bg-black bg-opacity-70"></div>

    <!-- Content -->
    <div class="relative z-10 container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <!-- Logo -->
            <div class="mb-8 animate-fade-in">
                <img
                    src="<?php echo asset('src/assets/logo.png'); ?>"
                    alt="<?php echo htmlspecialchars($profil['nama'] ?? 'Samira Travel'); ?>"
                    class="mx-auto h-28 w-auto mb-4 hover:scale-110 transition-transform duration-500">
            </div>

            <!-- Main Heading -->
            <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight animate-slide-up">
                <?php echo htmlspecialchars($profil['nama'] ?? 'SAMIRA TRAVEL'); ?>
            </h1>

            <!-- Subtitle -->
            <p class="text-xl md:text-2xl mb-8 text-gray-200 font-medium animate-slide-up opacity-90">
                <?php echo htmlspecialchars($profil['visi'] ?? 'Sahabat Umroh & Haji Keluarga Anda'); ?>
            </p>

            <!-- Call to Action Button -->
            <div class="animate-slide-up">
                <a
                    href="<?php echo htmlspecialchars($waUrl); ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold px-8 py-4 rounded-full shadow-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-green-500/25 group">
                    <svg class="w-6 h-6 mr-3 animate-bounce-gentle" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.425 3.585" />
                    </svg>
                    <span class="group-hover:text-green-100 transition-colors duration-200">Hubungi via WhatsApp</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <div class="w-6 h-10 border-2 border-white/50 rounded-full p-1">
            <div class="w-1 h-3 bg-white/70 rounded-full mx-auto animate-pulse"></div>
        </div>
    </div>
</section>

<style>
    @keyframes fade-in {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
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

    .animate-fade-in {
        animation: fade-in 1s ease-out forwards;
        animation-delay: 0.3s;
        opacity: 0;
    }

    .animate-slide-up {
        animation: slide-up 1s ease-out forwards;
        animation-delay: 0.6s;
        opacity: 0;
    }

    .animate-bounce-gentle {
        animation: bounce-gentle 2s ease-in-out infinite;
    }
</style>