<?php
require_once __DIR__ . '/includes/helpers.php';

$db = getDB();

// Get some stats for the landing page
$productCount = $db->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn();
$sellerCount = $db->query("SELECT COUNT(*) FROM users WHERE role='seller' AND seller_status='approved'")->fetchColumn();
$categories = $db->query("SELECT * FROM categories ORDER BY id")->fetchAll();

// Get top products for preview (max 4)
$stmt = $db->query("
    SELECT p.*, u.store_name, u.store_location, c.name as category_name
    FROM products p JOIN users u ON p.seller_id = u.id
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.is_active = 1 ORDER BY p.sold_count DESC LIMIT 4
");
$topProducts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freshly - Dari Petani Langsung Ke Anda</title>
    <meta name="description" content="Beli hasil bumi segar langsung dari petani tanpa perantara. Kualitas terbaik, harga lebih murah, dan petani lebih sejahtera.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .stats-section { padding:3rem 5%; display:flex; justify-content:center; gap:4rem; margin-bottom:2rem; }
        .stat-item { text-align:center; }
        .stat-number { font-size:2.5rem; font-weight:800; color:var(--primary-color); }
        .stat-label { font-size:0.95rem; color:var(--text-muted); margin-top:0.25rem; }
        .features-section { padding:4rem 5%; background:var(--white); }
        .features-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:2.5rem; margin-top:2.5rem; }
        .feature-card { text-align:center; padding:2rem 1.5rem; border-radius:var(--radius); border:1px solid var(--border-color); transition:var(--transition); }
        .feature-card:hover { transform:translateY(-5px); box-shadow:var(--shadow-hover); border-color:var(--primary-color); }
        .feature-icon { width:60px; height:60px; border-radius:50%; background:rgba(16,185,129,0.1); color:var(--primary-color); display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin:0 auto 1.25rem; }
        .feature-title { font-size:1.1rem; font-weight:700; margin-bottom:0.5rem; }
        .feature-desc { font-size:0.9rem; color:var(--text-muted); line-height:1.6; }
        .preview-section { padding:4rem 5%; }
        .preview-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem; }
        .preview-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1.5rem; }
        .cta-section { padding:5rem 5%; background:linear-gradient(135deg, rgba(16,185,129,0.05), rgba(245,158,11,0.05)); text-align:center; }
        .cta-title { font-size:2rem; font-weight:800; margin-bottom:1rem; }
        .cta-subtitle { font-size:1.1rem; color:var(--text-muted); max-width:500px; margin:0 auto 2rem; }
        @media(max-width:768px) {
            .features-grid { grid-template-columns:1fr; }
            .preview-grid { grid-template-columns:repeat(2,1fr); }
            .stats-section { flex-wrap:wrap; gap:2rem; }
        }
    </style>
