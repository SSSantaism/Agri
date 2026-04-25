<?php
// Mockup dynamic products data for Scalability
// In the future, this can be fetched from a database using PDO or MySQLi.
$products = [
    [
        "id" => 1,
        "name" => "Tomat Cherry Organik Segar",
        "weight" => "500 gram",
        "price" => 15000,
        "original_price" => 20000,
        "farmer" => "Pak Budi",
        "location" => "Lembang, Bandung",
        "rating" => 4.8,
        "sold" => 120,
        "image" => "assets/images/product_tomato.png",
        "badge" => "Diskon 25%"
    ],
    [
        "id" => 2,
        "name" => "Wortel Manis Berastagi",
        "weight" => "1 kg",
        "price" => 12000,
        "original_price" => null,
        "farmer" => "Bu Siti",
        "location" => "Berastagi, Karo",
        "rating" => 4.9,
        "sold" => 340,
        "image" => "assets/images/product_carrot.png",
        "badge" => "Terlaris"
    ],
    [
        "id" => 3,
        "name" => "Bayam Hijau Hidroponik",
        "weight" => "250 gram",
        "price" => 8000,
        "original_price" => 10000,
        "farmer" => "Kang Dadan",
        "location" => "Cisarua, Bogor",
        "rating" => 4.7,
        "sold" => 85,
        "image" => "assets/images/product_spinach.png",
        "badge" => null
    ],
    [
        "id" => 4,
        "name" => "Apel Fuji Segar (Petik Langsung)",
        "weight" => "1 kg",
        "price" => 35000,
        "original_price" => 45000,
        "farmer" => "Pak Tono",
        "location" => "Batu, Malang",
        "rating" => 4.9,
        "sold" => 210,
        "image" => "https://images.unsplash.com/photo-1568702846914-96b305d2aaeb?q=80&w=600&auto=format&fit=crop",
        "badge" => null
    ],
    [
        "id" => 5,
        "name" => "Beras Merah Organik Premium",
        "weight" => "2 kg",
        "price" => 45000,
        "original_price" => 50000,
        "farmer" => "Koperasi Tani Makmur",
        "location" => "Cianjur, Jabar",
        "rating" => 4.6,
        "sold" => 500,
        "image" => "https://images.unsplash.com/photo-1586201375761-83865001e8ac?q=80&w=600&auto=format&fit=crop",
        "badge" => null
    ],
];

// Categories array
$categories = [
    ["name" => "Sayuran", "icon" => "🥬"],
    ["name" => "Buah-buahan", "icon" => "🍎"],
    ["name" => "Beras & Biji", "icon" => "🌾"],
    ["name" => "Rempah", "icon" => "🧄"],
    ["name" => "Daging", "icon" => "🥩"],
    ["name" => "Telur & Susu", "icon" => "🥚"]
];

