<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit();
}

require_once '../../models/ProfilTravel.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();
$profilTravel = new ProfilTravel($db);

$message = '';
$error = '';

// 1. Ambil data profil travel sebelum post untuk mendapatkan nama file gambar lama
$stmt = $profilTravel->read();
$profil = null;
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $profil = $row;
}
$existing_image = $profil['image'] ?? null; // Simpan konten gambar lama (bisa base64 atau path)
$existing_image_src_data = build_profile_image_src($existing_image);
$existing_image_src = $existing_image_src_data[0];

function is_base64_string($value)
{
    if (!is_string($value) || strlen($value) < 40) {
        return false;
    }
    $clean = preg_replace('/\s+/', '', $value);
    if ($clean === '' || strlen($clean) % 4 !== 0) {
        return false;
    }
    return base64_decode($clean, true) !== false;
}

function resolve_profile_image_path($value)
{
    if (empty($value) || is_base64_string($value)) {
        return null;
    }

    $normalized = trim((string)$value);
    if ($normalized === '') {
        return null;
    }

    $normalized = str_replace('\\', '/', $normalized);
    $normalized = preg_replace('~^(\./|\.\./)+~', '', $normalized);
    $normalized = ltrim($normalized, '/');

    if (strpos($normalized, 'src/') !== 0) {
        if (strpos($normalized, 'assets/') === 0) {
            $normalized = 'src/' . $normalized;
        } elseif (strpos($normalized, 'uploads/') === 0) {
            $normalized = 'src/assets/' . $normalized;
        }
    }

    $candidates = [
        __DIR__ . '/../../' . $normalized,
    ];

    if (strpos($normalized, 'src/assets/') === 0) {
        $relativeWithinAssets = substr($normalized, strlen('src/assets/'));
        $candidates[] = __DIR__ . '/../assets/' . $relativeWithinAssets;
    } else {
        $candidates[] = __DIR__ . '/../assets/' . $normalized;
    }

    $candidates[] = __DIR__ . '/../assets/' . basename($normalized);

    foreach ($candidates as $candidate) {
        $real = realpath($candidate);
        if ($real && is_file($real)) {
            return $real;
        }
    }

    return null;
}

function build_profile_image_src($value)
{
    if (empty($value)) {
        return [null, null];
    }

    if (is_base64_string($value)) {
        $decoded = base64_decode($value, true);
        $mime = 'image/jpeg';
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
        return ['data:' . $mime . ';base64,' . $value, $mime];
    }

    $path = resolve_profile_image_path($value);
    if (!$path) {
        return [null, null];
    }

    $normalizedPath = str_replace('\\', '/', $path);
    $assetsRoot = realpath(__DIR__ . '/../assets');
    if ($assetsRoot) {
        $assetsRoot = str_replace('\\', '/', $assetsRoot);
    }

    if ($assetsRoot && strpos($normalizedPath, $assetsRoot) === 0) {
        $relative = ltrim(substr($normalizedPath, strlen($assetsRoot)), '/');
        return ['../assets/' . $relative, null];
    }

    return [null, null];
}

function translate_upload_error($code)
{
    switch ($code) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            $maxFile = ini_get('upload_max_filesize');
            $postMax = ini_get('post_max_size');
            $limit = $maxFile ?: $postMax;
            return 'Ukuran file melampaui batas server (upload_max_filesize/post_max_size ≈ ' . ($limit ?: 'php.ini') . ').';
        case UPLOAD_ERR_PARTIAL:
            return 'Upload banner terhenti sebelum selesai. Coba ulangi.';
        case UPLOAD_ERR_NO_FILE:
            return 'Tidak ada file yang diunggah.';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Server tidak menemukan folder sementara untuk upload.';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Server gagal menyimpan file ke disk (periksa permission).';
        case UPLOAD_ERR_EXTENSION:
            return 'Upload dibatalkan oleh ekstensi PHP.';
        default:
            return 'Upload banner gagal (Kode Error: ' . $code . ').';
    }
}

