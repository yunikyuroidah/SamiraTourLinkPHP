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

$message = '';
$error = '';

$idParam = $_GET['id'] ?? '';
if (!is_numeric($idParam)) {
    header('Location: admin-packages.php');
    exit();
}
$id = (int)$idParam;

// Ambil data paket
$row = $paket->getById($id);
if (!$row) {
    $error = 'Paket tidak ditemukan.';
}

if ($_POST) {
    // Proses update
    $idInput = $_POST['id'] ?? $id;
    if (!is_numeric($idInput)) {
        $error = 'ID paket tidak valid.';
    } else {
        $paket->id = (int)$idInput;
        $paket->nama_paket = trim($_POST['nama_paket'] ?? '');
        $paket->deskripsi = trim($_POST['deskripsi'] ?? '');
        $fasilitas_lines = array_filter(array_map('trim', explode("\n", $_POST['fasilitas'] ?? '')));
        $features_lines = array_filter(array_map('trim', explode("\n", $_POST['features'] ?? '')));
        $paket->fasilitas = json_encode(array_values($fasilitas_lines));
        $paket->features = json_encode(array_values($features_lines));

        if ($paket->update()) {
            // Redirect kembali dengan pesan
            header('Location: admin-packages.php?msg=updated');
            exit();
        } else {
            $error = 'Gagal mengupdate paket.';
        }
    }
}

// Prepare form values
$form_nama = $row['nama_paket'] ?? '';
$form_deskripsi = $row['deskripsi'] ?? '';
$form_fasilitas = '';
$form_features = '';
if (!empty($row['fasilitas'])) {
    $decoded = json_decode($row['fasilitas'], true);
    if (is_array($decoded)) $form_fasilitas = implode("\n", $decoded);
}
if (!empty($row['features'])) {
    $decoded = json_decode($row['features'], true);
    if (is_array($decoded)) $form_features = implode("\n", $decoded);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Paket - Admin Samira Travel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'deep-navy': '#0A3355',
                        'samira-teal': '#14B8A6',
                        'samira-teal-dark': '#0D9488',
                        'samira-gold': '#FACC15',
                        'active-sidebar': '#134D6C'
                    },
                    boxShadow: {
                        'custom-lg': '0 10px 25px -3px rgba(10, 51, 85, 0.2)'
                    }
                }
            }
        };
    </script>
    <style>
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background-color: #0A3355;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 10;
        }

        .content-area {
            margin-left: 250px;
            padding: 32px;
            width: calc(100% - 250px);
            min-height: 100vh;
        }

        .sidebar-nav-item {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            font-size: 16px;
            color: #E5E7EB;
            transition: background-color 0.2s, color 0.2s;
        }

        .sidebar-nav-item:hover {
            background-color: #134D6C;
            color: white;
        }

        .sidebar-nav-item.active {
            background-color: #134D6C;
            color: #FACC15;
            font-weight: 600;
            border-left: 5px solid #FACC15;
            padding-left: 15px;
        }

        .input-field {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #D1D5DB;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .input-field:focus {
            border-color: #14B8A6;
            outline: none;
            box-shadow: 0 0 0 1px #14B8A6;
        }

        .badge-id {
            display: inline-block;
            font-size: 12px;
            font-weight: 600;
            color: #0A3355;
            background-color: rgba(250, 204, 21, 0.2);
            padding: 6px 12px;
            border-radius: 9999px;
        }
    </style>
</head>

<body class="bg-gray-50 font-sans">
    <div class="min-h-screen flex">
        <?php $activePage = 'packages'; include 'admin-sidebar.php'; ?>

        <div class="content-area">
            <div class="flex justify-between items-center mb-8 pb-4 border-b-2 border-deep-navy/10 shadow-sm">
                <div>
                    <h1 class="text-3xl font-bold text-deep-navy">Edit Paket</h1>
                    <p class="text-gray-500 mt-1">Perbarui detail paket perjalanan yang sudah terdaftar.</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="../../index.php" target="_blank" title="Lihat Halaman Depan"
                        class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-samira-teal text-white shadow-lg hover:bg-samira-teal-dark transition-transform transform hover:-translate-y-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </a>
                    <a href="admin-packages.php"
                        class="inline-flex items-center space-x-2 bg-gray-200 text-gray-700 px-5 py-3 rounded-xl font-semibold hover:bg-gray-300 transition-colors shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span>Kembali ke daftar</span>
                    </a>
                </div>
            </div>

            <main>
                <?php if ($error): ?>
                    <div class="mb-6 p-4 text-sm bg-red-100 text-red-700 rounded-lg border border-red-300 font-medium flex items-center space-x-3">
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    </div>
                <?php endif; ?>

                <div class="max-w-3xl mx-auto">
                    <div class="bg-white rounded-xl shadow-lg p-10 border border-gray-200 border-t-4 border-samira-teal">
                        <div class="flex items-center justify-between mb-6 border-b pb-4">
                            <div class="flex items-center space-x-4">
                                <div class="p-3 bg-samira-gold/30 rounded-full">
                                    <svg class="w-6 h-6 text-deep-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">Formulir Edit Paket</h2>
                                    <p class="text-gray-600">Sesuaikan nama, deskripsi, serta daftar fasilitas dan keunggulan paket.</p>
                                </div>
                            </div>
                            <span class="badge-id">ID Paket: <?php echo htmlspecialchars($id); ?></span>
                        </div>

                        <form method="POST" class="space-y-6">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Paket</label>
                                <input type="text" name="nama_paket" required value="<?php echo htmlspecialchars($form_nama); ?>" class="input-field" placeholder="Masukkan nama paket">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                                <textarea name="deskripsi" rows="4" class="input-field" placeholder="Rangkuman singkat paket"><?php echo htmlspecialchars($form_deskripsi); ?></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Fasilitas (satu per baris)</label>
                                    <textarea name="fasilitas" rows="8" class="input-field" placeholder="Hotel Bintang 5\nMaskapai Full Service\nManasik eksklusif"><?php echo htmlspecialchars($form_fasilitas); ?></textarea>
                                    <p class="text-xs text-gray-500 mt-1">Pisahkan setiap fasilitas dengan menekan Enter.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Keunggulan / Features</label>
                                    <textarea name="features" rows="8" class="input-field" placeholder="Pembimbing Ustaz Berpengalaman\nCity Tour Premium"><?php echo htmlspecialchars($form_features); ?></textarea>
                                    <p class="text-xs text-gray-500 mt-1">Pisahkan setiap keunggulan dengan menekan Enter.</p>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-100">
                                <a href="admin-packages.php" class="inline-flex items-center space-x-2 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    <span>Batal</span>
                                </a>
                                <button type="submit" class="inline-flex items-center space-x-2 bg-samira-teal text-white px-6 py-3 rounded-lg font-bold hover:bg-samira-teal-dark transition-colors shadow-lg shadow-samira-teal/30">
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