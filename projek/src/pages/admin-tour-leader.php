<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit();
}

require_once '../../config/database.php';
require_once '../../models/TourLeader.php';

$database = new Database();
$db = $database->getConnection();
$tourLeader = new TourLeader($db);

$message = '';
$error = '';

$leader = null;
$stmt = $tourLeader->read();
if ($stmt) {
    $leader = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

if (!$leader) {
    $leader = [
        'id' => 'tour_leader_id',
        'nama' => '',
        'telepon' => '',
        'gambar_base64' => null,
    ];
}

$currentImageBase64 = $leader['gambar_base64'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? $leader['id'];
    $nama = trim($_POST['nama'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $newImageBase64 = null;

    if ($id === '') {
        $error = 'Data tour leader tidak ditemukan. Silakan muat ulang halaman.';
    } elseif ($nama === '') {
        $error = 'Nama wajib diisi.';
    } elseif ($telepon === '') {
        $error = 'Nomor telepon wajib diisi.';
    }

    if (empty($error) && isset($_FILES['gambar']) && $_FILES['gambar']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['gambar'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error = 'Upload gambar gagal (kode ' . $file['error'] . ').';
        } elseif ($file['size'] > 1024 * 1024) {
            $error = 'Ukuran gambar maksimal 1MB.';
        } else {
            $tmpPath = $file['tmp_name'];
            $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];

            $detectedMime = null;
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                if ($finfo) {
                    $detectedMime = finfo_file($finfo, $tmpPath);
                    finfo_close($finfo);
                }
            }

            if (!$detectedMime && function_exists('getimagesize')) {
                $imageInfo = @getimagesize($tmpPath);
                if (is_array($imageInfo) && isset($imageInfo['mime'])) {
                    $detectedMime = $imageInfo['mime'];
                }
            }

            $mime = $detectedMime ?: ($file['type'] ?? '');

            if (!in_array($mime, $allowedMime, true)) {
                $error = 'Format gambar harus JPG, PNG, atau WEBP.';
            } else {
                $binary = file_get_contents($tmpPath);
                if ($binary === false) {
                    $error = 'Gagal membaca file gambar.';
                } else {
                    $newImageBase64 = base64_encode($binary);
                }
            }
        }
    }

    if (empty($error)) {
        $tourLeader->id = $id;
        $tourLeader->nama = $nama;
        $tourLeader->telepon = $telepon;
        $tourLeader->gambar_base64 = $newImageBase64;

        if ($tourLeader->update()) {
            $message = 'Data tour leader berhasil diperbarui.';
            $stmt = $tourLeader->read();
            $leader = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
            if (!$leader) {
                $leader = [
                    'id' => $id,
                    'nama' => $nama,
                    'telepon' => $telepon,
                    'gambar_base64' => $newImageBase64 ?? $currentImageBase64,
                ];
            }
        } else {
            $error = 'Gagal menyimpan data tour leader.';
        }
    } else {
        if (!is_array($leader)) {
            $leader = [];
        }
        $leader['id'] = $id;
        $leader['nama'] = $nama;
        $leader['telepon'] = $telepon;
    }
}

$leaderImageSrc = null;
if (!empty($leader['gambar_base64'])) {
    $decoded = base64_decode($leader['gambar_base64'], true);
    if ($decoded !== false) {
        $mime = 'image/jpeg';
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $detected = finfo_buffer($finfo, $decoded);
                if ($detected) {
                    $mime = $detected;
                }
                finfo_close($finfo);
            }
        }
        $leaderImageSrc = 'data:' . $mime . ';base64,' . $leader['gambar_base64'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Leader - Admin Samira Travel</title>
    <?php include 'admin-shared-head.php'; ?>
</head>
<body class="bg-gray-50 font-sans">
    <div class="min-h-screen flex">
        <?php $activePage = 'tour-leader'; include 'admin-sidebar.php'; ?>

        <div class="content-area">
            <div class="flex justify-between items-center mb-8 pb-4 border-b-2 border-deep-navy/10 shadow-sm">
                <div>
                    <h1 class="text-3xl font-bold text-deep-navy">Tour Leader</h1>
                    <p class="text-gray-500 mt-1">Kelola informasi pemandu wisata utama</p>
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
                        <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="mb-6 p-4 text-sm bg-red-100 text-red-700 rounded-lg border border-red-300 font-medium flex items-center space-x-3">
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                <?php endif; ?>

                <div class="max-w-2xl mx-auto space-y-8">
                    <div class="card-panel space-y-8">
                        <div class="flex items-center space-x-4 mb-8 border-b pb-4">
                            <div class="p-3 bg-deep-navy rounded-xl">
                                <svg class="w-8 h-8 text-samira-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">Form Update Tour Leader</h2>
                                <p class="text-gray-600">Perbarui data nama, telepon, dan foto profil tour leader.</p>
                            </div>
                        </div>

                        <form method="POST" enctype="multipart/form-data" class="space-y-6">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($leader['id'] ?? 'tour_leader_id', ENT_QUOTES, 'UTF-8'); ?>">

                            <div>
                                <label for="nama" class="block text-sm font-semibold text-gray-700 mb-2">Nama</label>
                                <input
                                    type="text"
                                    id="nama"
                                    name="nama"
                                    required
                                    value="<?php echo htmlspecialchars($leader['nama'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    class="input-field"
                                    placeholder="Masukkan Nama Tour Leader">
                            </div>

                            <div>
                                <label for="telepon" class="block text-sm font-semibold text-gray-700 mb-2">Telepon</label>
                                <input
                                    type="text"
                                    id="telepon"
                                    name="telepon"
                                    required
                                    value="<?php echo htmlspecialchars($leader['telepon'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    class="input-field"
                                    placeholder="Masukkan Nomor Telepon">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Gambar Profil</label>

                                <?php if ($leaderImageSrc): ?>
                                    <div class="mb-4 flex items-center space-x-4">
                                        <img src="<?php echo htmlspecialchars($leaderImageSrc, ENT_QUOTES, 'UTF-8'); ?>" alt="Preview tour leader" class="w-24 h-24 object-cover rounded-full border-2 border-samira-teal shadow-md">
                                        <p class="text-sm text-gray-600">Gambar saat ini.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="mb-4 flex items-center space-x-4">
                                        <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center text-gray-500">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <p class="text-sm text-gray-500">Belum ada gambar terpasang.</p>
                                    </div>
                                <?php endif; ?>

                                <input type="file" name="gambar" accept="image/png,image/jpeg,image/webp" class="file-input border-gray-300 border rounded-lg p-2 text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <p class="text-xs text-gray-500 mt-1">Maks. 1MB. Format: JPG, PNG, WEBP.</p>
                            </div>

                            <div class="flex space-x-4 pt-6">
                                <a
                                    href="admin-tour-leader.php"
                                    class="inline-flex items-center space-x-2 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition-colors font-semibold shadow-md">
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

                </div>
            </main>
        </div>
    </div>
</body>
</html>