// Handle form submission (Post-Redirect-Get using session flash)
if ($_POST) {
    $profilTravel->alamat = $_POST['alamat'] ?? null;
    $profilTravel->email = $_POST['email'] ?? null;
    $profilTravel->nama = $_POST['nama'] ?? null;
    $profilTravel->visi = $_POST['visi'] ?? null;

    // Defaultkan gambar ke konten lama
    $new_image_content = $existing_image;
    $new_image_full_path = null;
    $old_image_full_path = null;

    // Banner upload handling
    if (isset($_FILES['banner']) && $_FILES['banner']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['banner'];
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error = translate_upload_error($file['error']);
        } else {
            $tmp = $file['tmp_name'];
            $detectedMime = null;
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                if ($finfo) {
                    $detectedMime = finfo_file($finfo, $tmp);
                    finfo_close($finfo);
                }
            }

            if (!$detectedMime && function_exists('getimagesize')) {
                $imageInfo = @getimagesize($tmp);
                if (is_array($imageInfo) && isset($imageInfo['mime'])) {
                    $detectedMime = $imageInfo['mime'];
                }
            }

            $mime = $detectedMime ?: ($file['type'] ?? '');

            if (!in_array($mime, $allowed)) {
                $error = 'Format banner harus JPG, PNG, GIF, atau WEBP.';
            } else {
                $extensions = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/gif' => 'gif',
                    'image/webp' => 'webp',
                ];
                $ext = $extensions[$mime] ?? strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
                if ($ext === 'jpeg') {
                    $ext = 'jpg';
                }
                if (!$ext) {
                    $ext = 'jpg';
                }

                $uploadDir = __DIR__ . '/../assets/uploads';
                if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true)) {
                    $error = 'Folder upload tidak dapat dibuat.';
                } else {
                    try {
                        $unique = bin2hex(random_bytes(4));
                    } catch (Exception $e) {
                        $unique = uniqid();
                    }

                    $newFileName = 'banner_' . date('Ymd_His') . '_' . $unique . '.' . $ext;
                    $destination = $uploadDir . '/' . $newFileName;

                    if (!move_uploaded_file($tmp, $destination)) {
                        $error = 'Gagal menyimpan file banner.';
                    } else {
                        $new_image_full_path = $destination;
                        $new_image_content = 'src/assets/uploads/' . $newFileName;
                        $oldPath = resolve_profile_image_path($existing_image);
                        if ($oldPath && strpos(str_replace('\\', '/', $oldPath), str_replace('\\', '/', realpath(__DIR__ . '/../assets/uploads'))) === 0) {
                            $old_image_full_path = $oldPath;
                        }
                    }
                }
            }
        }
    }

    // Hanya lakukan update jika tidak ada error dari pengecekan atau upload
    if (empty($error)) {
        // Set konten gambar, baik lama maupun baru
        $profilTravel->image = $new_image_content;

        $res = $profilTravel->update();

        if ($res) {
            if ($old_image_full_path && is_file($old_image_full_path)) {
                @unlink($old_image_full_path);
            }
            $_SESSION['flash_message'] = "Profil travel berhasil diupdate!";
            header('Location: admin-profile-travel.php');
            exit();
        } else {
            if ($new_image_full_path && is_file($new_image_full_path)) {
                @unlink($new_image_full_path);
            }
            // Jika update database gagal, set error
            $errMsg = $profilTravel->lastError ?? 'Database error';
            $error = "Gagal mengupdate profil travel. (" . htmlspecialchars($errMsg) . ")";
        }
    }
}

// Show flash message once (if set)
if (!empty($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}

// Re-read data after potential update for display if no redirect happened (e.g., on error)
if (empty($_POST) || !empty($error)) {
    $stmt = $profilTravel->read();
    $profil = null;
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $profil = $row;
    }
}

if ($profil !== null) {
    $existing_image = $profil['image'] ?? null;
    $existing_image_src_data = build_profile_image_src($existing_image);
    $existing_image_src = $existing_image_src_data[0];
}

// Set default value if profil is null
$default_alamat = 'Perum Graha Kota Blok D4 No.16, Suko, Sidoarjo';
$default_email = 'samiratravel@gmail.com';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Travel - Admin Samira Travel</title>
    <?php include 'admin-shared-head.php'; ?>
    <style>
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            border: 1px solid #d1d5db; /* gray-300 */
            border-radius: 0.5rem; /* rounded-lg */
            cursor: pointer;
            padding: 0.5rem 1rem;
            background-color: #f9fafb; /* gray-50 */
            color: #4b5563; /* gray-600 */
            transition: all 0.2s;
        }

        .file-input-wrapper:hover {
            background-color: #e5e7eb; /* gray-100 */
            border-color: #9ca3af; /* gray-400 */
        }

        .file-input-wrapper input[type=file] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
        }
    </style>
</head>

