<?php
require_once __DIR__ . '/../includes/helpers.php';
requireApprovedSeller();

$db = getDB();
$sellerId = $_SESSION['user_id'];
$currentUser = getCurrentUser();

// Stats
$revenue = $db->prepare("SELECT COALESCE(SUM(total),0) FROM orders WHERE seller_id=? AND status IN ('shipped','delivered','completed') AND MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())");
$revenue->execute([$sellerId]);
$monthRevenue = (int) $revenue->fetchColumn();

$newOrders = $db->prepare("SELECT COUNT(*) FROM orders WHERE seller_id=? AND status IN ('pending','processing')");
$newOrders->execute([$sellerId]);
$pendingOrders = (int) $newOrders->fetchColumn();

$preorderCount = $db->prepare("SELECT COUNT(*) FROM products WHERE seller_id=? AND preorder_available=1 AND is_active=1");
$preorderCount->execute([$sellerId]);
$activePreorders = (int) $preorderCount->fetchColumn();

$ratingStmt = $db->prepare("SELECT AVG(r.rating) as avg_rating, COUNT(r.id) as total FROM reviews r JOIN products p ON r.product_id=p.id WHERE p.seller_id=?");
$ratingStmt->execute([$sellerId]);
$ratingData = $ratingStmt->fetch();
$avgRating = $ratingData['avg_rating'] ? round($ratingData['avg_rating'], 1) : 0;
$totalReviews = (int) $ratingData['total'];

// Monthly revenue for chart (last 6 months)
$chartData = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-{$i} months"));
    $stmt = $db->prepare("SELECT COALESCE(SUM(total),0) FROM orders WHERE seller_id=? AND status IN ('shipped','delivered','completed') AND DATE_FORMAT(created_at,'%Y-%m')=?");
    $stmt->execute([$sellerId, $month]);
    $chartData[] = ['label' => date('M', strtotime($month)), 'value' => round((int)$stmt->fetchColumn() / 1000000, 1)];
}

