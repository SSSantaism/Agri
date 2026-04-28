<?php
require_once __DIR__ . '/includes/helpers.php';

$db = getDB();

// Handle search and filters
$search = trim($_GET['search'] ?? '');
$categoryFilters = $_GET['category'] ?? [];
if (!is_array($categoryFilters)) $categoryFilters = $categoryFilters ? [$categoryFilters] : [];
$categoryFilters = array_map('intval', $categoryFilters);
$categoryFilters = array_filter($categoryFilters, fn($v) => $v > 0);
$locationFilter = trim($_GET['location'] ?? '');
$minPrice = (int) ($_GET['min_price'] ?? 0);
$maxPrice = (int) ($_GET['max_price'] ?? 0);
$minRating = (float) ($_GET['min_rating'] ?? 0);

// Build product query
$where = ["p.is_active = 1"];
$params = [];

if (!empty($search)) {
    $where[] = "(p.name LIKE ? OR p.description LIKE ? OR u.store_name LIKE ?)";
    $searchTerm = "%{$search}%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
}
if (!empty($categoryFilters)) {
    $placeholders = implode(',', array_fill(0, count($categoryFilters), '?'));
    $where[] = "p.category_id IN ({$placeholders})";
    $params = array_merge($params, $categoryFilters);
}
if (!empty($locationFilter)) { $where[] = "u.store_location LIKE ?"; $params[] = "%{$locationFilter}%"; }
if ($minPrice > 0) { $where[] = "p.price >= ?"; $params[] = $minPrice; }
if ($maxPrice > 0) { $where[] = "p.price <= ?"; $params[] = $maxPrice; }
if ($minRating > 0) { $where[] = "p.rating_avg >= ?"; $params[] = $minRating; }