<body class="bg-gray-50 font-sans">
    <div class="min-h-screen flex">
        <?php $activePage = 'profile'; include 'admin-sidebar.php'; ?>

        <div class="content-area">
            <div class="flex justify-between items-center mb-8 pb-4 border-b-2 border-deep-navy/10 shadow-sm">
                <div>
                    <h1 class="text-3xl font-bold text-deep-navy">Profil Travel</h1>
                    <p class="text-gray-500 mt-1">Kelola informasi profil travel agency</p>
                </div>
                <a href="../../index.php" target="_blank" title="Lihat Halaman Depan"
                    class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-samira-teal text-white shadow-lg hover:bg-samira-teal-dark transition-transform transform hover:-translate-y-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
            </div>

            <main>
                <?php if ($message): ?>
                    <div class="mb-6 p-4 text-sm bg-green-100 text-green-700 rounded-lg border border-green-300 font-medium flex items-center space-x-3">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <p><?php echo htmlspecialchars($message); ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="mb-6 p-4 text-sm bg-red-100 text-red-700 rounded-lg border border-red-300 font-medium flex items-center space-x-3">
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    </div>
                <?php endif; ?>

                <div class="max-w-3xl mx-auto space-y-8">
                    <div class="card-panel space-y-8">
                        <div class="flex items-center space-x-4 mb-8 border-b pb-4">
                            <div class="p-3 bg-deep-navy rounded-xl">
                                <svg class="w-8 h-8 text-samira-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">Form Update Informasi Travel</h2>
                                <p class="text-gray-600">Update informasi kontak, alamat, dan banner utama.</p>
                            </div>
                        </div>

                        <form method="POST" enctype="multipart/form-data" class="space-y-6">
                            <div>
                                <label for="alamat" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Alamat Lengkap
                                </label>
                                <textarea
                                    id="alamat"
                                    name="alamat"
                                    required
                                    rows="4"
                                    class="input-field"
                                    placeholder="Masukkan alamat lengkap travel agency"><?php echo $profil ? htmlspecialchars($profil['alamat']) : $default_alamat; ?></textarea>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Email
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    required
                                    value="<?php echo $profil ? htmlspecialchars($profil['email']) : $default_email; ?>"
                                    class="input-field"
                                    placeholder="Masukkan alamat email">
                            </div>

                            <div>
                                <label for="banner" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Banner Situs (gambar atas)
                                </label>
                                <div class="mt-1 flex flex-col gap-3">
                                    <div class="file-input-wrapper">
                                        Pilih File Banner Baru
                                        <input type="file" id="banner" name="banner" accept="image/*" />
                                    </div>

                                    <div class="text-sm text-gray-500">
                                        Gambar saat ini: <span class="font-medium text-gray-700"><?php echo $existing_image ? 'Sudah ada banner tersimpan' : 'Belum ada banner terpasang'; ?></span>
                                    </div>

                                    <p class="text-xs text-gray-500">Gunakan format JPG, PNG, GIF, atau WEBP. (Batas ukuran mengikuti pengaturan server PHP.)</p>

                                    <div id="banner-preview" class="mt-3 <?php echo empty($existing_image_src) ? 'hidden' : ''; ?>">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Preview Banner:</label>
                                        <img src="<?php echo htmlspecialchars($existing_image_src ?? '#'); ?>" alt="Preview Banner" class="w-full h-48 object-cover rounded-xl border-2 border-gray-300 shadow-md" />
                                    </div>
                                </div>
                            </div>

                            <div class="flex space-x-4 pt-6">
                                <a
                                    href="admin-profile-travel.php"
                                    class="inline-flex items-center space-x-2 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    <span>Batal</span>
                                </a>
                                <button
                                    type="submit"
                                    class="inline-flex items-center space-x-2 bg-samira-teal text-white px-6 py-3 rounded-lg hover:bg-samira-teal-dark transition-all duration-300 font-bold shadow-lg shadow-samira-teal/30">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Simpan Perubahan</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="card-panel space-y-4">
                        <h3 class="text-xl font-bold text-deep-navy mb-4 border-b pb-3">Informasi Tersimpan Saat Ini</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-semibold text-gray-600 block">Alamat:</label>
                                <p class="text-gray-800 mt-1 p-2 bg-gray-50 rounded-lg border"><?php echo $profil ? htmlspecialchars($profil['alamat']) : 'Belum ada data'; ?></p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600 block">Email:</label>
                                <p class="text-gray-800 mt-1 p-2 bg-gray-50 rounded-lg border"><?php echo $profil ? htmlspecialchars($profil['email']) : 'Belum ada data'; ?></p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600 block">Banner Tersimpan:</label>
                                <?php if (!empty($existing_image_src)): ?>
                                    <div class="mt-2 inline-flex p-2 bg-gray-50 border rounded-lg shadow-sm">
                                        <img src="<?php echo htmlspecialchars($existing_image_src); ?>" alt="Banner tersimpan" class="max-w-xs h-auto rounded-md object-cover">
                                    </div>
                                <?php else: ?>
                                    <p class="text-gray-500 mt-1">Belum ada banner tersimpan.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <script>
        // Perbarui preview banner ketika user memilih file baru
        function updateBannerPreview(file) {
            const preview = document.getElementById('banner-preview');
            const img = preview ? preview.querySelector('img') : null;

            if (!preview || !img) {
                return;
            }

            if (file) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    img.src = ev.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                const existingImage = <?php echo json_encode($existing_image_src ?? null); ?>;
                if (existingImage) {
                    img.src = existingImage;
                    preview.classList.remove('hidden');
                } else {
                    preview.classList.add('hidden');
                }
            }
        }

        // Ganti label file input agar menampilkan nama file yang dipilih
        function updateFileInputText(input) {
            const wrapper = input.closest('.file-input-wrapper');
            if (!wrapper) return;

            const textNode = wrapper.childNodes[0];
            if (input.files.length > 0) {
                if (textNode) textNode.nodeValue = input.files[0].name;
            } else if (textNode) {
                textNode.nodeValue = 'Pilih File Banner Baru';
            }
        }

        document.getElementById('banner')?.addEventListener('change', function() {
            updateBannerPreview(this.files[0]);
            updateFileInputText(this);
        });

        // Pastikan preview menampilkan banner tersimpan saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            updateBannerPreview(null);
        });
    </script>
</body>

</html>