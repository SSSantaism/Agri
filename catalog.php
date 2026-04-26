<?php
require_once __DIR__ . '/includes/helpers.php';

$db = getDB();

// Handle search and filters
$search = trim($_GET['search'] ?? '');
$categoryFilter = (int) ($_GET['category'] ?? 0);
$locationFilter = trim($_GET['location'] ?? '');
$minPrice = (int) ($_GET['min_price'] ?? 0);
$maxPrice = (int) ($_GET['max_price'] ?? 0);
$minRating = (float) ($_GET['min_rating'] ?? 0);
$sortBy = trim($_GET['sort'] ?? 'terbaru');

// Build product query
$where = ["p.is_active = 1"];
$params = [];

if (!empty($search)) {
    $where[] = "(p.name LIKE ? OR p.description LIKE ? OR u.store_name LIKE ?)";
    $searchTerm = "%{$search}%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
}
if ($categoryFilter > 0) { $where[] = "p.category_id = ?"; $params[] = $categoryFilter; }
if (!empty($locationFilter)) { $where[] = "u.store_location LIKE ?"; $params[] = "%{$locationFilter}%"; }
if ($minPrice > 0) { $where[] = "p.price >= ?"; $params[] = $minPrice; }
if ($maxPrice > 0) { $where[] = "p.price <= ?"; $params[] = $maxPrice; }
if ($minRating > 0) { $where[] = "p.rating_avg >= ?"; $params[] = $minRating; }

$whereClause = implode(' AND ', $where);

// Sort
$orderBy = match($sortBy) {
    'termurah' => 'p.price ASC',
    'termahal' => 'p.price DESC',
    'terlaris' => 'p.sold_count DESC',
    'rating'   => 'p.rating_avg DESC',
    default    => 'p.created_at DESC',
};

$stmt = $db->prepare("
    SELECT p.*, u.name as farmer_name, u.store_name, u.store_location,
           c.name as category_name, c.icon as category_icon
    FROM products p JOIN users u ON p.seller_id = u.id
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE {$whereClause} ORDER BY {$orderBy} LIMIT 40
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
    <title>Katalog Produk - Panenly</title>
    <meta name="description" content="Jelajahi katalog produk pertanian segar langsung dari petani lokal. Sayuran, buah, beras, dan rempah berkualitas.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .catalog-header {
            padding: 2rem 5%;
            background: var(--white);
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
        }
        .catalog-header h1 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }
        .catalog-header p {
            color: var(--text-muted);
        }
        .sort-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .sort-options {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .sort-btn {
            padding: 0.4rem 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background: var(--white);
            color: var(--text-muted);
            font-size: 0.85rem;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
        }
        .sort-btn:hover, .sort-btn.active {
            border-color: var(--primary-color);
            color: var(--primary-color);
            background: rgba(16, 185, 129, 0.05);
        }
    </style>
</head>
<body>
    <?php $navbarType = 'full'; include __DIR__ . '/includes/navbar.php'; ?>
    <?= renderFlash() ?>

    <div class="catalog-header">
        <h1><?php if($search): ?>Hasil Pencarian: "<?= sanitize($search) ?>"<?php else: ?>Katalog Produk<?php endif; ?></h1>
        <p>Temukan hasil bumi segar langsung dari petani lokal terpercaya</p>
    </div>

    <div class="catalog-section">
        <aside class="sidebar-filter">
            <h3>Filter Produk</h3>
            <form action="" method="GET">
                <?php if ($search): ?><input type="hidden" name="search" value="<?= sanitize($search) ?>"><?php endif; ?>
                <div class="filter-group">
                    <h4>Lokasi</h4>
                    <select class="filter-select" name="location">
                        <option value="">Semua Lokasi</option>
                        <?php foreach($locations as $loc): ?>
                        <option value="<?= sanitize($loc['store_location']) ?>" <?= $locationFilter===$loc['store_location']?'selected':'' ?>><?= sanitize($loc['store_location']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <h4>Harga</h4>
                    <div class="price-range">
                        <input type="number" name="min_price" placeholder="Min" class="filter-input" value="<?= $minPrice?:''; ?>">
                        <span>-</span>
                        <input type="number" name="max_price" placeholder="Max" class="filter-input" value="<?= $maxPrice?:''; ?>">
                    </div>
                </div>
                <div class="filter-group">
                    <h4>Kategori</h4>
                    <?php foreach($categories as $cat): ?>
                    <label><input type="radio" name="category" value="<?= $cat['id'] ?>" <?= $categoryFilter==$cat['id']?'checked':'' ?>> <?= sanitize($cat['name']) ?></label>
                    <?php endforeach; ?>
                    <label><input type="radio" name="category" value="0" <?= $categoryFilter==0?'checked':'' ?>> Semua</label>
                </div>
                <div class="filter-group">
                    <h4>Rating Minimal</h4>
                    <select class="filter-select" name="min_rating">
                        <option value="0">Semua Bintang</option>
                        <option value="4" <?= $minRating>=4?'selected':'' ?>>4+ Bintang</option>
                        <option value="3" <?= $minRating>=3&&$minRating<4?'selected':'' ?>>3+ Bintang</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;margin-top:1rem;">Terapkan Filter</button>
                <?php if($categoryFilter||$locationFilter||$minPrice||$maxPrice||$minRating||$search): ?>
                <a href="<?= BASE_URL ?>/catalog.php" class="btn btn-outline" style="width:100%;margin-top:0.5rem;text-align:center;display:block;text-decoration:none;">Reset</a>
                <?php endif; ?>
            </form>
        </aside>

        <div class="catalog-content">
            <div class="sort-bar">
                <span style="color:var(--text-muted);font-size:0.9rem;"><?= count($products) ?> produk ditemukan</span>
                <div class="sort-options">
                    <span style="font-size:0.85rem; color:var(--text-muted); margin-right:0.25rem; align-self:center;">Urutkan:</span>
                    <?php
                    $currentParams = $_GET;
                    $sorts = ['terbaru'=>'Terbaru','terlaris'=>'Terlaris','termurah'=>'Termurah','termahal'=>'Termahal','rating'=>'Rating'];
                    foreach($sorts as $key => $label):
                        $currentParams['sort'] = $key;
                    ?>
                    <a href="?<?= http_build_query($currentParams) ?>" class="sort-btn <?= $sortBy===$key?'active':'' ?>"><?= $label ?></a>
                    <?php endforeach; ?>
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
                        <a href="product.php?id=<?= $prod['id'] ?>">
                            <img src="<?= getProductImage($prod['image_url']??'') ?>" alt="<?= sanitize($prod['name']) ?>" class="product-img" onerror="this.src='https://via.placeholder.com/400x300?text=Panenly'">
                        </a>
                    </div>
                    <div class="product-info">
                        <span class="product-weight"><?= sanitize($prod['weight']??'') ?></span>
                        <h3 class="product-title"><a href="product.php?id=<?= $prod['id'] ?>" style="color:inherit;text-decoration:none;"><?= sanitize($prod['name']) ?></a></h3>
                        <div class="product-rating" style="font-size:0.8rem;color:var(--text-muted);margin-bottom:0.25rem;">
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
</body>
</html>
