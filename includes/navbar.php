<?php
/**
 * Freshly - Reusable Navbar Component
 * Include this in pages: <?php include __DIR__ . '/includes/navbar.php'; ? >
 * 
 * Variables available before include:
 * - $navbarType: 'full' (with search, default) or 'simple' (just brand)
 */

$navbarType = $navbarType ?? 'full';
$currentUser = getCurrentUser();
$cartCount = getCartCount();
$unreadCount = getUnreadMessageCount();
$isLogged = isLoggedIn();
$userRole = $_SESSION['role'] ?? '';
?>

<nav class="navbar">
    <a href="<?= BASE_URL ?>/index.php" class="nav-brand">
        <i class="fa-solid fa-leaf"></i> Freshly
    </a>
    
    <?php if ($navbarType === 'full'): ?>
    <div class="search-container">
        <i class="fa-solid fa-search search-icon"></i>
        <form action="<?= BASE_URL ?>/index.php" method="GET" style="display:contents;">
            <input type="text" name="search" class="search-input" placeholder="Cari sayur segar, buah, atau beras..." value="<?= sanitize($_GET['search'] ?? '') ?>">
        </form>
    </div>
    <?php endif; ?>

    <div class="nav-actions">
        <?php if ($isLogged): ?>
            <?php if ($userRole === 'buyer'): ?>
                <div class="icon-action" onclick="window.location.href='<?= BASE_URL ?>/chat.php'" title="Pesan">
                    <i class="fa-solid fa-envelope fa-lg"></i>
                    <?php if ($unreadCount > 0): ?>
                        <span class="icon-badge"><?= $unreadCount ?></span>
                    <?php endif; ?>
                </div>
                <div class="icon-action cart-icon" onclick="window.location.href='<?= BASE_URL ?>/cart.php'" title="Keranjang">
                    <i class="fa-solid fa-cart-shopping fa-lg"></i>
                    <?php if ($cartCount > 0): ?>
                        <span class="icon-badge cart-badge"><?= $cartCount ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="user-profile-dropdown" style="position:relative;">
                <div style="display:flex; align-items:center; gap:8px; cursor:pointer;" onclick="this.parentElement.querySelector('.dropdown-menu').classList.toggle('show')">
                    <img src="<?= getAvatarUrl($currentUser['name'] ?? 'User') ?>" style="width:38px; height:38px; border-radius:50%; border: 2px solid var(--primary-color);" alt="Profile">
                    <span style="font-weight:600; font-size:0.9rem; max-width:120px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= sanitize($currentUser['name'] ?? '') ?></span>
                    <i class="fa-solid fa-chevron-down" style="font-size:0.7rem; color:var(--text-muted);"></i>
                </div>
                <div class="dropdown-menu" style="
                    display:none; position:absolute; top:calc(100% + 8px); right:0;
                    background:var(--white); border-radius:var(--radius); box-shadow:var(--shadow-hover);
                    min-width:200px; z-index:200; overflow:hidden; border:1px solid var(--border-color);
                ">
                    <?php if ($userRole === 'buyer'): ?>
                        <a href="<?= BASE_URL ?>/buyer/profile.php" class="dropdown-item"><i class="fa-solid fa-user"></i> Profil Saya</a>
                        <a href="<?= BASE_URL ?>/buyer/history.php" class="dropdown-item"><i class="fa-solid fa-clipboard-list"></i> Riwayat Belanja</a>
                    <?php elseif ($userRole === 'seller'): ?>
                        <a href="<?= BASE_URL ?>/seller/dashboard.php" class="dropdown-item"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
                        <a href="<?= BASE_URL ?>/seller/products.php" class="dropdown-item"><i class="fa-solid fa-box"></i> Kelola Produk</a>
                        <a href="<?= BASE_URL ?>/seller/profile.php" class="dropdown-item"><i class="fa-solid fa-store"></i> Profil Toko</a>
                    <?php elseif ($userRole === 'admin'): ?>
                        <a href="<?= BASE_URL ?>/admin/dashboard.php" class="dropdown-item"><i class="fa-solid fa-users-gear"></i> Admin Panel</a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/chat.php" class="dropdown-item"><i class="fa-solid fa-message"></i> Pesan</a>
                    <div style="border-top:1px solid var(--border-color);"></div>
                    <a href="<?= BASE_URL ?>/includes/logout.php" class="dropdown-item" style="color:#ef4444;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar</a>
                </div>
            </div>
        <?php else: ?>
            <button class="btn btn-outline" onclick="window.location.href='<?= BASE_URL ?>/login.php'">Masuk</button>
            <button class="btn btn-primary" onclick="window.location.href='<?= BASE_URL ?>/register.php'">Daftar</button>
        <?php endif; ?>
    </div>
</nav>

<style>
    .dropdown-menu.show { display: block !important; }
    .dropdown-item {
        display: flex; align-items: center; gap: 10px;
        padding: 0.75rem 1.25rem; color: var(--text-muted);
        font-size: 0.9rem; font-weight: 500;
        transition: var(--transition); cursor: pointer;
        text-decoration: none;
    }
    .dropdown-item:hover {
        background: rgba(16, 185, 129, 0.05); color: var(--primary-color);
    }
</style>

<script>
// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    document.querySelectorAll('.dropdown-menu.show').forEach(function(menu) {
        if (!menu.parentElement.contains(e.target)) {
            menu.classList.remove('show');
        }
    });
});
</script>
