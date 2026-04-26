<?php
require_once __DIR__ . '/../includes/helpers.php';
requireLogin();

$db = getDB();
$currentUser = getCurrentUser();
$userId = $_SESSION['user_id'];

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'review') {
    $orderId = (int) ($_POST['order_id'] ?? 0);
    $productId = (int) ($_POST['product_id'] ?? 0);
    $rating = (int) ($_POST['rating'] ?? 5);
    $comment = trim($_POST['comment'] ?? '');
    
    if ($orderId > 0 && $productId > 0 && $rating >= 1 && $rating <= 5) {
        // Check if review already exists
        $existing = $db->prepare("SELECT id FROM reviews WHERE order_id=? AND product_id=? AND user_id=?");
        $existing->execute([$orderId, $productId, $userId]);
        if (!$existing->fetch()) {
            $db->prepare("INSERT INTO reviews (product_id, user_id, order_id, rating, comment) VALUES (?,?,?,?,?)")
               ->execute([$productId, $userId, $orderId, $rating, $comment]);
            // Update product rating average
            $avgStmt = $db->prepare("SELECT AVG(rating) as avg_r FROM reviews WHERE product_id=?");
            $avgStmt->execute([$productId]);
            $avg = $avgStmt->fetch();
            $db->prepare("UPDATE products SET rating_avg=? WHERE id=?")->execute([round($avg['avg_r'],1), $productId]);
            setFlash('success', 'Ulasan berhasil dikirim!');
        }
    }
    header('Location: ' . BASE_URL . '/buyer/history.php');
    exit;
}

