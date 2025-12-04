<?php
// Simple gallery API for admin (used by admin dashboard right sidebar)
// Usage (all requests are POST except list which can be GET):
// GET -> list all
// POST action=upload (multipart/form-data) -> file upload + name, category, uploaded_by
// POST action=update -> id, name, category, (optional file)
// POST action=delete -> id

session_start();
// Ensure PHP errors are logged (not output) to avoid breaking JSON responses
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../tmp/php_errors.log');
header('Content-Type: application/json');

require_once __DIR__ . '/../../models/GalleryAsset.php';

// Legacy: images used to be stored on disk under src/assets/gallery/
// Keep directory reference for backward compatibility when serving old files
$uploaddir = __DIR__ . '/../assets/gallery';

$gallery = new GalleryAsset();

// Helper to send response
function res($ok, $data = null, $msg = '')
{
    echo json_encode(['success' => $ok, 'message' => $msg, 'data' => $data]);
    exit;
}

function looks_like_base64($value)
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

function detect_mime_from_base64($base64)
{
    $mime = 'image/jpeg';
    $decoded = base64_decode($base64, true);
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
    return $mime;
}

function build_payload($item)
{
    global $uploaddir;

    $payload = $item;
    unset($payload['mime']);
    $base64 = $item['content'] ?? ($item['content_base64'] ?? null);
    $mime = null;

    if (!$base64 && !empty($item['filename']) && looks_like_base64($item['filename'])) {
        $base64 = $item['filename'];
    }

    if ($base64) {
        $mime = detect_mime_from_base64($base64);
        $payload['base64'] = $base64;
        $payload['url'] = 'data:' . $mime . ';base64,' . $base64;
        $payload['content_type'] = $mime;
    } elseif (!empty($item['filename'])) {
        $payload['base64'] = null;
        $payload['url'] = 'src/assets/gallery/' . $item['filename'];
        $payload['content_type'] = null;
    } else {
        $payload['base64'] = null;
        $payload['url'] = null;
        $payload['content_type'] = null;
    }

    return $payload;
}

$method = $_SERVER['REQUEST_METHOD'];

// enforce admin-only for any modifying requests (POST actions)
if ($method === 'GET') {
    // support GET?id= to fetch single item
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $it = $gallery->find($_GET['id']);
        if (!$it) res(false, null, 'Item tidak ditemukan');
        res(true, build_payload($it));
    }

    $items = $gallery->all();
    if (!$items) res(true, []);

    // Build response: prefer filename-based URLs (local files)
    $out = array_map('build_payload', $items);

    res(true, $out);
}

// POST actions
$action = $_POST['action'] ?? ($_GET['action'] ?? null);

if ($method === 'POST') {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        http_response_code(401);
        res(false, null, 'Unauthorized: admin only');
    }
}

