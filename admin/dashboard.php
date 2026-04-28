<?php
require_once __DIR__ . '/../includes/helpers.php';
requireRole('admin');

$db = getDB();

// Handle approve/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int) ($_POST['user_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    
    if ($userId > 0 && in_array($action, ['approve', 'reject'])) {
        $newStatus = ($action === 'approve') ? 'approved' : 'rejected';
        $db->prepare("UPDATE users SET seller_status=? WHERE id=? AND role='seller'")->execute([$newStatus, $userId]);
        $msg = ($action === 'approve') ? 'Penjual berhasil disetujui.' : 'Penjual ditolak.';
        setFlash('success', $msg);
        header('Location: ' . BASE_URL . '/admin/dashboard.php');
        exit;
    }
}

// Get pending sellers
$pendingSellers = $db->query("SELECT * FROM users WHERE role='seller' AND seller_status='pending' ORDER BY created_at DESC")->fetchAll();

// Stats
$totalUsers = (int) $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalSellers = (int) $db->query("SELECT COUNT(*) FROM users WHERE role='seller' AND seller_status='approved'")->fetchColumn();
$totalProducts = (int) $db->query("SELECT COUNT(*) FROM products WHERE is_active=1")->fetchColumn();
$totalOrders = (int) $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Freshly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .dashboard-container { display:flex; min-height:100vh; background:var(--bg-color); }
        .sidebar { width:250px; background:var(--white); border-right:1px solid var(--border-color); padding:1.5rem; }
        .main-content { flex:1; padding:2rem; overflow-y:auto; }
        .chart-container { background:var(--white); padding:1.5rem; border-radius:var(--radius); box-shadow:var(--shadow); margin-bottom:2rem; }
        .sidebar-menu { list-style:none; margin-top:2rem; }
        .sidebar-menu li { margin-bottom:0.5rem; }
        .sidebar-menu a { display:flex; align-items:center; gap:10px; padding:0.75rem 1rem; color:var(--text-muted); border-radius:8px; font-weight:500; text-decoration:none; }
        .sidebar-menu a.active { background:rgba(16,185,129,0.1); color:var(--primary-color); }
        .sidebar-menu a:hover:not(.active) { background:var(--bg-color); color:var(--text-main); }
        .stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1.5rem; margin-bottom:2rem; }
        .stat-card { background:var(--white); padding:1.5rem; border-radius:var(--radius); box-shadow:var(--shadow); }
        .stat-title { color:var(--text-muted); font-size:0.9rem; margin-bottom:0.5rem; }
        .stat-value { font-size:1.5rem; font-weight:700; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <a href="../index.php" class="nav-brand" style="margin-bottom:2rem;"><i class="fa-solid fa-leaf"></i> Freshly Admin</a>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active"><i class="fa-solid fa-users-gear"></i> Verifikasi Penjual</a></li>
                <li><a href="<?= BASE_URL ?>/includes/logout.php" style="color:#ef4444;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <?= renderFlash() ?>
            
            <h2 style="margin-bottom:2rem;">Admin Dashboard</h2>
            
            <div class="stat-grid">
                <div class="stat-card"><div class="stat-title">Total Pengguna</div><div class="stat-value"><?= $totalUsers ?></div></div>
                <div class="stat-card"><div class="stat-title">Penjual Aktif</div><div class="stat-value"><?= $totalSellers ?></div></div>
                <div class="stat-card"><div class="stat-title">Total Produk</div><div class="stat-value"><?= $totalProducts ?></div></div>
                <div class="stat-card"><div class="stat-title">Total Pesanan</div><div class="stat-value"><?= $totalOrders ?></div></div>
            </div>
            
            <h3 style="margin-bottom:1rem;">Permintaan Pendaftaran Penjual (<?= count($pendingSellers) ?>)</h3>
            <div class="chart-container">
                <?php if(empty($pendingSellers)): ?>
                <p style="text-align:center;color:var(--text-muted);padding:2rem;">Tidak ada permintaan pendaftaran baru.</p>
                <?php else: ?>
                <table style="width:100%;border-collapse:collapse;text-align:left;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border-color);">
                            <th style="padding:1rem;">Toko / Petani</th>
                            <th style="padding:1rem;">Kontak</th>
                            <th style="padding:1rem;">Tanggal Daftar</th>
                            <th style="padding:1rem;">Status</th>
                            <th style="padding:1rem;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($pendingSellers as $seller): ?>
                        <tr style="border-bottom:1px solid var(--border-color);">
                            <td style="padding:1rem;">
                                <div style="font-weight:600;"><?= sanitize($seller['store_name']??$seller['name']) ?></div>
                                <div style="font-size:0.85rem;color:var(--text-muted);"><?= sanitize($seller['store_location']??'') ?></div>
                            </td>
                            <td style="padding:1rem;">
                                <?= sanitize($seller['email']) ?><br>
                                <span style="font-size:0.85rem;color:var(--text-muted);"><?= sanitize($seller['phone']??'') ?></span>
                            </td>
                            <td style="padding:1rem;"><?= date('d M Y', strtotime($seller['created_at'])) ?></td>
                            <td style="padding:1rem;"><span style="background:rgba(245,158,11,0.1);color:#b45309;padding:4px 8px;border-radius:4px;font-size:0.8rem;font-weight:600;">Menunggu Review</span></td>
                            <td style="padding:1rem;">
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?= $seller['id'] ?>">
                                    <button type="submit" name="action" value="approve" class="btn btn-primary" style="padding:0.25rem 0.75rem;font-size:0.8rem;margin-right:5px;">Terima</button>
                                    <button type="submit" name="action" value="reject" class="btn btn-outline" style="padding:0.25rem 0.75rem;font-size:0.8rem;color:#ef4444;border-color:#ef4444;" onclick="return confirm('Tolak penjual ini?')">Tolak</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