</head>
<body>
    <?php $navbarType = 'full'; include __DIR__ . '/includes/navbar.php'; ?>
    <?= renderFlash() ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-badge">🌿 100% Langsung dari Petani Lokal</div>
            <h1 class="hero-title">Panen Segar, <span>Harga Jujur</span> Langsung Ke Meja Anda</h1>
            <p class="hero-subtitle">Beli hasil bumi segar langsung dari petani tanpa perantara distributor. Kualitas terbaik, harga lebih murah, dan petani lebih sejahtera.</p>
            <a href="<?= BASE_URL ?>/catalog.php" class="btn btn-primary" style="padding:1rem 2rem;font-size:1.1rem;text-decoration:none;display:inline-block;">Mulai Belanja <i class="fa-solid fa-arrow-right" style="margin-left:8px;"></i></a>
        </div>
        <div class="hero-image-wrapper">
            <img src="assets/images/hero_image.png" alt="Sayuran Segar" class="hero-image" onerror="this.src='https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=800&auto=format&fit=crop'">
        </div>
    </section>

    <!-- Stats -->
    <div class="stats-section">
        <div class="stat-item">
            <div class="stat-number"><?= $productCount ?>+</div>
            <div class="stat-label">Produk Tersedia</div>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?= $sellerCount ?>+</div>
            <div class="stat-label">Petani Terdaftar</div>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?= count($categories) ?></div>
            <div class="stat-label">Kategori Produk</div>
        </div>
    </div>

    <!-- Features -->
    <div class="features-section">
        <div style="text-align:center;">
            <h2 style="font-size:1.8rem;font-weight:800;margin-bottom:0.5rem;">Kenapa Belanja di Freshly?</h2>
            <p style="color:var(--text-muted);max-width:500px;margin:0 auto;">Platform yang menghubungkan Anda langsung dengan petani lokal terpercaya.</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-leaf"></i></div>
                <div class="feature-title">100% Segar & Alami</div>
                <div class="feature-desc">Produk dipanen langsung dari kebun tanpa bahan pengawet. Segar dan organik sampai di rumah Anda.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                <div class="feature-title">Harga Lebih Murah</div>
                <div class="feature-desc">Tanpa perantara distributor berarti harga lebih terjangkau untuk Anda dan keuntungan lebih untuk petani.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-truck-fast"></i></div>
                <div class="feature-title">Pengiriman Cepat</div>
                <div class="feature-desc">Sistem pengiriman terorganisir memastikan produk sampai dalam kondisi terbaik ke tangan Anda.</div>
            </div>
        </div>
    </div>

    <!-- Product Preview -->
    <?php if(!empty($topProducts)): ?>
    <div class="preview-section">
        <div class="preview-header">
            <h2 style="font-size:1.5rem;font-weight:700;">Produk Populer</h2>
            <a href="<?= BASE_URL ?>/catalog.php" style="color:var(--primary-color);font-weight:600;text-decoration:none;">Lihat Semua <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <div class="preview-grid">
            <?php foreach($topProducts as $prod): ?>
            <div class="product-card">
                <div class="product-img-wrapper">
                    <?php if($prod['badge']): ?>
                    <div style="position:absolute;top:10px;right:10px;background:var(--secondary-color);color:white;padding:0.25rem 0.5rem;border-radius:4px;font-size:0.75rem;font-weight:600;z-index:10;"><?= sanitize($prod['badge']) ?></div>
                    <?php endif; ?>
                    <a href="product.php?id=<?= $prod['id'] ?>">
                        <img src="<?= getProductImage($prod['image_url']??'') ?>" alt="<?= sanitize($prod['name']) ?>" class="product-img" onerror="this.src='https://via.placeholder.com/400x300?text=Freshly'">
                    </a>
                </div>
                <div class="product-info">
                    <span class="product-weight"><?= sanitize($prod['weight']??'') ?></span>
                    <h3 class="product-title"><a href="product.php?id=<?= $prod['id'] ?>" style="color:inherit;text-decoration:none;"><?= sanitize($prod['name']) ?></a></h3>
                    <div style="font-size:0.8rem;color:var(--text-muted);margin-bottom:0.25rem;">
                        <i class="fa-solid fa-star" style="color:#f59e0b;"></i> <?= number_format($prod['rating_avg'],1) ?> | <?= $prod['sold_count'] ?> terjual
                    </div>
                    <span class="farmer-name"><i class="fa-solid fa-tractor"></i> <?= sanitize($prod['store_name']??'') ?></span>
                    <div class="product-price">
                        <?= formatRupiah($prod['price']) ?>
                        <?php if($prod['original_price']): ?><del><?= formatRupiah($prod['original_price']) ?></del><?php endif; ?>
                    </div>
                    <div class="product-actions">
                        <button class="btn-buy" onclick="window.location.href='product.php?id=<?= $prod['id'] ?>'">Lihat Produk</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- CTA -->
    <div class="cta-section">
        <h2 class="cta-title">Siap Belanja Produk Segar?</h2>
        <p class="cta-subtitle">Jelajahi katalog lengkap kami dan temukan produk terbaik langsung dari petani lokal.</p>
        <a href="<?= BASE_URL ?>/catalog.php" class="btn btn-primary" style="padding:1rem 2.5rem;font-size:1.1rem;text-decoration:none;display:inline-block;">Jelajahi Katalog <i class="fa-solid fa-arrow-right" style="margin-left:8px;"></i></a>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="assets/js/script.js"></script>
</body>
</html>
