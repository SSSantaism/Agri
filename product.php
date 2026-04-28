<?php
require_once __DIR__ . '/includes/helpers.php';

$db = getDB();
$productId = (int) ($_GET['id'] ?? 0);

if ($productId <= 0) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$stmt = $db->prepare("
    SELECT p.*, u.name as farmer_name, u.store_name, u.store_location, u.id as seller_user_id,
           c.name as category_name
    FROM products p JOIN users u ON p.seller_id = u.id
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.id = ? AND p.is_active = 1
");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    setFlash('error', 'Produk tidak ditemukan.');
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

// Get reviews
$stmt = $db->prepare("
    SELECT r.*, u.name as reviewer_name FROM reviews r
    JOIN users u ON r.user_id = u.id WHERE r.product_id = ?
    ORDER BY r.created_at DESC LIMIT 10
");
$stmt->execute([$productId]);
$reviews = $stmt->fetchAll();

$reviewCount = $db->prepare("SELECT COUNT(*) FROM reviews WHERE product_id = ?");
$reviewCount->execute([$productId]);
$totalReviews = (int) $reviewCount->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($product['name']) ?> - Freshly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .detail-section { padding:2rem 5%; margin-top:2rem; display:grid; grid-template-columns:1fr 1fr; gap:4rem; background:var(--white); border-radius:var(--radius); box-shadow:var(--shadow); margin-bottom:4rem; }
        .product-gallery img { width:100%; border-radius:var(--radius); object-fit:cover; aspect-ratio:4/3; }
        .detail-title { font-size:2rem; font-weight:800; color:var(--text-main); margin-bottom:0.5rem; }
        .detail-meta { display:flex; align-items:center; gap:1.5rem; margin-bottom:1.5rem; color:var(--text-muted); font-size:0.95rem; }
        .detail-price { font-size:2rem; font-weight:800; color:var(--primary-color); margin-bottom:2rem; padding-bottom:1.5rem; border-bottom:1px solid var(--border-color); }
        .specs-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:2rem; }
        .spec-item { background:var(--bg-color); padding:1rem; border-radius:8px; }
        .spec-label { font-size:0.85rem; color:var(--text-muted); margin-bottom:0.25rem; }
        .spec-value { font-weight:600; color:var(--text-main); }
        .detail-actions { display:flex; gap:1rem; margin-bottom:2rem; }
        .qty-selector { display:flex; align-items:center; border:1px solid var(--border-color); border-radius:8px; overflow:hidden; }
        .qty-btn { background:var(--bg-color); border:none; padding:0.75rem 1rem; cursor:pointer; transition:var(--transition); }
        .qty-btn:hover { background:var(--border-color); }
        .qty-input { width:50px; text-align:center; border:none; outline:none; font-weight:600; }
        .preorder-card { background:rgba(245,158,11,0.1); border:1px solid var(--secondary-color); padding:1.5rem; border-radius:var(--radius); margin-bottom:2rem; }
        .review-card { border-bottom:1px solid var(--border-color); padding:1.5rem 0; }
        .review-header { display:flex; justify-content:space-between; margin-bottom:0.5rem; }
        @media(max-width:768px) { .detail-section { grid-template-columns:1fr; gap:2rem; } }
    </style>
</head>
<body>
    <?php $navbarType = 'full'; include __DIR__ . '/includes/navbar.php'; ?>

    <div style="padding:0 5%;max-width:1200px;margin:0 auto;">
        <div class="detail-section">
            <div class="product-gallery">
                <img src="<?= getProductImage($product['image_url']??'') ?>" alt="<?= sanitize($product['name']) ?>" onerror="this.src='https://via.placeholder.com/800x600?text=Freshly'">
            </div>
            <div>
                <?php if($product['category_name']): ?>
                <div style="display:inline-block;padding:0.25rem 0.75rem;background:rgba(16,185,129,0.1);color:var(--primary-dark);border-radius:4px;font-size:0.8rem;font-weight:600;margin-bottom:1rem;"><?= sanitize($product['category_name']) ?></div>
                <?php endif; ?>
                <h1 class="detail-title"><?= sanitize($product['name']) ?></h1>
                <div class="detail-meta">
                    <span><i class="fa-solid fa-star" style="color:#f59e0b;"></i> <?= number_format($product['rating_avg'],1) ?> (<?= $totalReviews ?> Ulasan)</span>
                    <span><i class="fa-solid fa-basket-shopping"></i> <?= $product['sold_count'] ?> Terjual</span>
                    <span><i class="fa-solid fa-location-dot"></i> <?= sanitize($product['store_location']??'') ?></span>
                </div>
                <div class="detail-price">
                    <?= formatRupiah($product['price']) ?>
                    <?php if($product['original_price']): ?> <del style="font-size:1rem;color:var(--text-muted);font-weight:400;"><?= formatRupiah($product['original_price']) ?></del><?php endif; ?>
                    <span style="font-size:1rem;color:var(--text-muted);font-weight:400;">/ <?= sanitize($product['weight']??'') ?></span>
                </div>
                <div class="specs-grid">
                    <div class="spec-item"><div class="spec-label">Stok Tersedia</div><div class="spec-value"><?= $product['stock'] ?> <?= sanitize($product['weight']??'') ?></div></div>
                    <div class="spec-item"><div class="spec-label">Kualitas</div><div class="spec-value"><?= sanitize($product['quality']??'Standar') ?></div></div>
                    <div class="spec-item"><div class="spec-label">Metode Panen</div><div class="spec-value"><?= sanitize($product['harvest_method']??'-') ?></div></div>
                    <div class="spec-item"><div class="spec-label">Petani / Mitra</div><div class="spec-value"><i class="fa-solid fa-tractor" style="color:var(--primary-color);"></i> <?= sanitize($product['store_name']??$product['farmer_name']) ?></div></div>
                </div>

                <form id="addToCartForm">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <div class="detail-actions">
                        <div class="qty-selector">
                            <button type="button" class="qty-btn" onclick="changeQty(-1)">-</button>
                            <input type="text" value="1" class="qty-input" id="qtyInput" name="quantity" readonly>
                            <button type="button" class="qty-btn" onclick="changeQty(1)">+</button>
                        </div>
                        <button type="button" class="btn btn-outline" style="flex:1;" onclick="addToCart()"><i class="fa-solid fa-cart-plus"></i> Masukkan Keranjang</button>
                        <button type="button" class="btn btn-primary" style="flex:1;" onclick="buyNow()">Beli Langsung</button>
                    </div>
                </form>
                <a href="<?= BASE_URL ?>/chat.php?partner=<?= $product['seller_user_id'] ?>&product_id=<?= $product['id'] ?>" class="btn btn-outline" style="width:100%;text-align:center;display:block;margin-bottom:2rem;border-color:var(--primary-color);color:var(--primary-color);">
                    <i class="fa-solid fa-message"></i> Chat Penjual
                </a>

                <?php if($product['preorder_available'] && $product['preorder_date']): ?>
                <div class="preorder-card">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;">
                        <h4 style="color:#b45309;margin:0;"><i class="fa-solid fa-calendar-days"></i> Sistem Pre-Order</h4>
                        <span style="font-size:0.8rem;background:#b45309;color:white;padding:2px 6px;border-radius:4px;">Panen: <?= date('d M Y', strtotime($product['preorder_date'])) ?></span>
                    </div>
                    <p style="font-size:0.9rem;color:var(--text-muted);margin-bottom:1rem;">Amankan stok untuk panen berikutnya dengan Pre-Order sekarang.</p>
                    <button class="btn btn-outline" style="border-color:#b45309;color:#b45309;width:100%;">Ikut Pre-Order</button>
                </div>
                <?php endif; ?>

                <div>
                    <h3 style="margin-bottom:1rem;">Deskripsi Produk</h3>
                    <p style="color:var(--text-muted);line-height:1.8;font-size:0.95rem;"><?= nl2br(sanitize($product['description']??'')) ?></p>
                </div>
            </div>
        </div>

        <div style="margin-top:4rem;margin-bottom:4rem;">
            <h2 style="margin-bottom:2rem;">Ulasan Pembeli (<?= $totalReviews ?>)</h2>
            <?php if(empty($reviews)): ?>
            <p style="color:var(--text-muted);">Belum ada ulasan untuk produk ini.</p>
            <?php endif; ?>
            <?php foreach($reviews as $review): ?>
            <div class="review-card">
                <div class="review-header">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <img src="<?= getAvatarUrl($review['reviewer_name']) ?>" style="width:40px;border-radius:50%;" alt="User">
                        <div>
                            <div style="font-weight:600;font-size:0.95rem;"><?= sanitize($review['reviewer_name']) ?></div>
                            <div style="color:#f59e0b;font-size:0.8rem;"><?php for($i=0;$i<$review['rating'];$i++): ?><i class="fa-solid fa-star"></i><?php endfor; ?></div>
                        </div>
                    </div>
                    <div style="font-size:0.85rem;color:var(--text-muted);"><?= timeAgo($review['created_at']) ?></div>
                </div>
                <p style="color:var(--text-muted);font-size:0.95rem;margin-top:0.5rem;"><?= sanitize($review['comment']??'') ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
    function changeQty(delta) {
        const input = document.getElementById('qtyInput');
        let val = parseInt(input.value) + delta;
        if (val < 1) val = 1;
        if (val > <?= $product['stock'] ?>) val = <?= $product['stock'] ?>;
        input.value = val;
    }
    function addToCart() {
        const qty = document.getElementById('qtyInput').value;
        fetch('<?= BASE_URL ?>/api/cart.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=add&product_id=<?= $product['id'] ?>&quantity=' + qty
        }).then(r => r.json()).then(data => {
            if (data.redirect) { window.location.href = data.redirect; return; }
            alert(data.message);
            if (data.success) {
                const badge = document.querySelector('.cart-badge');
                if (badge) badge.innerText = data.cart_count;
            }
        });
    }
    function buyNow() {
        const qty = document.getElementById('qtyInput').value;
        fetch('<?= BASE_URL ?>/api/cart.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=add&product_id=<?= $product['id'] ?>&quantity=' + qty
        }).then(r => r.json()).then(data => {
            if (data.redirect) { window.location.href = data.redirect; return; }
            if (data.success) window.location.href = '<?= BASE_URL ?>/checkout.php';
            else alert(data.message);
        });
    }
    </script>
</body>
</html>
