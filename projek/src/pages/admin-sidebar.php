<?php
if (!isset($activePage)) {
    $activePage = '';
}
?>
<div class="sidebar shadow-xl">
    <div class="p-6 border-b border-gray-700 mb-6">
        <h2 class="text-3xl font-extrabold text-samira-gold">Samira Travel</h2>
        <p class="text-sm text-gray-400 mt-1">Admin Dashboard</p>
    </div>

    <nav class="space-y-1">
        <a href="admin-packages.php" class="sidebar-nav-item<?php echo $activePage === 'packages' ? ' active' : ''; ?>">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2l-8 4v12l8 4 8-4V6l-8-4zm0 14.5l-6-3V7.5l6 3 6-3v6l-6 3z" />
            </svg>
            <span>Kelola Paket</span>
        </a>
        <a href="admin-profile-travel.php" class="sidebar-nav-item<?php echo $activePage === 'profile' ? ' active' : ''; ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span>Profil Travel</span>
        </a>
        <a href="admin-tour-leader.php" class="sidebar-nav-item<?php echo $activePage === 'tour-leader' ? ' active' : ''; ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span>Tour Leader</span>
        </a>
        <a href="admin-gallery.php" class="sidebar-nav-item<?php echo $activePage === 'gallery' ? ' active' : ''; ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>Galeri</span>
        </a>
    </nav>

    <div class="absolute bottom-6 w-full px-5">
        <a href="admin-logout.php" class="flex items-center justify-center space-x-2 w-full bg-samira-gold text-deep-navy px-4 py-3 rounded-lg font-bold hover:bg-yellow-500 transition-colors shadow-md">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span>Logout</span>
        </a>
    </div>
</div>