// Recent orders
$stmt = $db->prepare("
    SELECT o.*, u.name as buyer_name FROM orders o
    JOIN users u ON o.buyer_id = u.id
    WHERE o.seller_id = ? ORDER BY o.created_at DESC LIMIT 10
");
$stmt->execute([$sellerId]);
$recentOrders = $stmt->fetchAll();

$statusLabels = ['pending'=>'Menunggu','processing'=>'Diproses','shipped'=>'Dikirim','delivered'=>'Sampai','completed'=>'Selesai'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penjual - Panenly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-container { display:flex; min-height:100vh; background:var(--bg-color); }
        .sidebar { width:250px; background:var(--white); border-right:1px solid var(--border-color); padding:1.5rem; }
        .main-content { flex:1; padding:2rem; overflow-y:auto; }
        .stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1.5rem; margin-bottom:2rem; }
        .stat-card { background:var(--white); padding:1.5rem; border-radius:var(--radius); box-shadow:var(--shadow); }
        .stat-title { color:var(--text-muted); font-size:0.9rem; margin-bottom:0.5rem; }
        .stat-value { font-size:1.5rem; font-weight:700; color:var(--text-main); }
        .chart-container { background:var(--white); padding:1.5rem; border-radius:var(--radius); box-shadow:var(--shadow); margin-bottom:2rem; }
        .sidebar-menu { list-style:none; margin-top:2rem; }
        .sidebar-menu li { margin-bottom:0.5rem; }
        .sidebar-menu a { display:flex; align-items:center; gap:10px; padding:0.75rem 1rem; color:var(--text-muted); border-radius:8px; font-weight:500; text-decoration:none; }
        .sidebar-menu a.active { background:rgba(16,185,129,0.1); color:var(--primary-color); }
        .sidebar-menu a:hover:not(.active) { background:var(--bg-color); color:var(--text-main); }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <a href="../index.php" class="nav-brand" style="margin-bottom:2rem;"><i class="fa-solid fa-leaf"></i> Panenly</a>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
                <li><a href="products.php"><i class="fa-solid fa-box"></i> Kelola Produk</a></li>
                <li><a href="../chat.php"><i class="fa-solid fa-message"></i> Pesan Masuk</a></li>
                <li><a href="profile.php"><i class="fa-solid fa-store"></i> Profil Toko</a></li>
                <li><a href="<?= BASE_URL ?>/includes/logout.php" style="color:#ef4444;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;">
                <h2>Ringkasan <?= sanitize($currentUser['store_name'] ?? $currentUser['name']) ?></h2>
                <a href="products.php" class="btn btn-primary" style="text-decoration:none;">+ Tambah Produk Baru</a>
            </div>
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-title">Pendapatan Bulan Ini</div>
                    <div class="stat-value"><?= formatRupiah($monthRevenue) ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Pesanan Baru</div>
                    <div class="stat-value"><?= $pendingOrders ?></div>
                    <div style="font-size:0.8rem;color:var(--text-muted);margin-top:5px;">Menunggu diproses</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Pre-Order Aktif</div>
                    <div class="stat-value"><?= $activePreorders ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Rating Toko</div>
                    <div class="stat-value"><i class="fa-solid fa-star" style="color:#f59e0b;font-size:1.2rem;"></i> <?= $avgRating ?></div>
                    <div style="font-size:0.8rem;color:var(--text-muted);margin-top:5px;">Dari <?= $totalReviews ?> ulasan</div>
                </div>
            </div>
            <div class="chart-container">
                <h3 style="margin-bottom:1.5rem;">Statistik Penjualan</h3>
                <canvas id="salesChart" height="100"></canvas>
            </div>
            
            <!-- Recent Orders -->
            <?php if(!empty($recentOrders)): ?>
            <div class="chart-container">
                <h3 style="margin-bottom:1.5rem;">Pesanan Terbaru</h3>
                <table style="width:100%;border-collapse:collapse;text-align:left;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border-color);">
                            <th style="padding:0.75rem;">No. Order</th>
                            <th style="padding:0.75rem;">Pembeli</th>
                            <th style="padding:0.75rem;">Total</th>
                            <th style="padding:0.75rem;">Status</th>
                            <th style="padding:0.75rem;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($recentOrders as $order): ?>
                        <tr style="border-bottom:1px solid var(--border-color);">
                            <td style="padding:0.75rem;font-size:0.9rem;"><?= sanitize($order['order_number']) ?></td>
                            <td style="padding:0.75rem;"><?= sanitize($order['buyer_name']) ?></td>
                            <td style="padding:0.75rem;font-weight:600;"><?= formatRupiah($order['total']) ?></td>
                            <td style="padding:0.75rem;"><span class="badge <?= in_array($order['status'],['delivered','completed'])?'badge-success':'badge-warning' ?>" style="padding:4px 8px;border-radius:4px;font-size:0.8rem;font-weight:600;<?= in_array($order['status'],['delivered','completed'])?'background:rgba(16,185,129,0.1);color:var(--primary-dark);':'background:rgba(245,158,11,0.1);color:#b45309;' ?>"><?= $statusLabels[$order['status']]??$order['status'] ?></span></td>
                            <td style="padding:0.75rem;">
                                <?php if($order['status']==='pending'): ?>
                                <button class="btn btn-primary" style="padding:0.25rem 0.5rem;font-size:0.8rem;" onclick="updateOrder(<?= $order['id'] ?>,'processing','Pesanan sedang diproses penjual')">Proses</button>
                                <?php elseif($order['status']==='processing'): ?>
                                <button class="btn btn-primary" style="padding:0.25rem 0.5rem;font-size:0.8rem;" onclick="updateOrder(<?= $order['id'] ?>,'shipped','Pesanan dibawa oleh kurir')">Kirim</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </main>
    </div>
    <script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($chartData, 'label')) ?>,
            datasets: [{
                label: 'Pendapatan (Juta Rp)',
                data: <?= json_encode(array_column($chartData, 'value')) ?>,
                borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.1)',
                tension: 0.4, fill: true
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'top' } } }
    });
    
    function updateOrder(orderId, status, statusText) {
        fetch('<?= BASE_URL ?>/api/orders.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=update_status&order_id=' + orderId + '&status=' + status + '&status_text=' + encodeURIComponent(statusText)
        }).then(r => r.json()).then(data => {
            alert(data.message);
            if (data.success) location.reload();
        });
    }
    </script>
</body>
</html>
