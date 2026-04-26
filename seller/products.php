<?php
require_once __DIR__ . '/../includes/helpers.php';
requireApprovedSeller();

$db = getDB();
$sellerId = $_SESSION['user_id'];

// Handle add product
$formError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $name = trim($_POST['product_name'] ?? '');
    $price = (int) ($_POST['price'] ?? 0);
    $originalPrice = (int) ($_POST['original_price'] ?? 0) ?: null;
    $stock = (int) ($_POST['stock'] ?? 0);
    $weight = trim($_POST['weight'] ?? '');
    $categoryId = (int) ($_POST['category_id'] ?? 0) ?: null;
    $quality = trim($_POST['quality'] ?? '');
    $harvestMethod = trim($_POST['harvest_method'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $preorder = isset($_POST['preorder']) ? 1 : 0;
    $preorderDate = $_POST['preorder_date'] ?? null;
    $badge = trim($_POST['badge'] ?? '') ?: null;
    
    // Handle image upload
    $imageUrl = '';
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $uploaded = handleImageUpload($_FILES['product_image']);
        if ($uploaded) $imageUrl = $uploaded;
    }
    if (empty($imageUrl)) {
        $imageUrl = trim($_POST['image_url'] ?? '') ?: 'https://via.placeholder.com/400x300?text=Panenly';
    }
    
    if (empty($name) || $price <= 0) {
        $formError = 'Nama produk dan harga wajib diisi.';
    } else {
        $stmt = $db->prepare("INSERT INTO products (seller_id, category_id, name, weight, price, original_price, stock, quality, harvest_method, description, image_url, badge, preorder_available, preorder_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$sellerId, $categoryId, $name, $weight, $price, $originalPrice, $stock, $quality, $harvestMethod, $description, $imageUrl, $badge, $preorder, $preorderDate ?: null]);
        setFlash('success', 'Produk berhasil ditambahkan!');
        header('Location: ' . BASE_URL . '/seller/products.php');
        exit;
    }
}

// Handle edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    $pid = (int) ($_POST['product_id'] ?? 0);
    $name = trim($_POST['product_name'] ?? '');
    $price = (int) ($_POST['price'] ?? 0);
    $stock = (int) ($_POST['stock'] ?? 0);
    $preorder = isset($_POST['preorder']) ? 1 : 0;
    
    if ($pid > 0 && !empty($name) && $price > 0) {
        // Handle image
        $imageUpdate = '';
        $params = [$name, $price, $stock, $preorder, $pid, $sellerId];
        
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $uploaded = handleImageUpload($_FILES['product_image']);
            if ($uploaded) {
                $imageUpdate = ', image_url=?';
                array_splice($params, 4, 0, [$uploaded]);
            }
        }
        
        $db->prepare("UPDATE products SET name=?, price=?, stock=?, preorder_available=? {$imageUpdate} WHERE id=? AND seller_id=?")->execute($params);
        setFlash('success', 'Produk berhasil diperbarui!');
        header('Location: ' . BASE_URL . '/seller/products.php');
        exit;
    }
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $pid = (int) ($_POST['product_id'] ?? 0);
    $db->prepare("UPDATE products SET is_active=0 WHERE id=? AND seller_id=?")->execute([$pid, $sellerId]);
    setFlash('success', 'Produk berhasil dihapus.');
    header('Location: ' . BASE_URL . '/seller/products.php');
    exit;
}

// Get products
$stmt = $db->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id=c.id WHERE p.seller_id=? AND p.is_active=1 ORDER BY p.created_at DESC");
$stmt->execute([$sellerId]);
$products = $stmt->fetchAll();