function formatRupiah($angka) {
    return "Rp" . number_format($angka, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panenly - Dari Petani Langsung Ke Anda</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <a href="/" class="nav-brand">
            <i class="fa-solid fa-leaf"></i> Panenly
        </a>
        
        <div class="search-container">
            <i class="fa-solid fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Cari sayur segar, buah, atau beras...">
        </div>

        <div class="nav-actions">
            <div class="icon-action">
                <i class="fa-solid fa-bell fa-lg"></i>
                <span class="icon-badge">3</span>
            </div>
            <div class="icon-action">
                <i class="fa-solid fa-envelope fa-lg"></i>
                <span class="icon-badge">1</span>
            </div>
            <div class="icon-action cart-icon">
                <i class="fa-solid fa-cart-shopping fa-lg"></i>
                <span class="icon-badge cart-badge">2</span>
            </div>
            <button class="btn btn-outline">Masuk</button>
            <button class="btn btn-primary">Daftar</button>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-badge">🌿 100% Langsung dari Petani Lokal</div>
            <h1 class="hero-title">Panen Segar, <span>Harga Jujur</span> Langsung Ke Meja Anda</h1>
            <p class="hero-subtitle">Beli hasil bumi segar langsung dari petani tanpa perantara distributor. Kualitas terbaik, harga lebih murah, dan petani lebih sejahtera.</p>
            <button class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1.1rem;">Belanja Sekarang <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i></button>
        </div>
        <div class="hero-image-wrapper">
            <img src="assets/images/hero_image.png" alt="Sayuran Segar" class="hero-image">
        </div>
    </section>

    <!-- Categories -->
    <div class="section-header">
        <h2 class="section-title">Kategori Pilihan</h2>
    </div>
    <div class="categories">
        <?php foreach($categories as $cat): ?>
        <div class="category-card">
            <div class="category-icon"><?php echo $cat['icon']; ?></div>
            <div class="category-name"><?php echo $cat['name']; ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Catalog Section with Sidebar -->
    <div class="catalog-section">
        <!-- Sidebar Filter -->
        <aside class="sidebar-filter">
            <h3>Filter Produk</h3>
            <div class="filter-group">
                <h4>Lokasi</h4>
                <select class="filter-select">
                    <option>Semua Lokasi</option>
                    <option>Bandung</option>
                    <option>Bogor</option>
                    <option>Malang</option>
                    <option>Karo</option>
                </select>
            </div>
            <div class="filter-group">
                <h4>Harga</h4>
                <div class="price-range">
                    <input type="number" placeholder="Min" class="filter-input">
                    <span>-</span>
                    <input type="number" placeholder="Max" class="filter-input">
                </div>
            </div>
            <div class="filter-group">
                <h4>Kategori Komoditas</h4>
                <label><input type="checkbox"> Sayuran</label>
                <label><input type="checkbox"> Buah-buahan</label>
                <label><input type="checkbox"> Beras & Biji</label>
            </div>
            <div class="filter-group">
                <h4>Rating Minimal</h4>
                <select class="filter-select">
                    <option>Semua Bintang</option>
                    <option>4 Bintang ke atas</option>
                    <option>3 Bintang ke atas</option>
                </select>
            </div>
            <button class="btn btn-primary" style="width:100%; margin-top: 1rem;">Terapkan Filter</button>
        </aside>

        <!-- Products List -->
        <div class="catalog-content">
            <div class="section-header" style="padding: 0; margin-bottom: 1.5rem;">
                <h2 class="section-title">Katalog Produk</h2>
            </div>
            <div class="products">
                <?php foreach($products as $prod): ?>
                <div class="product-card">
                    <div class="product-img-wrapper">
                        <?php if($prod['badge']): ?>
                            <div class="product-badge" style="position: absolute; top: 10px; right: 10px; background: var(--secondary-color); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; z-index: 10;"><?php echo htmlspecialchars($prod['badge']); ?></div>
                        <?php endif; ?>
                        <img src="<?php echo htmlspecialchars($prod['image']); ?>" alt="<?php echo htmlspecialchars($prod['name']); ?>" class="product-img" onerror="this.src='https://via.placeholder.com/400x300?text=Panenly'">
                    </div>
                    <div class="product-info">
                        <span class="product-weight"><?php echo htmlspecialchars($prod['weight']); ?></span>
                        <h3 class="product-title"><?php echo htmlspecialchars($prod['name']); ?></h3>
                        <div class="product-meta" style="flex-wrap: wrap; margin-bottom: 0.25rem;">
                            <span class="farmer-name"><i class="fa-solid fa-tractor"></i> <?php echo htmlspecialchars($prod['farmer']); ?></span>
                            <span class="product-rating" style="font-size: 0.8rem; color: var(--text-muted);"><i class="fa-solid fa-star" style="color: #f59e0b;"></i> <?php echo htmlspecialchars($prod['rating']); ?> | <?php echo htmlspecialchars($prod['sold']); ?> terjual</span>
                        </div>
                        <div class="product-location" style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                            <i class="fa-solid fa-location-dot" style="color: var(--primary-color);"></i> <?php echo htmlspecialchars($prod['location']); ?>
                        </div>
                        
                        <div class="product-price">
                            <?php echo formatRupiah($prod['price']); ?>
                            <?php if($prod['original_price']): ?>
                                <del><?php echo formatRupiah($prod['original_price']); ?></del>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-actions">
                            <button class="btn-add" title="Tambah ke Keranjang"><i class="fa-solid fa-cart-plus"></i></button>
                            <button class="btn-buy" onclick="window.location.href='product.php?id=<?php echo $prod['id']; ?>'">Beli Langsung</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-grid">
            <div>
                <div class="footer-brand"><i class="fa-solid fa-leaf"></i> Panenly</div>
                <p class="footer-desc">Menjembatani petani lokal langsung ke tangan Anda. Sayuran lebih segar, harga lebih jujur.</p>
            </div>
            <div>
                <h4 class="footer-title">Panenly</h4>
                <ul class="footer-links">
                    <li><a href="#">Tentang Kami</a></li>
                    <li><a href="#">Mitra Petani</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Karir</a></li>
                </ul>
            </div>
            <div>
                <h4 class="footer-title">Bantuan</h4>
                <ul class="footer-links">
                    <li><a href="#">Cara Belanja</a></li>
                    <li><a href="#">Pengiriman</a></li>
                    <li><a href="#">Pengembalian Dana</a></li>
                    <li><a href="#">Hubungi Kami</a></li>
                </ul>
            </div>
            <div>
                <h4 class="footer-title">Unduh Aplikasi</h4>
                <p class="footer-desc" style="margin-bottom: 1rem;">Segera hadir di App Store dan Google Play</p>
                <div style="display:flex; gap: 10px; font-size: 1.5rem; color: #9ca3af;">
                    <i class="fa-brands fa-instagram" style="cursor:pointer"></i>
                    <i class="fa-brands fa-facebook" style="cursor:pointer"></i>
                    <i class="fa-brands fa-twitter" style="cursor:pointer"></i>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; <?php echo date("Y"); ?> Panenly. All rights reserved.
        </div>
    </footer>

    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
</body>
</html>
