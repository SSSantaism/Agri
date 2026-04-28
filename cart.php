<?php
require_once __DIR__ . '/includes/helpers.php';
requireLogin();

$db = getDB();
$userId = $_SESSION['user_id'];

// Handle POST actions (update qty / remove)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $cartId = (int) ($_POST['cart_id'] ?? 0);
    if ($action === 'update' && $cartId > 0) {
        $qty = (int) ($_POST['quantity'] ?? 1);
        if ($qty <= 0) {
            $db->prepare("DELETE FROM cart_items WHERE id=? AND user_id=?")->execute([$cartId, $userId]);
        } else {
            $db->prepare("UPDATE cart_items SET quantity=? WHERE id=? AND user_id=?")->execute([$qty, $cartId, $userId]);
        }
    } elseif ($action === 'remove' && $cartId > 0) {
        $db->prepare("DELETE FROM cart_items WHERE id=? AND user_id=?")->execute([$cartId, $userId]);
    }
    header('Location: ' . BASE_URL . '/cart.php');
    exit;
}

// Get cart items
$stmt = $db->prepare("
    SELECT ci.*, p.name, p.price, p.image_url, p.weight, p.stock,
           u.store_name, u.name as farmer_name
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.id
    JOIN users u ON p.seller_id = u.id
    WHERE ci.user_id = ?
    ORDER BY ci.created_at DESC
");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll();

$subtotal = 0;
$totalItems = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
    $totalItems += $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - Freshly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .cart-section { padding:2rem 0; display:flex; gap:2rem; margin-bottom:4rem; }
        .cart-items { flex:2; }
        .cart-summary { flex:1; background:var(--white); padding:1.5rem; border-radius:var(--radius); box-shadow:var(--shadow); height:fit-content; position:sticky; top:100px; }
        .cart-item { display:flex; gap:1.5rem; background:var(--white); padding:1.5rem; border-radius:var(--radius); box-shadow:var(--shadow); margin-bottom:1.5rem; }
        .cart-item img { width:100px; height:100px; border-radius:8px; object-fit:cover; }
        .item-details { flex:1; }
        .item-title { font-size:1.1rem; font-weight:600; margin-bottom:0.25rem; }
        .item-price { font-weight:700; color:var(--primary-color); margin-bottom:1rem; }
        .item-actions { display:flex; justify-content:space-between; align-items:center; }
        .summary-row { display:flex; justify-content:space-between; margin-bottom:1rem; color:var(--text-muted); }
        .summary-total { display:flex; justify-content:space-between; margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border-color); font-weight:700; font-size:1.2rem; }
    </style>
</head>
<body>
    <?php $navbarType = 'simple'; include __DIR__ . '/includes/navbar.php'; ?>
    <div style="padding:0 5%;max-width:1200px;margin:0 auto;">
        <h2 style="margin-top:2rem;">Keranjang Belanja</h2>
        <?php if(empty($cartItems)): ?>
        <div style="text-align:center;padding:4rem 2rem;color:var(--text-muted);">
            <i class="fa-solid fa-cart-shopping" style="font-size:4rem;margin-bottom:1rem;display:block;opacity:0.3;"></i>
            <h3>Keranjang Anda kosong</h3>
            <p style="margin-bottom:1.5rem;">Yuk mulai belanja produk segar dari petani!</p>
            <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary" style="text-decoration:none;">Mulai Belanja</a>
        </div>
        <?php else: ?>
        <div class="cart-section">
            <div class="cart-items">
                <?php foreach($cartItems as $item): ?>
                <div class="cart-item">
                    <img src="<?= getProductImage($item['image_url']??'') ?>" alt="<?= sanitize($item['name']) ?>" onerror="this.src='https://via.placeholder.com/100'">
                    <div class="item-details">
                        <div class="item-title"><?= sanitize($item['name']) ?></div>
                        <div style="font-size:0.85rem;color:var(--text-muted);margin-bottom:0.5rem;"><i class="fa-solid fa-tractor"></i> <?= sanitize($item['store_name']??$item['farmer_name']) ?></div>
                        <div class="item-price"><?= formatRupiah($item['price']) ?> <span style="font-weight:400;color:var(--text-muted);font-size:0.85rem;">x <?= $item['quantity'] ?> = <?= formatRupiah($item['price']*$item['quantity']) ?></span></div>
                        <div class="item-actions">
                            <form method="POST" style="display:flex;border:1px solid #e5e7eb;border-radius:6px;overflow:hidden;">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                <button type="submit" name="quantity" value="<?= max(1,$item['quantity']-1) ?>" style="padding:5px 10px;border:none;cursor:pointer;background:var(--bg-color);">-</button>
                                <span style="padding:5px 12px;font-weight:600;display:flex;align-items:center;"><?= $item['quantity'] ?></span>
                                <button type="submit" name="quantity" value="<?= min($item['stock'],$item['quantity']+1) ?>" style="padding:5px 10px;border:none;cursor:pointer;background:var(--bg-color);">+</button>
                            </form>
                            <form method="POST">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                <button type="submit" style="border:none;background:transparent;color:#ef4444;cursor:pointer;"><i class="fa-solid fa-trash"></i> Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="cart-summary">
                <h3 style="margin-bottom:1.5rem;">Ringkasan Belanja</h3>
                <div class="summary-row"><span>Total Harga (<?= $totalItems ?> barang)</span><span><?= formatRupiah($subtotal) ?></span></div>
                <div class="summary-row"><span>Diskon</span><span>-</span></div>
                <div class="summary-total"><span>Total Belanja</span><span><?= formatRupiah($subtotal) ?></span></div>
                <a href="<?= BASE_URL ?>/checkout.php" class="btn btn-primary" style="width:100%;margin-top:1.5rem;text-align:center;display:block;text-decoration:none;">Beli (<?= $totalItems ?>)</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
