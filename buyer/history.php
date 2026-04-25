<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Belanja - Panenly</title>
    <!-- CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .profile-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 5%;
            display: flex;
            gap: 2rem;
        }
        /* same as profile sidebar */
        .profile-sidebar {
            width: 250px;
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            height: fit-content;
        }
        .profile-content {
            flex: 1;
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 2rem;
        }
        .sidebar-menu { list-style: none; margin-top: 1.5rem; }
        .sidebar-menu li { margin-bottom: 0.5rem; }
        .sidebar-menu a {
            display: flex; align-items: center; gap: 10px;
            padding: 0.75rem 1rem; color: var(--text-muted);
            border-radius: 8px; font-weight: 500;
        }
        .sidebar-menu a.active {
            background: rgba(16, 185, 129, 0.1); color: var(--primary-color);
        }
        .sidebar-menu a:hover:not(.active) {
            background: var(--bg-color); color: var(--text-main);
        }
        /* history specific */
        .history-card {
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--bg-color);
        }
        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .badge-success { background: rgba(16,185,129,0.1); color: var(--primary-dark); }
        .badge-warning { background: rgba(245,158,11,0.1); color: #b45309; }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="../index.php" class="nav-brand">
            <i class="fa-solid fa-leaf"></i> Panenly
        </a>
    </nav>
    <div class="profile-container">
        <div class="profile-sidebar">
            <div style="text-align:center; padding-bottom:1.5rem; border-bottom:1px solid var(--border-color);">
                <img src="https://ui-avatars.com/api/?name=Budi+Santoso&background=10b981&color=fff" style="width:80px; border-radius:50%; margin-bottom:1rem;">
                <h3 style="font-size:1.1rem;">Budi Santoso</h3>
                <span style="font-size:0.85rem; color:var(--text-muted);">Pembeli</span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="profile.php"><i class="fa-solid fa-user"></i> Biodata Diri</a></li>
                <li><a href="history.php" class="active"><i class="fa-solid fa-clipboard-list"></i> Riwayat Belanja</a></li>
                <li><a href="../chat.php"><i class="fa-solid fa-message"></i> Pesan Masuk</a></li>
                <li><a href="#" style="color:#ef4444;" onclick="localStorage.clear(); window.location.href='../login.php'"><i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar</a></li>
            </ul>
        </div>
        <div class="profile-content">
            <h2 style="margin-bottom: 2rem;">Riwayat Belanja</h2>
            
            <div class="history-card">
                <div class="history-header">
                    <div>
                        <span style="font-weight:600; margin-right:1rem;"><i class="fa-solid fa-store"></i> Toko Pak Tono</span>
                        <span style="color:var(--text-muted); font-size:0.85rem;">24 Mei 2026</span>
                    </div>
                    <span class="badge badge-warning">Sedang Dikirim</span>
                </div>
                <div style="display:flex; gap:1rem; align-items:center;">
                    <img src="https://images.unsplash.com/photo-1568702846914-96b305d2aaeb?q=80&w=800&auto=format&fit=crop" style="width:60px; height:60px; border-radius:8px; object-fit:cover;">
                    <div style="flex:1;">
                        <div style="font-weight:600;">Apel Fuji Segar (Petik Langsung)</div>
                        <div style="font-size:0.85rem; color:var(--text-muted);">2 kg x Rp35.000</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:0.85rem; color:var(--text-muted);">Total Belanja</div>
                        <div style="font-weight:700; color:var(--primary-color);">Rp85.000</div>
                    </div>
                </div>
                <div style="margin-top:1.5rem; text-align:right;">
                    <button class="btn btn-outline" style="padding:0.5rem 1rem;" onclick="window.location.href='../tracking.php'">Lacak Paket</button>
                    <button class="btn btn-primary" style="padding:0.5rem 1rem;" onclick="window.location.href='../chat.php'">Hubungi Penjual</button>
                </div>
            </div>

            <div class="history-card">
                <div class="history-header">
                    <div>
                        <span style="font-weight:600; margin-right:1rem;"><i class="fa-solid fa-store"></i> Bu Siti</span>
                        <span style="color:var(--text-muted); font-size:0.85rem;">10 Mei 2026</span>
                    </div>
                    <span class="badge badge-success">Selesai</span>
                </div>
                <div style="display:flex; gap:1rem; align-items:center;">
                    <img src="../assets/images/product_carrot.png" style="width:60px; height:60px; border-radius:8px; object-fit:cover;" onerror="this.src='https://via.placeholder.com/60'">
                    <div style="flex:1;">
                        <div style="font-weight:600;">Wortel Manis Berastagi</div>
                        <div style="font-size:0.85rem; color:var(--text-muted);">1 kg x Rp12.000</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:0.85rem; color:var(--text-muted);">Total Belanja</div>
                        <div style="font-weight:700; color:var(--primary-color);">Rp27.000</div>
                    </div>
                </div>
                <div style="margin-top:1.5rem; text-align:right;">
                    <button class="btn btn-primary" style="padding:0.5rem 1rem;">Beri Ulasan</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
