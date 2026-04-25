<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pengiriman - Panenly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .tracking-container {
            max-width: 800px;
            margin: 2rem auto 4rem;
            padding: 0 5%;
        }
        .tracking-card {
            background: var(--white);
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-top: 1.5rem;
        }
        .timeline {
            position: relative;
            padding-left: 2rem;
            margin-top: 2rem;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 0.5rem;
            top: 0.5rem;
            bottom: 0;
            width: 2px;
            background: var(--border-color);
        }
        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
        }
        .timeline-item:last-child {
            margin-bottom: 0;
        }
        .timeline-item .dot {
            position: absolute;
            left: -2rem;
            top: 0.25rem;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            background: var(--border-color);
            border: 3px solid var(--white);
            box-shadow: 0 0 0 1px var(--border-color);
            z-index: 1;
        }
        .timeline-item.active .dot {
            background: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
            border-color: var(--white);
        }
        .timeline-item h4 {
            margin-bottom: 0.25rem;
            color: var(--text-muted);
        }
        .timeline-item.active h4 {
            color: var(--text-main);
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="nav-brand">
            <i class="fa-solid fa-leaf"></i> Panenly
        </a>
    </nav>
    <div class="tracking-container">
        <h2>Status Pengiriman</h2>
        <div class="tracking-card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; border-bottom:1px solid var(--border-color); padding-bottom:1rem;">
                <div>
                    <div style="font-size:0.9rem; color:var(--text-muted);">No. Resi</div>
                    <div style="font-weight:700; font-size:1.1rem;">PNL-8923749823</div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:0.9rem; color:var(--text-muted);">Kurir</div>
                    <div style="font-weight:600;"><i class="fa-solid fa-motorcycle" style="color:var(--primary-color);"></i> Panenly Express</div>
                </div>
            </div>
            
            <div style="font-weight:600; font-size:1.1rem; color:var(--primary-dark);">Estimasi Tiba: Hari ini, 14:00 WIB</div>
            
            <div class="timeline">
                <div class="timeline-item active">
                    <div class="dot"></div>
                    <div class="content">
                        <h4>Kurir sedang menuju lokasi Anda</h4>
                        <span style="font-size:0.85rem; color:var(--text-muted);">25 Mei 2026, 13:45 - Jl. Merdeka Indah</span>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="dot"></div>
                    <div class="content">
                        <h4>Pesanan dibawa oleh kurir</h4>
                        <span style="font-size:0.85rem; color:var(--text-muted);">25 Mei 2026, 10:00 - Gudang Transit Bandung</span>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="dot"></div>
                    <div class="content">
                        <h4>Pesanan diproses penjual</h4>
                        <span style="font-size:0.85rem; color:var(--text-muted);">25 Mei 2026, 08:30 - Lembang, Bandung</span>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="dot"></div>
                    <div class="content">
                        <h4>Pesanan Dibuat</h4>
                        <span style="font-size:0.85rem; color:var(--text-muted);">24 Mei 2026, 20:15</span>
                    </div>
                </div>
            </div>
            <button class="btn btn-outline" style="width:100%; margin-top:2rem;" onclick="window.location.href='buyer/history.php'">Lihat Riwayat Pesanan</button>
        </div>
    </div>
</body>
</html>
