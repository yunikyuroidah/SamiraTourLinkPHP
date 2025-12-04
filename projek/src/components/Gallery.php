<?php
require_once __DIR__ . '/../../models/GalleryAsset.php';

// Get gallery data from database
$gallery = new GalleryAsset();
$dbImages = $gallery->all();

// Transform database data to match our gallery format
$images = array_map(function ($img) {
    // Prefer inline content if available (DB-stored base64). Fall back to filename-based src.
    $src = null;
    if (!empty($img['content']) || !empty($img['content_base64'])) {
        $content = $img['content'] ?? $img['content_base64'];
        $mime = 'image/jpeg';
        $decoded = base64_decode($content, true);
        if ($decoded !== false && function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $detected = finfo_buffer($finfo, $decoded);
                if ($detected) $mime = $detected;
                finfo_close($finfo);
            }
        }
        $src = 'data:' . $mime . ';base64,' . $content;
    } elseif (!empty($img['filename'])) {
        // if filename actually contains base64 payload (we stored DB-only),
        // detect and render as data URI
        $fn = $img['filename'];
        $looksLikeBase64 = is_string($fn) && strlen($fn) > 200 && preg_match('/^[A-Za-z0-9\/+=\r\n]+$/', $fn);
        if ($looksLikeBase64) {
            $mime = 'image/jpeg';
            if (function_exists('finfo_open')) {
                $decoded = base64_decode($fn, true);
                if ($decoded !== false) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $detected = finfo_buffer($finfo, $decoded);
                    if ($detected) $mime = $detected;
                    finfo_close($finfo);
                }
            }
            $src = 'data:' . $mime . ';base64,' . $fn;
        } else {
            $src = 'src/assets/gallery/' . $img['filename'];
        }
    }

    return [
        'src' => $src,
        'alt' => $img['name'] ?? null,
        'caption' => $img['name'] ?? null,
        // prefer `deskripsi` column from DB (admin uses name + deskripsi)
        'description' => $img['deskripsi'] ?? ($img['description'] ?? 'Dokumentasi perjalanan bersama Samira Travel'),
        // Default stats for now
        'likes' => rand(500, 3000) . (rand(0, 1) ? '' : 'k'),
        'views' => rand(5000, 15000) . (rand(0, 1) ? '' : 'k')
    ];
}, $dbImages);

// Gallery categories
// categories removed — gallery will show all images without filter buttons
?>

