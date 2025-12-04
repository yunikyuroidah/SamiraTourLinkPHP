<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit();
}

require_once '../../models/Paket.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();
$paket = new Paket($db);

// Handle form submissions - LOGIKA PHP
$message = '';
$error = '';

if ($_POST) {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'create':
            $paket->nama_paket = trim($_POST['nama_paket'] ?? '');
            $paket->deskripsi = trim($_POST['deskripsi'] ?? '');
            // Mengubah input textarea menjadi array list (dipisahkan oleh baris baru)
            $fasilitas_array = array_values(array_filter(array_map('trim', explode("\n", $_POST['fasilitas'] ?? ''))));
            $features_array = array_values(array_filter(array_map('trim', explode("\n", $_POST['features'] ?? ''))));

            $paket->fasilitas = json_encode($fasilitas_array);
            $paket->features = json_encode($features_array);

            if ($paket->create()) {
                $message = 'Paket baru berhasil ditambahkan!';
                if (!empty($paket->id)) {
                    $message .= ' (ID #' . $paket->id . ')';
                }
            } else {
                $error = 'Gagal menambahkan paket.';
            }
            break;

        case 'update':
            $idInput = $_POST['id'] ?? null;
            if (!is_numeric($idInput)) {
                $error = 'ID paket tidak valid.';
                break;
            }
            $paket->id = (int)$idInput;
            $paket->nama_paket = trim($_POST['nama_paket'] ?? '');
            $paket->deskripsi = trim($_POST['deskripsi'] ?? '');
            $fasilitas_array = array_values(array_filter(array_map('trim', explode("\n", $_POST['fasilitas'] ?? ''))));
            $features_array = array_values(array_filter(array_map('trim', explode("\n", $_POST['features'] ?? ''))));
            
            $paket->fasilitas = json_encode($fasilitas_array);
            $paket->features = json_encode($features_array);

            if ($paket->update()) {
                $message = "Paket berhasil diupdate!";
            } else {
                $error = "Gagal mengupdate paket.";
            }
            break;

        case 'delete':
            $idInput = $_POST['id'] ?? null;
            if (!is_numeric($idInput)) {
                $error = 'ID paket tidak valid.';
                break;
            }
            $paket->id = (int)$idInput;
            if ($paket->delete()) {
                $message = "Paket berhasil dihapus!";
            } else {
                $error = "Gagal menghapus paket.";
            }
            break;
    }
}

// Ambil semua paket
$stmt = $paket->read();
$packages = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Decode JSON strings menjadi array untuk ditampilkan
    $row['fasilitas'] = json_decode($row['fasilitas'], true);
    $row['features'] = json_decode($row['features'], true);
    $packages[] = $row;
}

// Pastikan paket tersortir berdasarkan ID numerik (ascending)
if (!empty($packages)) {
    usort($packages, function ($a, $b) {
        return ((int)($a['id'] ?? 0)) <=> ((int)($b['id'] ?? 0));
    });
}