$whereClause = implode(' AND ', $where);
$stmt = $db->prepare("
    SELECT p.*, u.name as farmer_name, u.store_name, u.store_location,
           c.name as category_name, c.icon as category_icon
    FROM products p JOIN users u ON p.seller_id = u.id
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE {$whereClause} ORDER BY p.sold_count DESC LIMIT 20
");
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $db->query("SELECT * FROM categories ORDER BY id")->fetchAll();
$locations = $db->query("SELECT DISTINCT store_location FROM users WHERE role='seller' AND seller_status='approved' AND store_location IS NOT NULL ORDER BY store_location")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freshly - Dari Petani Langsung Ke Anda</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php $navbarType = 'full'; include __DIR__ . '/includes/navbar.php'; ?>
    <?= renderFlash() ?>

    <section class="hero">
        <div class="hero-content">
            <div class="hero-badge">🌿 100% Langsung dari Petani Lokal</div>
            <h1 class="hero-title">Panen Segar, <span>Harga Jujur</span> Langsung Ke Meja Anda</h1>
            <p class="hero-subtitle">Beli hasil bumi segar langsung dari petani tanpa perantara distributor. Kualitas terbaik, harga lebih murah, dan petani lebih sejahtera.</p>
            <a href="#katalog" class="btn btn-primary" style="padding:1rem 2rem;font-size:1.1rem;text-decoration:none;display:inline-block;">Belanja Sekarang <i class="fa-solid fa-arrow-right" style="margin-left:8px;"></i></a>
        </div>
        <div class="hero-image-wrapper">
            <img src="assets/images/hero_image.png" alt="Sayuran Segar" class="hero-image" onerror="this.src='https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=800&auto=format&fit=crop'">
        </div>
    </section>

    <div class="section-header"><h2 class="section-title">Kategori Pilihan</h2></div>
    <div class="categories">
        <?php foreach($categories as $cat): ?>
        <a href="<?= BASE_URL ?>/index.php?category[]=<?= $cat['id'] ?>#katalog" class="category-card" style="text-decoration:none;color:inherit;<?= in_array($cat['id'], $categoryFilters) ? 'border-color:var(--primary-color);' : '' ?>">
            <div class="category-icon"><?= $cat['icon'] ?></div>
            <div class="category-name"><?= sanitize($cat['name']) ?></div>
        </a>
        <?php endforeach; ?>
    </div>

    <div class="catalog-section" id="katalog">
        <aside class="sidebar-filter">
            <h3>Filter Produk</h3>
            <form action="" method="GET" id="filterForm">
                <?php if ($search): ?><input type="hidden" name="search" value="<?= sanitize($search) ?>"><?php endif; ?>
                <div class="filter-group">
                    <h4>Lokasi</h4>
                    <select class="filter-select auto-filter" name="location">
                        <option value="">Semua Lokasi</option>
                        <?php foreach($locations as $loc): ?>
                        <option value="<?= sanitize($loc['store_location']) ?>" <?= $locationFilter===$loc['store_location']?'selected':'' ?>><?= sanitize($loc['store_location']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <h4>Harga</h4>
                    <div class="price-range">
                        <input type="number" name="min_price" placeholder="Rp Min" class="filter-input auto-filter-delay" value="<?= $minPrice?:''; ?>">
                        <span>-</span>
                        <input type="number" name="max_price" placeholder="Rp Max" class="filter-input auto-filter-delay" value="<?= $maxPrice?:''; ?>">
                    </div>
                </div>
                <div class="filter-group">
                    <h4>Kategori</h4>
                    <?php foreach($categories as $cat): ?>
                    <label><input type="checkbox" name="category[]" value="<?= $cat['id'] ?>" class="auto-filter" <?= in_array($cat['id'], $categoryFilters)?'checked':'' ?>> <?= sanitize($cat['name']) ?></label>
                    <?php endforeach; ?>
                </div>
                <div class="filter-group">
                    <h4>Rating Minimal</h4>
                    <select class="filter-select auto-filter" name="min_rating">
                        <option value="0">Semua Bintang</option>
                        <option value="4" <?= $minRating>=4?'selected':'' ?>>4+ Bintang</option>
                        <option value="3" <?= $minRating>=3&&$minRating<4?'selected':'' ?>>3+ Bintang</option>
                    </select>
                </div>
            </form>
        </aside>

        <div class="catalog-content">
            <div class="section-header" style="padding:0;margin-bottom:1.5rem;">
                <h2 class="section-title"><?php if($search): ?>Hasil: "<?= sanitize($search) ?>"<?php else: ?>Katalog Produk<?php endif; ?></h2>
                <div style="display:flex;align-items:center;gap:1rem;">
                    <span style="color:var(--text-muted);font-size:0.9rem;"><?= count($products) ?> produk</span>
                    <a href="<?= BASE_URL ?>/catalog.php" style="color:var(--primary-color);font-weight:600;font-size:0.9rem;">Lihat Semua →</a>
                </div>
            </div>
            <?php if(empty($products)): ?>
            <div style="text-align:center;padding:4rem 2rem;color:var(--text-muted);">
                <i class="fa-solid fa-search" style="font-size:3rem;margin-bottom:1rem;display:block;opacity:0.3;"></i>
                <h3>Produk tidak ditemukan</h3><p>Coba ubah filter atau kata kunci.</p>
            </div>
            <?php else: ?>
            <div class="products">
                <?php foreach($products as $prod): ?>
                <div class="product-card">
                    <div class="product-img-wrapper">
                        <?php if($prod['badge']): ?>
                        <div class="product-badge" style="position:absolute;top:10px;right:10px;background:var(--secondary-color);color:white;padding:0.25rem 0.5rem;border-radius:4px;font-size:0.75rem;font-weight:600;z-index:10;"><?= sanitize($prod['badge']) ?></div>
                        <?php endif; ?>
                        <img src="<?= getProductImage($prod['image_url']??'') ?>" alt="<?= sanitize($prod['name']) ?>" class="product-img" onerror="this.src='https://via.placeholder.com/400x300?text=Freshly'">
                    </div>
                    <div class="product-info">
                        <span class="product-weight"><?= sanitize($prod['weight']??'') ?></span>
                        <h3 class="product-title"><?= sanitize($prod['name']) ?></h3>
                        <div style="font-size:0.8rem;color:var(--text-muted);margin-bottom:0.25rem;">
                            <i class="fa-solid fa-star" style="color:#f59e0b;"></i> <?= number_format($prod['rating_avg'],1) ?> | <?= $prod['sold_count'] ?> terjual
                        </div>
                        <span class="farmer-name" style="margin-bottom:0.25rem;"><i class="fa-solid fa-tractor"></i> <?= sanitize($prod['store_name']??$prod['farmer_name']) ?></span>
                        <div style="font-size:0.8rem;color:var(--text-muted);margin-bottom:0.5rem;">
                            <i class="fa-solid fa-location-dot" style="color:var(--primary-color);"></i> <?= sanitize($prod['store_location']??'') ?>
                        </div>
                        <div class="product-price">
                            <?= formatRupiah($prod['price']) ?>
                            <?php if($prod['original_price']): ?><del><?= formatRupiah($prod['original_price']) ?></del><?php endif; ?>
                        </div>
                        <div class="product-actions">
                            <button class="btn-add" title="Tambah ke Keranjang" data-product-id="<?= $prod['id'] ?>"><i class="fa-solid fa-cart-plus"></i></button>
                            <button class="btn-buy" onclick="window.location.href='product.php?id=<?= $prod['id'] ?>'">Beli Langsung</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="assets/js/script.js"></script>
    <script>
    // Auto-submit filter form on change
    (function() {
        const form = document.getElementById('filterForm');
        if (!form) return;
        let debounceTimer;
        // Instant submit for selects and checkboxes
        form.querySelectorAll('.auto-filter').forEach(el => {
            el.addEventListener('change', () => form.submit());
        });
        // Debounced submit for price inputs (wait 800ms after typing stops)
        form.querySelectorAll('.auto-filter-delay').forEach(el => {
            el.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => form.submit(), 800);
            });
        });
    })();
    </script>
</body>
</html>
