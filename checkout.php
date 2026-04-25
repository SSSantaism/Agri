<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Panenly</title>
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
        .cart-items { flex: 2; }
        .cart-summary {
            flex: 1;
            background: var(--white);
            padding: 1.5rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            height: fit-content;
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
        .payment-method {
            border: 1px solid var(--border-color);
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            font-weight: 600;
            color: var(--text-muted);
            transition: var(--transition);
        }
        .payment-method.active {
            border-color: var(--primary-color);
            background: rgba(16, 185, 129, 0.05);
            color: var(--primary-color);
        }
        .payment-method i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
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
        <h2 style="margin-top: 2rem;">Checkout</h2>
        <div class="cart-section">
            <div class="cart-items">
                <div style="background:var(--white); padding:1.5rem; border-radius:var(--radius); box-shadow:var(--shadow); margin-bottom:1.5rem;">
                    <h3 style="margin-bottom:1rem;">Alamat Pengiriman</h3>
                    <div style="font-weight:600;">Budi Santoso (+62 812-3456-7890)</div>
                    <div style="color:var(--text-muted); font-size:0.9rem; margin-top:0.5rem;">Jl. Merdeka No. 123, Kecamatan Sukajadi, Kota Bandung, Jawa Barat 40161</div>
                    <button class="btn btn-outline" style="margin-top:1rem; padding:0.5rem 1rem; font-size:0.85rem;">Ubah Alamat</button>
                </div>
                
                <div style="background:var(--white); padding:1.5rem; border-radius:var(--radius); box-shadow:var(--shadow); margin-bottom:1.5rem;">
                    <h3 style="margin-bottom:1rem;">Barang yang Dibeli</h3>
                    <div style="display:flex; gap:1rem; align-items:center;">
                        <img src="https://images.unsplash.com/photo-1568702846914-96b305d2aaeb?q=80&w=800&auto=format&fit=crop" style="width:60px; height:60px; border-radius:8px; object-fit:cover;">
                        <div>
                            <div style="font-weight:600;">Apel Fuji Segar (Petik Langsung)</div>
                            <div style="font-size:0.85rem; color:var(--text-muted);">2 kg x Rp35.000</div>
                        </div>
                    </div>
                </div>

                <div style="background:var(--white); padding:1.5rem; border-radius:var(--radius); box-shadow:var(--shadow);">
                    <h3 style="margin-bottom:1rem;">Metode Pembayaran</h3>
                    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem;" id="paymentMethods">
                        <div class="payment-method active" onclick="selectPayment(this)">
                            <i class="fa-solid fa-truck"></i><br>COD
                        </div>
                        <div class="payment-method" onclick="selectPayment(this)">
                            <i class="fa-solid fa-credit-card"></i><br>Transfer Bank
                        </div>
                        <div class="payment-method" onclick="selectPayment(this)">
                            <i class="fa-solid fa-qrcode"></i><br>QRIS
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
                    <span>Ongkos Kirim</span>
                    <span>Rp15.000</span>
                </div>
                <div class="summary-total">
                    <span>Total Tagihan</span>
                    <span>Rp85.000</span>
                </div>
                <button class="btn btn-primary" style="width:100%; margin-top:1.5rem;" onclick="window.location.href='tracking.php'">Buat Pesanan</button>
            </div>
        </div>
    </div>
    <script>
        function selectPayment(el) {
            document.querySelectorAll('.payment-method').forEach(p => p.classList.remove('active'));
            el.classList.add('active');
        }
    </script>
</body>
</html>
