<?php
require_once __DIR__ . '/../includes/helpers.php';
requireApprovedSeller();

$db = getDB();
$currentUser = getCurrentUser();

$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $storeName = trim($_POST['store_name'] ?? '');
    $storeLocation = trim($_POST['store_location'] ?? '');
    
    $db->prepare("UPDATE users SET name=?, phone=?, address=?, store_name=?, store_location=? WHERE id=?")
       ->execute([$name, $phone, $address, $storeName, $storeLocation, $_SESSION['user_id']]);
    $_SESSION['user_name'] = $name;
    $success = 'Profil berhasil diperbarui.';
    $stmt = $db->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute([$_SESSION['user_id']]);
    $currentUser = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Toko - Freshly</title>
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
        .form-group { margin-bottom:1.5rem; }
        .form-group label { display:block; margin-bottom:0.5rem; font-weight:500; font-size:0.9rem; }
        .form-control { width:100%; padding:0.75rem; border:1px solid var(--border-color); border-radius:8px; font-family:inherit; box-sizing:border-box; outline:none; }
        .form-control:focus { border-color:var(--primary-color); }
        .success-msg { background:rgba(16,185,129,0.1); color:var(--primary-dark); padding:0.75rem; border-radius:8px; margin-bottom:1.5rem; text-align:center; }
    </style>
</head>
<body>
    <?php $navbarType = 'simple'; include __DIR__ . '/../includes/navbar.php'; ?>
    <div class="profile-container">
        <div class="profile-sidebar">
            <div style="text-align:center;padding-bottom:1.5rem;border-bottom:1px solid var(--border-color);">
                <img src="<?= getAvatarUrl($currentUser['name']) ?>" style="width:80px;border-radius:50%;margin-bottom:1rem;">
                <h3 style="font-size:1.1rem;"><?= sanitize($currentUser['store_name']??$currentUser['name']) ?></h3>
                <span style="font-size:0.85rem;color:var(--text-muted);">Petani/Penjual</span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
                <li><a href="products.php"><i class="fa-solid fa-box"></i> Kelola Produk</a></li>
                <li><a href="chat.php"><i class="fa-solid fa-message"></i> Pesan Masuk</a></li>
                <li><a href="profile.php" class="active"><i class="fa-solid fa-store"></i> Profil Toko</a></li>
                <li><a href="<?= BASE_URL ?>/includes/logout.php" style="color:#ef4444;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar</a></li>
            </ul>
        </div>
        <div class="profile-content">
            <h2 style="margin-bottom:2rem;">Pengaturan Toko</h2>
            <?php if($success): ?><div class="success-msg"><?= sanitize($success) ?></div><?php endif; ?>
            <form method="POST">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
                    <div class="form-group"><label>Nama Toko/Petani</label><input type="text" name="name" class="form-control" value="<?= sanitize($currentUser['name']) ?>"></div>
                    <div class="form-group"><label>Nama Toko</label><input type="text" name="store_name" class="form-control" value="<?= sanitize($currentUser['store_name']??'') ?>"></div>
                    <div class="form-group"><label>Nomor HP</label><input type="text" name="phone" class="form-control" value="<?= sanitize($currentUser['phone']??'') ?>"></div>
                    <div class="form-group"><label>Lokasi Toko</label><input type="text" name="store_location" class="form-control" value="<?= sanitize($currentUser['store_location']??'') ?>"></div>
                    <div class="form-group" style="grid-column:span 2;"><label>Email</label><input type="email" class="form-control" value="<?= sanitize($currentUser['email']) ?>" readonly style="background:var(--bg-color);"></div>
                    <div class="form-group" style="grid-column:span 2;"><label>Alamat Lengkap</label><textarea name="address" class="form-control" rows="3"><?= sanitize($currentUser['address']??'') ?></textarea></div>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</body>
</html>