<!-- Gallery Section -->
<section id="gallery" class="py-20 bg-gradient-to-br from-gray-50 to-blue-50 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0">
        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-blue-50/30 to-green-50/30"></div>
        <div class="absolute top-20 right-20 w-32 h-32 bg-blue-200/20 rounded-full blur-xl animate-pulse"></div>
        <div class="absolute bottom-20 left-20 w-40 h-40 bg-green-200/20 rounded-full blur-xl animate-pulse" style="animation-delay: 1s;"></div>
    </div>

    <div class="max-w-7xl mx-auto px-6 relative z-10">
        <?php if (!isset($compact) || !$compact): ?>
            <!-- Header -->
            <div class="text-center mb-16 opacity-0 transform -translate-y-10 animate-slide-down">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                    Galeri <span class="text-blue-600">Perjalanan</span> <span class="text-green-600">Spiritual</span>
                </h2>
                <p class="text-lg text-slate-600 max-w-3xl mx-auto leading-relaxed">
                    Dokumentasi perjalanan ibadah umrah dan haji bersama Samira Travel.
                    Saksikan kebahagiaan dan kekhusyukan jamaah dalam menjalankan ibadah.
                </p>
            </div>
        <?php endif; ?>

        <!-- Filter kategori dihilangkan -->

        <!-- Image Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
            <?php if (empty($images) && (!isset($compact) || !$compact)): ?>
                <div class="col-span-3 text-center py-12">
                    <div class="text-gray-500">Belum ada gambar dalam galeri</div>
                </div>
            <?php else: ?>
                <?php foreach ($images as $index => $image): ?>
                    <div class="gallery-item group cursor-pointer opacity-0 transform translate-y-10 animate-fade-in"
                        style="animation-delay: <?php echo $index * 0.2; ?>s;">

                        <div class="relative overflow-hidden rounded-2xl shadow-xl bg-white transform transition-all duration-500 hover:scale-105 hover:-translate-y-2 hover:shadow-2xl">
                            <!-- Image -->
                            <div class="relative overflow-hidden">
                                <img
                                    src="<?php echo htmlspecialchars($image['src']); ?>"
                                    alt="<?php echo htmlspecialchars($image['alt']); ?>"
                                    class="w-full h-64 object-cover transition-transform duration-700 group-hover:scale-110"
                                    loading="lazy"
                                    onerror="this.src='src/assets/placeholder.jpg'">

                                <!-- Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                                <!-- Category badge removed (using description instead) -->

                                <!-- Hover Actions -->
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                                    <div class="flex space-x-4">
                                        <button class="bg-white/20 backdrop-blur-sm p-3 rounded-full text-white hover:bg-white/30 transition-colors duration-200"
                                            onclick="openLightbox(<?php echo $index; ?>)">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>

                                        <button class="bg-white/20 backdrop-blur-sm p-3 rounded-full text-white hover:bg-white/30 transition-colors duration-200">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors duration-300">
                                    <?php echo htmlspecialchars($image['caption']); ?>
                                </h3>
                                <p class="text-gray-600 text-sm mb-4 leading-relaxed line-clamp-2">
                                    <?php echo htmlspecialchars($image['description'] ?: 'Dokumentasi perjalanan spiritual bersama Samira Travel'); ?>
                                </p>

                                <!-- Stats -->
                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                        </svg>
                                        <?php echo htmlspecialchars($image['likes']); ?>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <?php echo htmlspecialchars($image['views']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (!isset($compact) || !$compact): ?>
            <!-- View More Button removed per request -->
        <?php endif; ?>
    </div>
</section>

<!-- Lightbox Modal -->
<div id="lightbox" class="fixed inset-0 bg-black/90 z-50 hidden items-center justify-center p-4">
    <div class="relative max-w-4xl w-full">
        <!-- Close Button -->
        <button
            onclick="closeLightbox()"
            class="absolute top-4 right-4 text-white text-2xl z-10 bg-black/50 rounded-full p-2 hover:bg-black/70 transition-colors duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Image -->
        <img id="lightbox-image" src="" alt="" class="w-full h-auto rounded-lg">

        <!-- Caption -->
        <div class="text-center mt-4 text-white">
            <h3 id="lightbox-caption" class="text-xl font-bold mb-2"></h3>
            <p id="lightbox-description" class="text-gray-300"></p>
        </div>

        <!-- Navigation -->
        <button
            onclick="previousImage()"
            class="absolute left-4 top-1/2 transform -translate-y-1/2 text-white text-2xl bg-black/50 rounded-full p-2 hover:bg-black/70 transition-colors duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        <button
            onclick="nextImage()"
            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white text-2xl bg-black/50 rounded-full p-2 hover:bg-black/70 transition-colors duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>
</div>

<script>
    let currentImageIndex = 0;
    const galleryImages = <?php echo json_encode($images); ?>;

    // Category filter JS removed — no filter buttons present

    // Lightbox functionality
    function openLightbox(index) {
        currentImageIndex = index;
        const image = galleryImages[index];

        document.getElementById('lightbox-image').src = image.src;
        document.getElementById('lightbox-image').alt = image.alt;
        document.getElementById('lightbox-caption').textContent = image.caption;
        document.getElementById('lightbox-description').textContent = image.description;

        document.getElementById('lightbox').classList.remove('hidden');
        document.getElementById('lightbox').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        document.getElementById('lightbox').classList.add('hidden');
        document.getElementById('lightbox').classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    function nextImage() {
        currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
        openLightbox(currentImageIndex);
    }

    function previousImage() {
        currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
        openLightbox(currentImageIndex);
    }

    // Close lightbox on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLightbox();
        } else if (e.key === 'ArrowRight') {
            nextImage();
        } else if (e.key === 'ArrowLeft') {
            previousImage();
        }
    });

    // Close lightbox on background click
    document.getElementById('lightbox').addEventListener('click', function(e) {
        if (e.target === this) {
            closeLightbox();
        }
    });
</script>

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

    .animate-slide-down {
        animation: slide-down 1s ease-out forwards;
        animation-delay: 0.2s;
    }

    .animate-slide-up {
        animation: slide-up 0.8s ease-out forwards;
        animation-delay: 0.4s;
    }

    .animate-fade-in {
        animation: fade-in 0.8s ease-out forwards;
    }

    .animate-fade-in-delayed {
        animation: fade-in 1s ease-out forwards;
        animation-delay: 1s;
    }

    /* Gallery item hover effects */
    .gallery-item:hover .group-hover\:scale-110 {
        transform: scale(1.1);
    }

    /* Smooth transitions for all interactive elements */
    * {
        transition: all 0.3s ease;
    }
</style>