$categories = $db->query("SELECT * FROM categories ORDER BY id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Panenly</title>
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
        .form-input { width:100%; padding:0.75rem; border:1px solid var(--border-color); border-radius:8px; font-family:inherit; box-sizing:border-box; outline:none; }
        .form-input:focus { border-color:var(--primary-color); }
        .error-msg { background:rgba(239,68,68,0.1); color:#ef4444; padding:0.75rem; border-radius:8px; margin-bottom:1rem; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <a href="../index.php" class="nav-brand" style="margin-bottom:2rem;"><i class="fa-solid fa-leaf"></i> Panenly</a>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
                <li><a href="products.php" class="active"><i class="fa-solid fa-box"></i> Kelola Produk</a></li>
                <li><a href="../chat.php"><i class="fa-solid fa-message"></i> Pesan Masuk</a></li>
                <li><a href="profile.php"><i class="fa-solid fa-store"></i> Profil Toko</a></li>
                <li><a href="<?= BASE_URL ?>/includes/logout.php" style="color:#ef4444;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <?= renderFlash() ?>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;">
                <h2>Kelola Produk</h2>
                <button class="btn btn-primary" onclick="document.getElementById('addForm').style.display=document.getElementById('addForm').style.display==='none'?'block':'none'">+ Tambah Produk</button>
            </div>

            <!-- Add Product Form -->
            <div class="chart-container" id="addForm" style="display:none;">
                <h3 style="margin-bottom:1.5rem;">Tambah Produk Baru</h3>
                <?php if($formError): ?><div class="error-msg"><?= sanitize($formError) ?></div><?php endif; ?>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                        <div><label style="font-size:0.9rem;font-weight:500;display:block;margin-bottom:0.25rem;">Nama Produk *</label><input type="text" name="product_name" class="form-input" required></div>
                        <div><label style="font-size:0.9rem;font-weight:500;display:block;margin-bottom:0.25rem;">Kategori</label>
                            <select name="category_id" class="form-input">
                                <option value="">Pilih Kategori</option>
                                <?php foreach($categories as $c): ?><option value="<?= $c['id'] ?>"><?= sanitize($c['name']) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div><label style="font-size:0.9rem;font-weight:500;display:block;margin-bottom:0.25rem;">Harga (Rp) *</label><input type="number" name="price" class="form-input" required></div>
                        <div><label style="font-size:0.9rem;font-weight:500;display:block;margin-bottom:0.25rem;">Harga Asli (coret)</label><input type="number" name="original_price" class="form-input"></div>
                        <div><label style="font-size:0.9rem;font-weight:500;display:block;margin-bottom:0.25rem;">Stok</label><input type="number" name="stock" class="form-input" value="0"></div>
                        <div><label style="font-size:0.9rem;font-weight:500;display:block;margin-bottom:0.25rem;">Berat/Satuan</label><input type="text" name="weight" class="form-input" placeholder="misal: 1 kg"></div>
                        <div><label style="font-size:0.9rem;font-weight:500;display:block;margin-bottom:0.25rem;">Foto Produk</label><input type="file" name="product_image" class="form-input" accept="image/*"></div>
                        <div><label style="font-size:0.9rem;font-weight:500;display:block;margin-bottom:0.25rem;">Atau URL Gambar</label><input type="url" name="image_url" class="form-input" placeholder="https://..."></div>
                        <div style="grid-column:span 2;"><label style="font-size:0.9rem;font-weight:500;display:block;margin-bottom:0.25rem;">Deskripsi</label><textarea name="description" class="form-input" rows="3"></textarea></div>
                        <div><label style="display:flex;align-items:center;gap:10px;cursor:pointer;margin-top:1rem;"><input type="checkbox" name="preorder" style="accent-color:var(--primary-color);"> Buka Pre-Order</label></div>
                        <div><label style="font-size:0.9rem;font-weight:500;display:block;margin-bottom:0.25rem;">Tanggal Panen (PO)</label><input type="date" name="preorder_date" class="form-input"></div>
                    </div>
                    <div style="margin-top:1.5rem;text-align:right;">
                        <button type="button" class="btn btn-outline" onclick="document.getElementById('addForm').style.display='none'">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Produk</button>
                    </div>
                </form>
            </div>

            <!-- Products Table -->
            <div class="chart-container">
                <table style="width:100%;border-collapse:collapse;text-align:left;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border-color);">
                            <th style="padding:1rem;">Produk</th>
                            <th style="padding:1rem;">Harga</th>
                            <th style="padding:1rem;">Stok</th>
                            <th style="padding:1rem;">Pre-Order</th>
                            <th style="padding:1rem;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(empty($products)): ?>
                        <tr><td colspan="5" style="padding:2rem;text-align:center;color:var(--text-muted);">Belum ada produk.</td></tr>
                    <?php endif; ?>
                    <?php foreach($products as $p): ?>
                        <tr style="border-bottom:1px solid var(--border-color);">
                            <td style="padding:1rem;display:flex;align-items:center;gap:10px;">
                                <img src="<?= getProductImage($p['image_url']??'') ?>" style="width:40px;height:40px;border-radius:4px;object-fit:cover;" onerror="this.src='https://via.placeholder.com/40'">
                                <div>
                                    <div style="font-weight:600;"><?= sanitize($p['name']) ?></div>
                                    <div style="font-size:0.8rem;color:var(--text-muted);"><?= sanitize($p['category_name']??'') ?></div>
                                </div>
                            </td>
                            <td style="padding:1rem;"><?= formatRupiah($p['price']) ?></td>
                            <td style="padding:1rem;"><?= $p['stock'] ?> <?= sanitize($p['weight']??'') ?></td>
                            <td style="padding:1rem;">
                                <?php if($p['preorder_available']): ?>
                                <span style="background:rgba(245,158,11,0.1);color:#b45309;padding:4px 8px;border-radius:4px;font-size:0.8rem;font-weight:600;">Aktif</span>
                                <?php else: ?>
                                <span style="font-size:0.8rem;color:var(--text-muted);">-</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:1rem;">
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus produk ini?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                    <button type="submit" class="btn btn-outline" style="padding:0.25rem 0.5rem;font-size:0.8rem;color:#ef4444;border-color:#ef4444;">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
