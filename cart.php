<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - Panenly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .cart-section {
            padding: 2rem 0;
            display: flex;
            gap: 2rem;
            margin-bottom: 4rem;
        }
        .cart-items {
            flex: 2;
        }
        .cart-summary {
            flex: 1;
            background: var(--white);
            padding: 1.5rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            height: fit-content;
        }
        .cart-item {
            display: flex;
            gap: 1.5rem;
            background: var(--white);
            padding: 1.5rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-bottom: 1.5rem;
        }
        .cart-item img {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            object-fit: cover;
        }
        .item-details {
            flex: 1;
        }
        .item-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        .item-price {
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        .item-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            color: var(--text-muted);
        }
        .summary-total {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
            font-weight: 700;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="nav-brand">
            <i class="fa-solid fa-leaf"></i> Panenly
        </a>
    </nav>
    <div style="padding: 0 5%; max-width: 1200px; margin: 0 auto;">
        <h2 style="margin-top: 2rem;">Keranjang Belanja</h2>
        <div class="cart-section">
            <div class="cart-items">
                <div class="cart-item">
                    <img src="https://images.unsplash.com/photo-1568702846914-96b305d2aaeb?q=80&w=800&auto=format&fit=crop" alt="Apel Fuji">
                    <div class="item-details">
                        <div class="item-title">Apel Fuji Segar (Petik Langsung)</div>
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;"><i class="fa-solid fa-tractor"></i> Pak Tono</div>
                        <div class="item-price">Rp35.000</div>
                        <div class="item-actions">
                            <div style="display:flex; border:1px solid #e5e7eb; border-radius:6px; overflow:hidden;">
                                <button style="padding: 5px 10px; border:none; cursor:pointer;">-</button>
                                <input type="text" value="2" style="width:40px; text-align:center; border:none; outline:none; font-weight:600;">
                                <button style="padding: 5px 10px; border:none; cursor:pointer;">+</button>
                            </div>
                            <button style="border:none; background:transparent; color:#ef4444; cursor:pointer;"><i class="fa-solid fa-trash"></i> Hapus</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="cart-summary">
                <h3 style="margin-bottom: 1.5rem;">Ringkasan Belanja</h3>
                <div class="summary-row">
                    <span>Total Harga (2 barang)</span>
                    <span>Rp70.000</span>
                </div>
                <div class="summary-row">
                    <span>Diskon</span>
                    <span>-</span>
                </div>
                <div class="summary-total">
                    <span>Total Belanja</span>
                    <span>Rp70.000</span>
                </div>
                <button class="btn btn-primary" style="width:100%; margin-top:1.5rem;" onclick="window.location.href='checkout.php'">Beli (2)</button>
            </div>
        </div>
    </div>
</body>
</html>
