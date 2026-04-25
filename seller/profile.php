<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Panenly</title>
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
        .sidebar-menu {
            list-style: none;
            margin-top: 1.5rem;
        }
        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.75rem 1rem;
            color: var(--text-muted);
            border-radius: 8px;
            font-weight: 500;
        }
        .sidebar-menu a.active {
            background: rgba(16, 185, 129, 0.1);
            color: var(--primary-color);
        }
        .sidebar-menu a:hover:not(.active) {
            background: var(--bg-color);
            color: var(--text-main);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            font-size: 0.9rem;
        }
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-family: inherit;
        }
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
                <img src="https://ui-avatars.com/api/?name=Pak+Tono&background=10b981&color=fff" style="width:80px; border-radius:50%; margin-bottom:1rem;">
                <h3 style="font-size:1.1rem;">Toko Pak Tono</h3>
                <span style="font-size:0.85rem; color:var(--text-muted);">Petani/Penjual</span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
                <li><a href="products.php"><i class="fa-solid fa-box"></i> Kelola Produk</a></li>
                <li><a href="../chat.php"><i class="fa-solid fa-message"></i> Pesan Masuk</a></li>
                <li><a href="profile.php" class="active"><i class="fa-solid fa-store"></i> Profil Toko</a></li>
                <li><a href="#" style="color:#ef4444;" onclick="localStorage.clear(); window.location.href='../login.php'"><i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar</a></li>
            </ul>
        </div>
        <div class="profile-content">
            <h2 style="margin-bottom: 2rem;">Pengaturan Toko</h2>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">
                <div class="form-group">
                    <label>Nama Toko/Petani</label>
                    <input type="text" class="form-control" value="Pak Tono">
                </div>
                <div class="form-group">
                    <label>Nomor HP</label>
                    <input type="text" class="form-control" value="+62 812-3456-7890">
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label>Email</label>
                    <input type="email" class="form-control" value="budi.santoso@email.com" readonly style="background:var(--bg-color);">
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label>Alamat Lengkap</label>
                    <textarea class="form-control" rows="3">Jl. Merdeka No. 123, Kecamatan Sukajadi, Kota Bandung, Jawa Barat 40161</textarea>
                </div>
            </div>
            <button class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </div>
</body>
</html>
