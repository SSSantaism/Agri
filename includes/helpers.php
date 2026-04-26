<?php
/**
 * Panenly - Helper Functions
 */

require_once __DIR__ . '/config.php';

/**
 * Format angka ke Rupiah
 */
function formatRupiah(int $amount): string {
    return "Rp" . number_format($amount, 0, ',', '.');
}

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Redirect to login if not authenticated
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        setFlash('warning', 'Silakan masuk terlebih dahulu.');
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

/**
 * Require specific role, redirect if unauthorized
 */
function requireRole(string $role): void {
    requireLogin();
    if ($_SESSION['role'] !== $role) {
        setFlash('error', 'Anda tidak memiliki akses ke halaman ini.');
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

/**
 * Require seller with approved status
 */
function requireApprovedSeller(): void {
    requireLogin();
    if ($_SESSION['role'] !== 'seller') {
        setFlash('error', 'Anda tidak memiliki akses ke halaman ini.');
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
    // Check seller_status
    $db = getDB();
    $stmt = $db->prepare("SELECT seller_status FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if (!$user || $user['seller_status'] !== 'approved') {
        setFlash('warning', 'Akun penjual Anda belum disetujui oleh admin.');
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

/**
 * Get current user data from database
 */
function getCurrentUser(): ?array {
    if (!isLoggedIn()) return null;
    
    static $user = null;
    if ($user === null) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
    }
    return $user;
}

/**
 * Get cart item count for current user
 */
function getCartCount(): int {
    if (!isLoggedIn()) return 0;
    
    $db = getDB();
    $stmt = $db->prepare("SELECT COALESCE(SUM(quantity), 0) FROM cart_items WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return (int) $stmt->fetchColumn();
}

/**
 * Get unread message count for current user
 */
function getUnreadMessageCount(): int {
    if (!isLoggedIn()) return 0;
    
    $db = getDB();
    $stmt = $db->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
    $stmt->execute([$_SESSION['user_id']]);
    return (int) $stmt->fetchColumn();
}

/**
 * Sanitize user input
 */
function sanitize(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate unique order number
 */
function generateOrderNumber(): string {
    return 'PNL-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

/**
 * Generate tracking number
 */
function generateTrackingNumber(): string {
    return 'PNL-' . mt_rand(1000000000, 9999999999);
}

/**
 * Set flash message
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Render flash message HTML
 */
function renderFlash(): string {
    $flash = getFlash();
    if (!$flash) return '';
    
    $colors = [
        'success' => '#10b981',
        'error'   => '#ef4444',
        'warning' => '#f59e0b',
        'info'    => '#3b82f6'
    ];
    $color = $colors[$flash['type']] ?? '#6b7280';
    
    return '<div class="flash-message" style="
        max-width: 600px;
        margin: 1rem auto;
        padding: 1rem 1.5rem;
        background: ' . $color . '15;
        border: 1px solid ' . $color . '40;
        border-radius: 8px;
        color: ' . $color . ';
        font-weight: 500;
        font-size: 0.95rem;
        text-align: center;
        animation: flashIn 0.3s ease;
    ">' . sanitize($flash['message']) . '</div>
    <style>@keyframes flashIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }</style>';
}

/**
 * Handle image upload, returns filename or null on failure
 */
function handleImageUpload(array $file, string $prefix = 'product'): ?string {
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    if ($file['size'] > MAX_UPLOAD_SIZE) return null;
    if (!in_array($file['type'], ALLOWED_IMAGE_TYPES)) return null;
    
    // Create upload directory if not exists
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    
    // Generate unique filename
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $prefix . '_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
    $destination = UPLOAD_DIR . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $filename;
    }
    
    return null;
}

/**
 * Get product image URL (handles both uploaded and external URLs)
 */
function getProductImage(string $imageUrl): string {
    if (str_starts_with($imageUrl, 'http')) {
        return $imageUrl;
    }
    return BASE_URL . '/uploads/products/' . $imageUrl;
}

/**
 * Get user avatar URL
 */
function getAvatarUrl(?string $name): string {
    $name = $name ?? 'User';
    return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=10b981&color=fff';
}

/**
 * Time ago helper (Indonesian)
 */
function timeAgo(string $datetime): string {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    if ($diff->y > 0) return $diff->y . ' tahun yang lalu';
    if ($diff->m > 0) return $diff->m . ' bulan yang lalu';
    if ($diff->d > 6) return floor($diff->d / 7) . ' minggu yang lalu';
    if ($diff->d > 0) return $diff->d . ' hari yang lalu';
    if ($diff->h > 0) return $diff->h . ' jam yang lalu';
    if ($diff->i > 0) return $diff->i . ' menit yang lalu';
    return 'Baru saja';
}