if ($method === 'POST') {
    if ($action === 'upload') {
        if (!isset($_FILES['file'])) res(false, null, 'File gambar wajib diunggah');

        $file = $_FILES['file'];
        if ($file['error'] !== UPLOAD_ERR_OK) res(false, null, 'Upload error (kode: ' . $file['error'] . ')');

        $maxSize = 1024 * 1024; // 1 MB limit
        if ($file['size'] > $maxSize) res(false, null, 'Ukuran file melebihi 1MB');

        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $tmpPath = $file['tmp_name'];
        $detectedMime = null;
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $detectedMime = finfo_file($finfo, $tmpPath);
                finfo_close($finfo);
            }
        }
        $mime = $detectedMime ?: ($file['type'] ?? '');
        if (!in_array($mime, $allowed)) res(false, null, 'Tipe file tidak didukung');

        $name = $_POST['name'] ?? $file['name'];
        $deskripsi = $_POST['deskripsi'] ?? null;
        $uploaded_by = $_SESSION['admin_username'] ?? ($_POST['uploaded_by'] ?? null);

        $payload = [
            'name' => $name,
            'deskripsi' => $deskripsi,
            'uploaded_by' => $uploaded_by,
            'uploaded_at' => date('Y-m-d H:i:s')
        ];

        if ($gallery->canStoreInlineContent()) {
            $raw = file_get_contents($tmpPath);
            if ($raw === false) res(false, null, 'Gagal membaca file yang diunggah');
            $payload['content'] = base64_encode($raw);
            // pastikan kolom filename kosong jika tabel mewajibkan
            $payload['filename'] = '';
        } else {
            res(false, null, 'Database belum memiliki kolom penyimpanan inline (content). Mohon tambahkan kolom tersebut.');
        }

        $id = $gallery->create($payload);

        if ($id) {
            $item = $gallery->find($id);
            res(true, build_payload($item), 'Upload berhasil');
        }
        res(false, null, 'Gagal menyimpan metadata');
    } elseif ($action === 'update') {
        $id = $_POST['id'] ?? null;
        if (!$id) res(false, null, 'ID diperlukan');
        $existing = $gallery->find($id);
        if (!$existing) res(false, null, 'Item tidak ditemukan');

        $updateData = [];
        if (isset($_POST['name'])) $updateData['name'] = $_POST['name'];
        if (isset($_POST['deskripsi'])) $updateData['deskripsi'] = $_POST['deskripsi'];

        if (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['file'];
            if ($file['error'] !== UPLOAD_ERR_OK) res(false, null, 'Upload error (kode: ' . $file['error'] . ')');

            $maxSize = 1024 * 1024; // 1 MB
            if ($file['size'] > $maxSize) res(false, null, 'Ukuran file melebihi 1MB');

            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $tmpPath = $file['tmp_name'];
            $detectedMime = null;
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                if ($finfo) {
                    $detectedMime = finfo_file($finfo, $tmpPath);
                    finfo_close($finfo);
                }
            }
            $mime = $detectedMime ?: ($file['type'] ?? '');
            if (!in_array($mime, $allowed)) res(false, null, 'Tipe file tidak didukung');

            if (!$gallery->canStoreInlineContent()) {
                res(false, null, 'Database belum memiliki kolom penyimpanan inline (content). Mohon tambahkan kolom tersebut.');
            }

            $raw = file_get_contents($tmpPath);
            if ($raw === false) res(false, null, 'Gagal membaca file yang diunggah');

            $updateData['content'] = base64_encode($raw);
            $updateData['filename'] = '';

            if (!empty($existing['filename']) && !looks_like_base64($existing['filename'])) {
                $oldPath = $uploaddir . '/' . basename($existing['filename']);
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }
        } elseif (isset($_POST['keep_image']) && $_POST['keep_image'] === '1') {
            if ($gallery->canStoreInlineContent()) {
                if (!isset($existing['content']) && !isset($existing['content_base64'])) {
                    res(false, null, 'Item ini belum memiliki konten gambar tersimpan. Mohon unggah gambar baru.');
                }
            } else {
                if (empty($existing['filename']) || looks_like_base64($existing['filename'])) {
                    res(false, null, 'Item ini belum memiliki file gambar. Mohon unggah gambar baru.');
                }
            }
        }

        $ok = $gallery->update($id, $updateData);
        if ($ok) {
            $item = $gallery->find($id);
            res(true, build_payload($item), 'Diperbarui');
        }
        res(false, null, 'Gagal memperbarui');
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? null;
        if (!$id) res(false, null, 'ID diperlukan');
        $existing = $gallery->find($id);
        if (!$existing) res(false, null, 'Item tidak ditemukan');

        if (!empty($existing['filename']) && !looks_like_base64($existing['filename'])) {
            $filePath = $uploaddir . '/' . basename($existing['filename']);
            if (is_file($filePath)) {
                @unlink($filePath);
            }
        }

        $ok = $gallery->delete($id);
        if ($ok) res(true, null, 'Dihapus');
        res(false, null, 'Gagal menghapus');
    } else {
        res(false, null, 'Action tidak dikenali');
    }
}

res(false, null, 'Method tidak didukung');