// Get orders
$stmt = $db->prepare("
    SELECT o.*, u.store_name as seller_name FROM orders o
    JOIN users u ON o.seller_id = u.id
    WHERE o.buyer_id = ? ORDER BY o.created_at DESC
");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();

// Get order items for each order
$orderItems = [];
if (!empty($orders)) {
    $orderIds = array_column($orders, 'id');
    $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
    $stmt = $db->prepare("SELECT oi.*, p.name, p.image_url FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id IN ({$placeholders})");
    $stmt->execute($orderIds);
    foreach ($stmt->fetchAll() as $item) {
        $orderItems[$item['order_id']][] = $item;
    }
}

// Check which products already have reviews
$reviewedProducts = [];
$stmt = $db->prepare("SELECT CONCAT(order_id, '-', product_id) as k FROM reviews WHERE user_id=?");
$stmt->execute([$userId]);
foreach ($stmt->fetchAll() as $r) { $reviewedProducts[$r['k']] = true; }

$statusBadge = [
    'pending' => ['Menunggu Konfirmasi', 'badge-warning'],
    'processing' => ['Sedang Diproses', 'badge-warning'],
    'shipped' => ['Sedang Dikirim', 'badge-warning'],
    'delivered' => ['Telah Sampai', 'badge-success'],
    'completed' => ['Selesai', 'badge-success']
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Belanja - Panenly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .profile-container { max-width:1000px; margin:2rem auto; padding:0 5%; display:flex; gap:2rem; }
        .profile-sidebar { width:250px; background:var(--white); border-radius:var(--radius); box-shadow:var(--shadow); padding:1.5rem; height:fit-content; }
        .profile-content { flex:1; background:var(--white); border-radius:var(--radius); box-shadow:var(--shadow); padding:2rem; }
        .sidebar-menu { list-style:none; margin-top:1.5rem; }
        .sidebar-menu li { margin-bottom:0.5rem; }
        .sidebar-menu a { display:flex; align-items:center; gap:10px; padding:0.75rem 1rem; color:var(--text-muted); border-radius:8px; font-weight:500; text-decoration:none; }
        .sidebar-menu a.active { background:rgba(16,185,129,0.1); color:var(--primary-color); }
        .sidebar-menu a:hover:not(.active) { background:var(--bg-color); color:var(--text-main); }
        .history-card { border:1px solid var(--border-color); border-radius:var(--radius); padding:1.5rem; margin-bottom:1.5rem; }
        .history-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; padding-bottom:1rem; border-bottom:1px solid var(--bg-color); }
        .badge { padding:0.25rem 0.5rem; border-radius:4px; font-size:0.8rem; font-weight:600; }
        .badge-success { background:rgba(16,185,129,0.1); color:var(--primary-dark); }
        .badge-warning { background:rgba(245,158,11,0.1); color:#b45309; }
    </style>
</head>
<body>
    <?php $navbarType = 'simple'; include __DIR__ . '/../includes/navbar.php'; ?>
    <?= renderFlash() ?>
    <div class="profile-container">
        <div class="profile-sidebar">
            <div style="text-align:center;padding-bottom:1.5rem;border-bottom:1px solid var(--border-color);">
                <img src="<?= getAvatarUrl($currentUser['name']) ?>" style="width:80px;border-radius:50%;margin-bottom:1rem;">
                <h3 style="font-size:1.1rem;"><?= sanitize($currentUser['name']) ?></h3>
                <span style="font-size:0.85rem;color:var(--text-muted);">Pembeli</span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="profile.php"><i class="fa-solid fa-user"></i> Biodata Diri</a></li>
                <li><a href="history.php" class="active"><i class="fa-solid fa-clipboard-list"></i> Riwayat Belanja</a></li>
                <li><a href="../chat.php"><i class="fa-solid fa-message"></i> Pesan Masuk</a></li>
                <li><a href="<?= BASE_URL ?>/includes/logout.php" style="color:#ef4444;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar</a></li>
            </ul>
        </div>
        <div class="profile-content">
            <h2 style="margin-bottom:2rem;">Riwayat Belanja</h2>
            <?php if(empty($orders)): ?>
            <p style="color:var(--text-muted);text-align:center;padding:2rem;">Belum ada pesanan.</p>
            <?php endif; ?>
            <?php foreach($orders as $order): ?>
            <?php $badge = $statusBadge[$order['status']] ?? ['Unknown','badge-warning']; ?>
            <div class="history-card">
                <div class="history-header">
                    <div>
                        <span style="font-weight:600;margin-right:1rem;"><i class="fa-solid fa-store"></i> <?= sanitize($order['seller_name']) ?></span>
                        <span style="color:var(--text-muted);font-size:0.85rem;"><?= date('d M Y', strtotime($order['created_at'])) ?></span>
                    </div>
                    <span class="badge <?= $badge[1] ?>"><?= $badge[0] ?></span>
                </div>
                <?php foreach(($orderItems[$order['id']] ?? []) as $item): ?>
                <div style="display:flex;gap:1rem;align-items:center;margin-bottom:0.75rem;">
                    <img src="<?= getProductImage($item['image_url']??'') ?>" style="width:60px;height:60px;border-radius:8px;object-fit:cover;" onerror="this.src='https://via.placeholder.com/60'">
                    <div style="flex:1;">
                        <div style="font-weight:600;"><?= sanitize($item['name']) ?></div>
                        <div style="font-size:0.85rem;color:var(--text-muted);"><?= $item['quantity'] ?> x <?= formatRupiah($item['price_at_purchase']) ?></div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:0.85rem;color:var(--text-muted);">Subtotal</div>
                        <div style="font-weight:700;color:var(--primary-color);"><?= formatRupiah($item['price_at_purchase']*$item['quantity']) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <div style="text-align:right;font-weight:700;margin-top:0.5rem;padding-top:0.75rem;border-top:1px solid var(--bg-color);">Total: <?= formatRupiah($order['total']) ?></div>
                <div style="margin-top:1rem;text-align:right;display:flex;gap:0.5rem;justify-content:flex-end;flex-wrap:wrap;">
                    <?php if(in_array($order['status'], ['shipped','delivered'])): ?>
                    <a href="<?= BASE_URL ?>/tracking.php?order_id=<?= $order['id'] ?>" class="btn btn-outline" style="padding:0.5rem 1rem;text-decoration:none;">Lacak Paket</a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/chat.php?partner=<?= $order['seller_id'] ?>" class="btn btn-primary" style="padding:0.5rem 1rem;text-decoration:none;">Hubungi Penjual</a>
                    <?php if($order['status'] === 'completed'): ?>
                        <?php foreach(($orderItems[$order['id']] ?? []) as $item): ?>
                            <?php if(!isset($reviewedProducts[$order['id'].'-'.$item['product_id']])): ?>
                            <button class="btn btn-outline" style="padding:0.5rem 1rem;" onclick="document.getElementById('review-<?= $order['id'] ?>-<?= $item['product_id'] ?>').style.display='block'">Beri Ulasan</button>
                            <!-- Review form (hidden) -->
                            <div id="review-<?= $order['id'] ?>-<?= $item['product_id'] ?>" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:999;display:none;align-items:center;justify-content:center;">
                                <form method="POST" style="background:var(--white);padding:2rem;border-radius:var(--radius);max-width:400px;width:90%;">
                                    <h3 style="margin-bottom:1rem;">Beri Ulasan - <?= sanitize($item['name']) ?></h3>
                                    <input type="hidden" name="action" value="review">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                    <div style="margin-bottom:1rem;">
                                        <label style="font-weight:500;">Rating</label>
                                        <select name="rating" style="width:100%;padding:0.5rem;border:1px solid var(--border-color);border-radius:8px;">
                                            <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                                            <option value="4">⭐⭐⭐⭐ (4)</option>
                                            <option value="3">⭐⭐⭐ (3)</option>
                                            <option value="2">⭐⭐ (2)</option>
                                            <option value="1">⭐ (1)</option>
                                        </select>
                                    </div>
                                    <div style="margin-bottom:1rem;">
                                        <label style="font-weight:500;">Komentar</label>
                                        <textarea name="comment" rows="3" style="width:100%;padding:0.5rem;border:1px solid var(--border-color);border-radius:8px;font-family:inherit;box-sizing:border-box;" placeholder="Tulis ulasan Anda..."></textarea>
                                    </div>
                                    <div style="display:flex;gap:0.5rem;justify-content:flex-end;">
                                        <button type="button" class="btn btn-outline" onclick="this.closest('[id^=review]').style.display='none'">Batal</button>
                                        <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
                                    </div>
                                </form>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
