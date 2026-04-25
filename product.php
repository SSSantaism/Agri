<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk - Panenly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .detail-section {
            padding: 2rem 5%;
            margin-top: 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-bottom: 4rem;
        }
        .product-gallery img {
            width: 100%;
            border-radius: var(--radius);
            object-fit: cover;
            aspect-ratio: 4/3;
        }
        .detail-title {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 0.5rem;
        }
        .detail-meta {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
            color: var(--text-muted);
            font-size: 0.95rem;
        }
        .detail-price {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }
        .specs-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .spec-item {
            background: var(--bg-color);
            padding: 1rem;
            border-radius: 8px;
        }
        .spec-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }
        .spec-value {
            font-weight: 600;
            color: var(--text-main);
        }
        .detail-actions {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .qty-selector {
            display: flex;
            align-items: center;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
        }
        .qty-btn {
            background: var(--bg-color);
            border: none;
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: var(--transition);
        }
        .qty-btn:hover {
            background: var(--border-color);
        }
        .qty-input {
            width: 50px;
            text-align: center;
            border: none;
            outline: none;
            font-weight: 600;
        }
        .preorder-card {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid var(--secondary-color);
            padding: 1.5rem;
            border-radius: var(--radius);
            margin-bottom: 2rem;
        }
        .reviews-section {
            margin-top: 4rem;
        }
        .review-card {
            border-bottom: 1px solid var(--border-color);
            padding: 1.5rem 0;
        }
        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <!-- Navbar (Mock) -->
    <nav class="navbar">
        <a href="index.php" class="nav-brand">
            <i class="fa-solid fa-leaf"></i> Panenly
        </a>
        <div class="nav-actions">
            <div class="icon-action cart-icon" onclick="window.location.href='cart.php'">
                <i class="fa-solid fa-cart-shopping fa-lg"></i>
                <span class="icon-badge cart-badge">2</span>
            </div>
            <div class="user-profile-mock" style="display:flex; align-items:center; gap:10px; cursor:pointer;" onclick="window.location.href='buyer/profile.php'">
                <img src="https://ui-avatars.com/api/?name=Buyer&background=10b981&color=fff" style="width:40px; border-radius:50%;" alt="Profile">
            </div>
        </div>
    </nav>

    <div style="padding: 0 5%; max-width: 1200px; margin: 0 auto;">
        <div class="detail-section">
            <!-- Product Gallery -->
            <div class="product-gallery">
                <img src="https://images.unsplash.com/photo-1568702846914-96b305d2aaeb?q=80&w=800&auto=format&fit=crop" alt="Apel Fuji">
            </div>
            
            <!-- Product Info -->
            <div class="product-info-detail">
                <div style="display: inline-block; padding: 0.25rem 0.75rem; background: rgba(16, 185, 129, 0.1); color: var(--primary-dark); border-radius: 4px; font-size: 0.8rem; font-weight: 600; margin-bottom: 1rem;">Buah-buahan Segar</div>
                <h1 class="detail-title">Apel Fuji Segar (Petik Langsung)</h1>
                
                <div class="detail-meta">
                    <span><i class="fa-solid fa-star" style="color: #f59e0b;"></i> 4.9 (120 Ulasan)</span>
                    <span><i class="fa-solid fa-basket-shopping"></i> 210 Terjual</span>
                    <span><i class="fa-solid fa-location-dot"></i> Batu, Malang</span>
                </div>
                
                <div class="detail-price">Rp35.000 <span style="font-size: 1rem; color: var(--text-muted); font-weight: 400;">/ 1 kg</span></div>
                
                <div class="specs-grid">
                    <div class="spec-item">
                        <div class="spec-label">Stok Tersedia</div>
                        <div class="spec-value">45 kg</div>
                    </div>
                    <div class="spec-item">
                        <div class="spec-label">Kualitas</div>
                        <div class="spec-value">Grade A (Premium)</div>
                    </div>
                    <div class="spec-item">
                        <div class="spec-label">Metode Panen</div>
                        <div class="spec-value">Petik Tangan (Hand-picked)</div>
                    </div>
                    <div class="spec-item">
                        <div class="spec-label">Petani / Mitra</div>
                        <div class="spec-value"><i class="fa-solid fa-tractor" style="color: var(--primary-color);"></i> Pak Tono</div>
                    </div>
                </div>

                <div class="detail-actions">
                    <div class="qty-selector">
                        <button class="qty-btn">-</button>
                        <input type="text" value="1" class="qty-input">
                        <button class="qty-btn">+</button>
                    </div>
                    <button class="btn btn-outline" style="flex: 1;" onclick="window.location.href='cart.php'"><i class="fa-solid fa-cart-plus"></i> Masukkan Keranjang</button>
                    <button class="btn btn-primary" style="flex: 1;" onclick="window.location.href='checkout.php'">Beli Langsung</button>
                </div>

                <!-- Pre Order Section -->
                <div class="preorder-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <h4 style="color: #b45309; margin:0;"><i class="fa-solid fa-calendar-days"></i> Sistem Pre-Order Tersedia</h4>
                        <span style="font-size: 0.8rem; background: #b45309; color: white; padding: 2px 6px; border-radius: 4px;">Panen Berikutnya: 15 Mei 2026</span>
                    </div>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 1rem;">Ingin mengamankan stok untuk panen minggu depan? Lakukan Pre-Order sekarang dengan harga yang sama.</p>
                    <button class="btn btn-outline" style="border-color: #b45309; color: #b45309; width: 100%;">Ikut Pre-Order</button>
                </div>

                <!-- Description -->
                <div>
                    <h3 style="margin-bottom: 1rem;">Deskripsi Produk</h3>
                    <p style="color: var(--text-muted); line-height: 1.8; font-size: 0.95rem;">
                        Apel Fuji langsung dipetik dari kebun kami di dataran tinggi Batu, Malang. Memiliki tekstur yang renyah dengan tingkat kemanisan yang tinggi dan kandungan air yang melimpah. Bebas dari pestisida berlebih dan aman dikonsumsi langsung setelah dicuci.
                    </p>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="reviews-section">
            <h2 style="margin-bottom: 2rem;">Ulasan Pembeli (120)</h2>
            <div class="review-card">
                <div class="review-header">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <img src="https://ui-avatars.com/api/?name=Budi&background=e5e7eb" style="width:40px; border-radius:50%;" alt="User">
                        <div>
                            <div style="font-weight: 600; font-size: 0.95rem;">Budi Santoso</div>
                            <div style="color: #f59e0b; font-size: 0.8rem;"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                        </div>
                    </div>
                    <div style="font-size: 0.85rem; color: var(--text-muted);">2 Hari yang lalu</div>
                </div>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.5rem;">Buahnya sangat segar dan manis. Pengirimannya juga cepat dan aman. Mantap Pak Tono!</p>
            </div>
            <div class="review-card">
                <div class="review-header">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <img src="https://ui-avatars.com/api/?name=Siti&background=e5e7eb" style="width:40px; border-radius:50%;" alt="User">
                        <div>
                            <div style="font-weight: 600; font-size: 0.95rem;">Siti Aminah</div>
                            <div style="color: #f59e0b; font-size: 0.8rem;"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                        </div>
                    </div>
                    <div style="font-size: 0.85rem; color: var(--text-muted);">1 Minggu yang lalu</div>
                </div>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.5rem;">Suka banget belanja disini, harganya jauh lebih murah dari supermarket dan kualitasnya grade A beneran.</p>
            </div>
        </div>
    </div>
</body>
</html>
