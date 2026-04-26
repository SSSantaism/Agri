<?php
require_once __DIR__ . '/includes/helpers.php';
requireLogin();

$db = getDB();
$orderId = (int) ($_GET['order_id'] ?? 0);

if ($orderId <= 0) {
    header('Location: ' . BASE_URL . '/buyer/history.php');
    exit;
}

// Get order (must belong to current user as buyer)
$stmt = $db->prepare("
    SELECT o.*, u.store_name as seller_name
    FROM orders o JOIN users u ON o.seller_id = u.id
    WHERE o.id = ? AND o.buyer_id = ?
");
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    setFlash('error', 'Pesanan tidak ditemukan.');
    header('Location: ' . BASE_URL . '/buyer/history.php');
    exit;
}

// Get tracking events (newest first for display)
$stmt = $db->prepare("SELECT * FROM order_tracking WHERE order_id = ? ORDER BY created_at DESC");
$stmt->execute([$orderId]);
$trackingEvents = $stmt->fetchAll();

// Get order items
$stmt = $db->prepare("
    SELECT oi.*, p.name, p.image_url FROM order_items oi
    JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?
");
$stmt->execute([$orderId]);
$orderItems = $stmt->fetchAll();

$statusLabels = [
    'pending' => 'Menunggu Konfirmasi',
    'processing' => 'Sedang Diproses',
    'shipped' => 'Dalam Pengiriman',
    'delivered' => 'Telah Sampai',
    'completed' => 'Selesai'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pengiriman - Panenly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .tracking-container { max-width:800px; margin:2rem auto 4rem; padding:0 5%; }
        .tracking-card { background:var(--white); padding:2rem; border-radius:var(--radius); box-shadow:var(--shadow); margin-top:1.5rem; }
        .timeline { position:relative; padding-left:2rem; margin-top:2rem; }
        .timeline::before { content:''; position:absolute; left:0.5rem; top:0.5rem; bottom:0; width:2px; background:var(--border-color); }
        .timeline-item { position:relative; margin-bottom:2rem; }
        .timeline-item:last-child { margin-bottom:0; }
        .timeline-item .dot { position:absolute; left:-2rem; top:0.25rem; width:1rem; height:1rem; border-radius:50%; background:var(--border-color); border:3px solid var(--white); box-shadow:0 0 0 1px var(--border-color); z-index:1; }
        .timeline-item:first-child .dot { background:var(--primary-color); box-shadow:0 0 0 2px rgba(16,185,129,0.2); border-color:var(--white); }
        .timeline-item h4 { margin-bottom:0.25rem; color:var(--text-muted); }
        .timeline-item:first-child h4 { color:var(--text-main); }
    </style>
</head>
<body>
    <?php $navbarType = 'simple'; include __DIR__ . '/includes/navbar.php'; ?>
    <?= renderFlash() ?>
    <div class="tracking-container">
        <h2>Status Pengiriman</h2>
        <div class="tracking-card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;border-bottom:1px solid var(--border-color);padding-bottom:1rem;">
                <div>
                    <div style="font-size:0.9rem;color:var(--text-muted);">No. Resi</div>
                    <div style="font-weight:700;font-size:1.1rem;"><?= sanitize($order['tracking_number']??'-') ?></div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:0.9rem;color:var(--text-muted);">Status</div>
                    <div style="font-weight:600;color:var(--primary-color);"><?= $statusLabels[$order['status']] ?? $order['status'] ?></div>
                </div>
            </div>

            <!-- Order items summary -->
            <div style="margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid var(--border-color);">
                <div style="font-weight:600;margin-bottom:0.75rem;"><i class="fa-solid fa-store"></i> <?= sanitize($order['seller_name']) ?></div>
                <?php foreach($orderItems as $oi): ?>
                <div style="display:flex;gap:10px;align-items:center;margin-bottom:0.5rem;">
                    <img src="<?= getProductImage($oi['image_url']??'') ?>" style="width:40px;height:40px;border-radius:4px;object-fit:cover;" onerror="this.src='https://via.placeholder.com/40'">
                    <div style="flex:1;font-size:0.9rem;"><?= sanitize($oi['name']) ?> x<?= $oi['quantity'] ?></div>
                    <div style="font-weight:600;font-size:0.9rem;"><?= formatRupiah($oi['price_at_purchase']*$oi['quantity']) ?></div>
                </div>
                <?php endforeach; ?>
                <div style="text-align:right;font-weight:700;color:var(--primary-color);margin-top:0.5rem;">Total: <?= formatRupiah($order['total']) ?></div>
            </div>

            <div class="timeline">
                <?php foreach($trackingEvents as $event): ?>
                <div class="timeline-item">
                    <div class="dot"></div>
                    <div>
                        <h4><?= sanitize($event['status_text']) ?></h4>
                        <span style="font-size:0.85rem;color:var(--text-muted);">
                            <?= date('d M Y, H:i', strtotime($event['created_at'])) ?>
                            <?= $event['location'] ? ' - ' . sanitize($event['location']) : '' ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <a href="<?= BASE_URL ?>/buyer/history.php" class="btn btn-outline" style="width:100%;margin-top:2rem;text-align:center;display:block;text-decoration:none;">Lihat Riwayat Pesanan</a>
        </div>
    </div>
</body>
</html>