if (!$message && isset($_GET['msg'])) {
    if ($_GET['msg'] === 'updated') {
        $message = 'Paket berhasil diupdate!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Paket - Admin Samira Travel</title>
<?php include 'admin-shared-head.php'; ?>
</head>

<body class="bg-gray-50 font-sans">
    <div class="min-h-screen flex">
        <?php $activePage = 'packages'; include 'admin-sidebar.php'; ?>

        <div class="content-area">
            <div class="flex justify-between items-center mb-8 pb-4 border-b-2 border-deep-navy/10 shadow-sm">
                <div>
                    <h1 class="text-3xl font-bold text-deep-navy">Kelola Paket Travel</h1>
                    <p class="text-gray-500 mt-1">Daftar dan konfigurasi semua paket perjalanan premium.</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="../../index.php" target="_blank" title="Lihat Halaman Depan" class="btn-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </a>
                    <button type="button" onclick="toggleModal('addModal')" class="btn-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <span>Tambah Paket Baru</span>
                    </button>
                </div>
            </div>

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

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php if (empty($packages)): ?>
                    <div class="card-panel lg:col-span-4 text-center text-gray-500">
                        <h3 class="text-xl font-medium">Belum ada paket perjalanan yang terdaftar.</h3>
                        <p class="mt-2">Silakan klik "Tambah Paket Baru" untuk membuat daftar.</p>
                    </div>
                <?php endif; ?>

                <?php foreach ($packages as $package): ?>
                    <div class="card-panel slim flex flex-col justify-between transform hover:shadow-xl hover:scale-[1.01] transition duration-300">
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-xs font-semibold text-deep-navy bg-samira-gold/30 px-3 py-1 rounded-full inline-block">ID: <?php echo htmlspecialchars($package['id']); ?></span>
                            </div>
                            
                            <h3 class="font-bold text-xl text-deep-navy mb-2 leading-snug"><?php echo htmlspecialchars($package['nama_paket']); ?></h3>
                            <p class="text-gray-600 mb-4 text-sm italic min-h-[40px] border-b pb-3 border-gray-100"><?php echo htmlspecialchars(substr($package['deskripsi'], 0, 80)) . (strlen($package['deskripsi']) > 80 ? '...' : ''); ?></p>

                            <div class="mb-4">
                                <h4 class="font-bold text-sm text-deep-navy mb-3 flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-samira-teal" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zm12 0a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2h-2zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zm12 0a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2h-2z" />
                                    </svg>
                                    <span>Fasilitas Utama:</span>
                                </h4>
                                <ul class="text-xs text-gray-700 space-y-1">
                                    <?php 
                                        $fasilitas_list = is_array($package['fasilitas']) ? $package['fasilitas'] : [];
                                        $display_count = 3;
                                    ?>
                                    <?php if (!empty($fasilitas_list)): ?>
                                        <?php foreach (array_slice($fasilitas_list, 0, $display_count) as $fasilitas): ?>
                                            <li class="flex items-start text-gray-800">
                                                <svg class="w-4 h-4 text-samira-teal mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                                <span><?php echo htmlspecialchars($fasilitas); ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                        <?php if (count($fasilitas_list) > $display_count): ?>
                                            <li class="text-gray-500 italic mt-1">+ <?php echo count($fasilitas_list) - $display_count; ?> lainnya</li>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <li class="text-gray-500 italic">Tidak ada detail fasilitas.</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>

                        <div class="pt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-3 border-t border-gray-100 mt-auto">
                            <button type="button" onclick="editPackage('<?php echo $package['id']; ?>')"
                                class="btn-edit w-full sm:w-1/2 justify-center text-sm">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                <span>Edit</span>
                            </button>
                            <button type="button" onclick="deletePackage('<?php echo $package['id']; ?>', '<?php echo htmlspecialchars($package['nama_paket']); ?>')"
                                class="btn-danger w-full sm:w-1/2 justify-center text-sm">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span>Hapus</span>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div id="addModal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl p-8 max-w-3xl w-full max-h-[95vh] overflow-y-auto transform transition-all duration-300">
            <h2 class="text-2xl font-bold text-deep-navy border-b pb-3 mb-6">📝 Formulir Paket Perjalanan Baru</h2>
            <form method="POST" class="space-y-6">
                <input type="hidden" name="action" value="create">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Paket <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_paket" required class="input-field" placeholder="Contoh: Umroh Premium 12 Hari">
                    <p class="text-xs text-gray-500 mt-2">ID paket akan dibuat otomatis oleh sistem.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Singkat <span class="text-red-500">*</span></label>
                    <textarea name="deskripsi" required class="input-field h-24" placeholder="Jelaskan detail singkat paket, misal: 'Perjalanan umroh eksklusif dengan fasilitas bintang 5 dan bimbingan ustaz berpengalaman.'"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Fasilitas (Satu per baris) <span class="text-red-500">*</span></label>
                        <textarea name="fasilitas" required class="input-field h-40" placeholder="Hotel Bintang 5 Dekat Masjid Nabawi&#10;Tiket Pesawat Saudi Airlines&#10;Transportasi Bus Eksekutif"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Pisahkan setiap item fasilitas dengan baris baru (Enter).</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Fitur / Keunggulan (Satu per baris) <span class="text-red-500">*</span></label>
                        <textarea name="features" required class="input-field h-40" placeholder="Gratis City Tour Dubai/Istanbul&#10;Asuransi Perjalanan Komprehensif&#10;Perlengkapan Premium & Manasik"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Pisahkan setiap item fitur/keunggulan dengan baris baru (Enter).</p>
                    </div>
                </div>

                <div class="flex justify-end gap-4 pt-4 border-t border-gray-100">
                    <button type="button" onclick="toggleModal('addModal')" class="btn-secondary">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Simpan Paket</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl p-8 max-w-sm w-full transform transition-all duration-300">
            <h2 class="text-xl font-bold text-red-600 mb-4 flex items-center space-x-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Konfirmasi Penghapusan</span>
            </h2>
            <p class="text-gray-700 mb-6">Apakah Anda yakin ingin menghapus paket **<span id="delete-package-name" class="font-bold text-deep-navy"></span>** (ID: <span id="delete-package-id-display" class="font-mono text-xs text-gray-500"></span>)? Tindakan ini **tidak dapat dibatalkan**.</p>
            <form method="POST" id="deleteForm" class="flex justify-end gap-3">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="delete-package-id">
                <button type="button" onclick="toggleModal('deleteModal')" class="btn-secondary">
                    Batal
                </button>
                <button type="submit" class="btn-danger">
                    <span>Ya, Hapus Permanen</span>
                </button>
            </form>
        </div>
    </div>


    <script>
        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.toggle('hidden');
            modal.classList.toggle('flex');
        }

        function editPackage(id) {
            // Redirect ke halaman edit terpisah (admin-edit-package.php)
            window.location.href = `admin-edit-package.php?id=${id}`;
        }

        function deletePackage(id, nama) {
            document.getElementById('delete-package-id').value = id;
            document.getElementById('delete-package-id-display').textContent = id;
            document.getElementById('delete-package-name').textContent = nama;
            toggleModal('deleteModal');
        }
    </script>
</body>

</html>