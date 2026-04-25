<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Panenly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .dashboard-container {
            display: flex;
            min-height: 100vh;
            background: var(--bg-color);
        }
        .sidebar {
            width: 250px;
            background: var(--white);
            border-right: 1px solid var(--border-color);
            padding: 1.5rem;
        }
        .main-content {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }
        .chart-container {
            background: var(--white);
            padding: 1.5rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }
        /* nav items */
        .sidebar-menu { list-style: none; margin-top: 2rem; }
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
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <a href="../index.php" class="nav-brand" style="margin-bottom: 2rem;">
                <i class="fa-solid fa-leaf"></i> Panenly
            </a>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
                <li><a href="products.php" class="active"><i class="fa-solid fa-box"></i> Kelola Produk</a></li>
                <li><a href="../chat.php"><i class="fa-solid fa-message"></i> Pesan Masuk</a></li>
                <li><a href="profile.php"><i class="fa-solid fa-store"></i> Profil Toko</a></li>
                <li><a href="#" style="color:#ef4444;" onclick="localStorage.clear(); window.location.href='../login.php'"><i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar</a></li>
            </ul>
        </aside>
        
        <main class="main-content">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
                <h2>Kelola Produk</h2>
                <button class="btn btn-primary" onclick="document.getElementById('addProductForm').style.display='block'">+ Tambah Produk</button>
            </div>

            <div class="chart-container" id="addProductForm" style="display:none; margin-bottom:2rem;">
                <h3 style="margin-bottom:1.5rem;">Form Tambah Produk Baru</h3>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">
                    <div>
                        <label style="display:block; margin-bottom:0.5rem; font-size:0.9rem; font-weight:500;">Nama Produk</label>
                        <input type="text" style="width:100%; padding:0.75rem; border:1px solid var(--border-color); border-radius:8px;" placeholder="Misal: Apel Fuji">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:0.5rem; font-size:0.9rem; font-weight:500;">Harga (per kg/satuan)</label>
                        <input type="number" style="width:100%; padding:0.75rem; border:1px solid var(--border-color); border-radius:8px;">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:0.5rem; font-size:0.9rem; font-weight:500;">Stok Tersedia</label>
                        <input type="number" style="width:100%; padding:0.75rem; border:1px solid var(--border-color); border-radius:8px;">
                    </div>
                    <div>
                        <label style="display:flex; align-items:center; gap:10px; margin-top:2rem; cursor:pointer;">
                            <input type="checkbox" style="accent-color:var(--primary-color);"> Buka Sistem Pre-Order
                        </label>
                    </div>
                </div>
                <div style="margin-top:1.5rem; text-align:right;">
                    <button class="btn btn-outline" onclick="document.getElementById('addProductForm').style.display='none'">Batal</button>
                    <button class="btn btn-primary" onclick="alert('Produk berhasil ditambahkan!'); document.getElementById('addProductForm').style.display='none'">Simpan Produk</button>
                </div>
            </div>

            <div class="chart-container">
                <table style="width:100%; border-collapse:collapse; text-align:left;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border-color);">
                            <th style="padding:1rem;">Produk</th>
                            <th style="padding:1rem;">Harga</th>
                            <th style="padding:1rem;">Stok</th>
                            <th style="padding:1rem;">Pre-Order</th>
                            <th style="padding:1rem;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom:1px solid var(--border-color);">
                            <td style="padding:1rem; display:flex; align-items:center; gap:10px;">
                                <img src="https://images.unsplash.com/photo-1568702846914-96b305d2aaeb?q=80&w=100&auto=format&fit=crop" style="width:40px; height:40px; border-radius:4px; object-fit:cover;">
                                Apel Fuji Segar
                            </td>
                            <td style="padding:1rem;">Rp35.000</td>
                            <td style="padding:1rem;">45 kg</td>
                            <td style="padding:1rem;"><span style="background:rgba(245,158,11,0.1); color:#b45309; padding:4px 8px; border-radius:4px; font-size:0.8rem; font-weight:600;">Aktif</span></td>
                            <td style="padding:1rem;">
                                <button class="btn btn-outline" style="padding:0.25rem 0.5rem; font-size:0.8rem;">Edit</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
