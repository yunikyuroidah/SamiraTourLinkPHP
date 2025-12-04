<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Galeri - Admin Samira Travel</title>
<?php include 'admin-shared-head.php'; ?>
</head>

<body class="bg-gray-50 font-sans">
    <div class="min-h-screen flex">
        <?php $activePage = 'gallery'; include 'admin-sidebar.php'; ?>

        <div class="content-area">
            <div class="flex justify-between items-center mb-8 pb-4 border-b-2 border-deep-navy/10 shadow-sm">
                <div>
                    <h1 class="text-3xl font-bold text-deep-navy">Kelola Galeri</h1>
                    <p class="text-gray-500 mt-1">Tambah, edit, atau hapus foto galeri perjalanan.</p>
                </div>
                <a href="../../index.php" target="_blank" title="Lihat Halaman Depan"
                    class="btn-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
            </div>

            <main class="flex-1">
                <div class="card-panel mb-8">
                    <div class="flex items-center mb-6 border-b pb-4">
                        <div class="p-3 bg-samira-gold/30 rounded-full mr-4">
                            <svg class="w-6 h-6 text-deep-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-deep-navy">Upload Gambar Baru / Edit Gambar</h3>
                    </div>

                    <form id="gallery-upload-form" class="space-y-6" enctype="multipart/form-data">
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="g-name" class="block text-sm font-semibold text-gray-700 mb-2">Judul Gambar <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="g-name" required class="input-field" placeholder="Contoh: Jamaah di Masjidil Haram">
                                </div>
                                <div>
                                    <label for="g-deskripsi" class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi (Singkat)</label>
                                    <textarea name="deskripsi" id="g-deskripsi" rows="1" class="input-field resize-none" placeholder="Deskripsi singkat untuk gambar (opsional)"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih File Gambar <span class="text-red-500" id="file-required-star">*</span></label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-samira-teal/50 border-dashed rounded-xl hover:border-samira-teal transition-colors cursor-pointer">
                                <div class="space-y-1 text-center w-full">
                                    <svg class="mx-auto h-12 w-12 text-samira-teal/70" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <div class="flex items-center justify-center text-sm text-gray-600">
                                        <label for="g-file" class="relative cursor-pointer bg-white rounded-md font-medium text-deep-navy hover:text-samira-teal focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-samira-teal px-3 py-2">
                                            <span>Pilih file</span>
                                            <input id="g-file" name="file" type="file" class="sr-only" accept="image/*" required>
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF, WEBP (Max. 1MB)</p>
                                </div>
                            </div>
                            <div id="image-preview" class="mt-4 hidden p-4 border border-gray-200 rounded-lg bg-gray-50">
                                <img src="#" alt="Preview" class="max-w-full h-48 object-contain mx-auto rounded-lg shadow-md">
                            </div>
                        </div>

                        <div class="flex items-center gap-4 pt-4">
                            <button type="button" id="g-upload"
                                class="btn-primary flex-1 justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                <span>Upload Gambar</span>
                            </button>
                            <button type="button" id="g-reset"
                                class="btn-secondary flex-1 justify-center">
                                Reset Form
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card-panel">
                    <div class="flex flex-col items-start mb-8 border-b pb-4">
                        <h3 class="text-2xl font-bold text-deep-navy">Daftar Gambar Galeri</h3>
                        <p class="text-sm text-gray-500 mt-1">Total: <span id="gallery-count" class="font-bold text-samira-teal">0</span> gambar</p>
                    </div>

                    <div class="pt-2">
                        <div id="gallery-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            <div class="col-span-full text-center py-12 text-gray-500">Memuat data...</div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <div id="gallery-feedback" class="fixed bottom-4 right-4 z-50"></div>
    </div>

    <script>
        const apiUrl = 'admin-gallery-api.php';
        const FALLBACK_IMAGE = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjcwIiB2aWV3Qm94PSIwIDAgMTAwIDcwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgogIDxyZWN0IHdpZHRoPSIxMDAiIGhlaWdodD0iNzAiIHJ4PSIxNCIgZmlsbD0iI2UwZjJmNSIvPgogIDxwYXRoIGQ9Ik00My45OTk4IDQ0LjQ5ODhMMzEuNDk5OSAzMS45OTg4QzI5Ljc3NjggMzAuMjc2NyAyOS43NzY4IDI3LjMyMyAzMS40OTk5IDI1LjYwMTJDMzMuMjIzIDIzLjg3OTIgMzYuMTc2MyAyMy44NzkyIDM3Ljg5ODQgMjUuNjAxMkw1MC4zOTg0IDM4LjEwMTJMNjIuODk4NCAyNS42MDEyQzY0LjYyMDUgMjMuODc5MiA2Ny41NzQ2IDIzLjg3OTIgNjkuMjk2NyAyNS42MDEyQzcxLjAxODcgMjcuMzIzIDcxLjAxODcgMzAuMjc2NyA2OS4yOTY3IDMxLjk5ODhMNDYuNDk4NCA1NC43OTY4QzQ0Ljc3NjMgNTYuNTE4OSA0MS44MjE5IDU2LjUxODkgNDAuMDk5OCA1NC43OTY4QzAwLjA5OTggNTQuNzk2OCA0MC4wOTk4IDU0Ljc5NjggNDMuOTk5OCA0NC40OTg4WiIgZmlsbD0iIzk4YTZkNSIgZmlsbC1vcGFjaXR5PSIwLjciLz4KICA8Y2lyY2xlIGN4PSI2My42IiBjeT0iMjYuNiIgcj0iNi42IiBmaWxsPSIjZDRlMmYyIi8+CiAgPGNpcmNsZSBjeD0iMzYuNCIgY3k9IjI2LjYiIHI9IjYuNiIgZmlsbD0iI2Q0ZTJmMiIvPgogIDxyZWN0IHg9IjIwIiB5PSI1Mi41IiB3aWR0aD0iNjAiIGhlaWdodD0iOC41IiByeD0iNC4yIiBmaWxsPSIjZGRlMmYzIi8+Cjwvc3ZnPg==';
        
        // Fungsi Feedback
        function showFeedback(msg, ok = true) {
            const el = document.getElementById('gallery-feedback');
            el.textContent = msg;
            const bgColor = ok ? 'bg-samira-teal text-white' : 'bg-red-500 text-white';
            el.className = `fixed bottom-4 right-4 z-50 px-5 py-3 rounded-xl shadow-xl font-medium ${bgColor} transition-all duration-300 transform translate-y-0 opacity-100`;
            setTimeout(() => el.className = 'fixed bottom-4 right-4 z-50 opacity-0 transition-all duration-500 transform translate-y-10', 4000);
        }

        function handleImageError(img) {
            if (img.dataset.fallbackApplied === 'true') {
                return;
            }
            img.dataset.fallbackApplied = 'true';
            img.onerror = null;
            img.src = FALLBACK_IMAGE;
        }

        // Image preview
        document.getElementById('g-file').addEventListener('change', function(e) {
            const preview = document.getElementById('image-preview');
            const img = preview.querySelector('img');
            const required = document.getElementById('g-file').required;

            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    preview.classList.remove('hidden');
                    preview.style.opacity = '0';
                    preview.style.transition = 'opacity 0.3s ease-in-out';
                    setTimeout(() => preview.style.opacity = '1', 50);
                }
                reader.readAsDataURL(this.files[0]);
            } else if (required) {
                preview.classList.add('hidden');
            } else {
                 // Sembunyikan preview jika tidak ada file dan mode edit
                 preview.classList.add('hidden');
            }
        });

        // Reset form and preview
        document.getElementById('g-reset').addEventListener('click', function() {
            document.getElementById('gallery-upload-form').reset();
            document.getElementById('image-preview').classList.add('hidden');
            
            const fileInput = document.getElementById('g-file');
            fileInput.required = true; // Kembali ke mode Upload (file harus diisi)
            document.getElementById('file-required-star').classList.remove('hidden'); // Tampilkan bintang required

            const up = document.getElementById('g-upload');
            delete up.dataset.mode;
            delete up.dataset.id;
            
            // Kembalikan gaya tombol default
            up.classList.remove('btn-warning');
            if (!up.classList.contains('btn-primary')) {
                up.classList.add('btn-primary');
            }
            
            // Icon Upload
            up.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <span>Upload Gambar</span>`;
        });

        async function fetchList() {
            const countEl = document.getElementById('gallery-count');
            countEl.textContent = 'Memuat...';
            try {
                const res = await fetch(apiUrl, { credentials: 'same-origin' });
                if (!res.ok) {
                    countEl.textContent = 'Gagal';
                    return showFeedback('Request gagal (' + res.status + ')', false);
                }
                const text = await res.text();
                if (!text) {
                    return showFeedback('Empty response from server', false);
                }
                let json;
                try {
                    json = JSON.parse(text);
                } catch (err) {
                    console.error('Invalid JSON from API:', text);
                    return showFeedback('Invalid JSON response from server', false);
                }
                if (!json.success) return showFeedback('Gagal memuat daftar', false);
                countEl.textContent = json.data.length;
                renderList(json.data);
            } catch (e) {
                countEl.textContent = 'Gagal';
                showFeedback('Error: ' + e.message, false);
            }
        }

        function renderList(items) {
            const container = document.getElementById('gallery-list');
            container.innerHTML = '';
            if (!items || items.length === 0) {
                container.innerHTML = '<div class="col-span-full text-center py-8 text-gray-500 p-6 bg-white rounded-xl shadow-inner border border-dashed border-gray-300"><svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><p class="mt-2 text-base font-medium">Belum ada gambar yang diunggah.</p><p class="text-sm">Gunakan form di atas untuk memulai.</p></div>';
                return;
            }
            items.forEach(it => {
                const div = document.createElement('div');
                div.className = 'group card-panel slim flex flex-col gap-4 transition duration-300 transform hover:shadow-xl hover:scale-[1.01]';
                div.dataset.deskripsi = it.deskripsi || '';

                const baseUrl = window.location.pathname.includes('/src/') ? '../' : 'src/';

                const imageUrl = (function(){
                    const source = it.url || it.filename || '';
                    if (typeof source === 'string' && source) {
                        if (source.startsWith('data:')) return source;
                        if (source.startsWith('http://') || source.startsWith('https://') || source.startsWith('/')) return source;
                        const cleaned = source.replace(/^src\//, '');
                        return baseUrl + cleaned;
                    }
                    if (it.base64) {
                        const mime = it.content_type || 'image/jpeg';
                        return `data:${mime};base64,${it.base64}`;
                    }
                    return '';
                })();

                div.innerHTML = `
                    <div class="overflow-hidden rounded-xl bg-gray-100 h-48">
                        <img src="${imageUrl}"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            onerror="handleImageError(this)"
                            alt="${escapeHtml(it.name || 'Gallery Image')}"
                        />
                    </div>
                    <div>
                        <h4 class="font-bold text-lg text-deep-navy group-hover:text-samira-teal transition-colors line-clamp-1 mb-1">
                            ${escapeHtml(it.name || '-')}
                        </h4>
                        <p class="text-xs text-gray-600 line-clamp-2 mb-3 min-h-[30px]">
                            ${escapeHtml(it.deskripsi || 'Tidak ada deskripsi.')}
                        </p>
                    </div>
                    <div class="mt-auto flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-3 border-t border-gray-100 pt-3">
                            <button class="btn-edit js-btn-edit w-full sm:w-1/2 justify-center text-sm" data-id="${it.id}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>Edit</span>
                            </button>
                            <button class="btn-danger js-btn-delete w-full sm:w-1/2 justify-center text-sm" data-id="${it.id}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span>Hapus</span>
                            </button>
                    </div>
                `;
                container.appendChild(div);
            });

            document.querySelectorAll('.js-btn-delete').forEach(b => b.onclick = async (e) => {
                const button = e.currentTarget;
                const id = button.getAttribute('data-id');
                const itemName = (button.closest('.group').querySelector('h4')?.textContent || '').trim();
                const confirmMessage = `Hapus gambar ini?\n\nJudul  : ${itemName || '-'}\n\nTindakan ini tidak dapat dibatalkan.`;
                if (!confirm(confirmMessage)) return;
                
                const fd = new FormData();
                fd.append('action', 'delete');
                fd.append('id', id);

                button.disabled = true;

                try {
                    const res = await fetch(apiUrl, {
                        method: 'POST',
                        body: fd,
                        credentials: 'same-origin'
                    });
                    if (!res.ok) {
                        showFeedback('Request gagal (' + res.status + ')', false);
                        return;
                    }
                    const text = await res.text();
                    
                    if (!text) return showFeedback('Empty response', false);
                    let j;
                    try {
                        j = JSON.parse(text);
                    } catch (err) {
                        console.error('Invalid JSON:', text);
                        return showFeedback('Invalid response', false);
                    }

                    if (j.success) {
                        showFeedback('Gambar berhasil dihapus!');
                        fetchList();
                    } else showFeedback(j.message || 'Gagal menghapus gambar', false);
                } catch (err) {
                    showFeedback('Error: ' + err.message, false);
                } finally {
                    button.disabled = false;
                }
            });

            document.querySelectorAll('.js-btn-edit').forEach(b => b.onclick = async (e) => {
                const button = e.currentTarget;
                const id = button.getAttribute('data-id');
                // Menggunakan ID untuk mendapatkan data gambar
                try {
                    const resp = await fetch(apiUrl + '?id=' + encodeURIComponent(id), {
                        credentials: 'same-origin'
                    });
                    if (!resp.ok) {
                        return showFeedback('Request gagal (' + resp.status + ')', false);
                    }
                    const text = await resp.text();

                    if (!text) return showFeedback('Empty response', false);
                    let j;
                    try {
                        j = JSON.parse(text);
                    } catch (err) {
                        console.error('Invalid JSON:', text);
                        return showFeedback('Invalid response', false);
                    }

                    if (!j.success) return showFeedback('Gagal memuat item', false);
                    const it = j.data;
                    
                    // Isi Form
                    document.getElementById('g-name').value = it.name || '';
                    document.getElementById('g-deskripsi').value = it.deskripsi || '';
                    
                    // Reset File Input dan atur agar tidak wajib
                    document.getElementById('g-file').value = '';
                    document.getElementById('g-file').required = false; 
                    document.getElementById('file-required-star').classList.add('hidden'); // Sembunyikan bintang required
                    const preview = document.getElementById('image-preview');
                    const previewImg = preview.querySelector('img');
                    const baseUrl = window.location.pathname.includes('/src/') ? '../' : 'src/';
                    const source = (function(){
                        if (it.url) return it.url;
                        if (it.base64) {
                            const mime = it.content_type || 'image/jpeg';
                            return `data:${mime};base64,${it.base64}`;
                        }
                        return it.filename || '';
                    })();

                    if (source) {
                        if (source.startsWith('data:') || source.startsWith('http://') || source.startsWith('https://') || source.startsWith('/')) {
                            previewImg.src = source;
                        } else {
                            previewImg.src = baseUrl + source.replace(/^src\//, '');
                        }
                        preview.classList.remove('hidden');
                    } else {
                        preview.classList.add('hidden');
                    }
                    
                    const up = document.getElementById('g-upload');
                    up.dataset.mode = 'update';
                    up.dataset.id = it.id;
                    
                    // Ganti warna tombol untuk mode update menggunakan gaya konsisten
                    up.classList.remove('btn-primary');
                    up.classList.add('btn-warning');

                    // Ganti Icon
                    up.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.356 2H15" />
                            </svg>
                            <span>Update Data</span>`;
                            
                    showFeedback('Mode edit aktif. Unggah file baru untuk ganti gambar (opsional).');
                    
                    // scroll to form
                    document.getElementById('gallery-upload-form').scrollIntoView({
                        behavior: 'smooth'
                    });
                } catch (err) {
                    showFeedback('Error memuat item', false);
                }
            });
        }

        function escapeHtml(s) {
            return (s + '').replace(/[&<>"']/g, function(c) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": "&#39;"
                } [c];
            });
        }

        document.getElementById('g-upload').addEventListener('click', async function() {
            const btn = this;
            const form = document.getElementById('gallery-upload-form');
            const formData = new FormData(form);

            // Periksa validitas form manual karena required bisa berubah di mode edit
            let isValid = true;
            if (!document.getElementById('g-name').value.trim()) isValid = false;
            
            const mode = btn.dataset.mode || 'upload';
            
            // Logika untuk memastikan file diupload jika mode adalah 'upload'
            if (mode === 'upload' && !document.getElementById('g-file').files.length) {
                isValid = false;
                showFeedback('Mohon pilih file gambar yang akan diunggah.', false);
            }

            if (!isValid) {
                form.reportValidity(); // Tampilkan pesan validasi browser
                return;
            }
            
            if (mode === 'upload') {
                formData.append('action', 'upload');
            } else {
                formData.append('action', 'update');
                const editId = btn.dataset.id;
                if (!editId) {
                    showFeedback('ID item tidak ditemukan. Muat ulang halaman dan coba lagi.', false);
                    return;
                }
                formData.append('id', editId);
                if (!document.getElementById('g-file').files.length) {
                    formData.append('keep_image', '1');
                }
            }
            
            const originalText = btn.querySelector('span').textContent;
            btn.disabled = true;
            btn.querySelector('span').textContent = (mode === 'upload' ? 'Mengunggah...' : 'Meng-update...');
            
            try {
                const res = await fetch(apiUrl, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });
                if (!res.ok) {
                    showFeedback('Request gagal (' + res.status + ')', false);
                    return;
                }
                const text = await res.text();
                
                if (!text) return showFeedback('Empty response', false);
                let j;
                try {
                    j = JSON.parse(text);
                } catch (err) {
                    console.error('Invalid JSON:', text);
                    return showFeedback('Invalid response', false);
                }

                if (j.success) {
                    showFeedback(j.message || 'Sukses!');
                    document.getElementById('g-reset').click(); // Reset form
                    fetchList();
                } else {
                    showFeedback(j.message || 'Gagal', false);
                }
            } catch (err) {
                showFeedback('Error: ' + err.message, false);
            } finally {
                btn.disabled = false;
                btn.querySelector('span').textContent = originalText;
                
                // Pastikan tombol kembali ke mode upload jika selesai
                if(mode !== 'upload' && btn.dataset.mode !== 'update') {
                    document.getElementById('g-reset').click();
                }
            }
        });

        // initial load
        fetchList();
    </script>
</body>

</html>