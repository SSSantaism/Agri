<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Panenly</title>
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
                <i class="fa-solid fa-leaf"></i> Panenly Admin
            </a>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active"><i class="fa-solid fa-users-gear"></i> Verifikasi Penjual</a></li>
                <li><a href="#"><i class="fa-solid fa-users"></i> Semua Pengguna</a></li>
                <li><a href="#"><i class="fa-solid fa-chart-pie"></i> Laporan Platform</a></li>
                <li><a href="#" style="color:#ef4444;" onclick="localStorage.clear(); window.location.href='../login.php'"><i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar</a></li>
            </ul>
        </aside>
        
        <main class="main-content">
            <h2 style="margin-bottom: 2rem;">Permintaan Pendaftaran Penjual</h2>
            
            <div class="chart-container">
                <table style="width:100%; border-collapse:collapse; text-align:left;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border-color);">
                            <th style="padding:1rem;">Toko / Petani</th>
                            <th style="padding:1rem;">Kontak</th>
                            <th style="padding:1rem;">Tanggal Daftar</th>
                            <th style="padding:1rem;">Status</th>
                            <th style="padding:1rem;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom:1px solid var(--border-color);">
                            <td style="padding:1rem; font-weight:600;">Kebun Sayur Lembang</td>
                            <td style="padding:1rem;">kebun.lembang@email.com<br><span style="font-size:0.85rem; color:var(--text-muted);">+62 811-2233-4455</span></td>
                            <td style="padding:1rem;">25 Mei 2026</td>
                            <td style="padding:1rem;"><span style="background:rgba(245,158,11,0.1); color:#b45309; padding:4px 8px; border-radius:4px; font-size:0.8rem; font-weight:600;">Menunggu Review</span></td>
                            <td style="padding:1rem;">
                                <button class="btn btn-primary" style="padding:0.25rem 0.75rem; font-size:0.8rem; margin-right:5px;" onclick="alert('Penjual diterima!'); this.parentElement.parentElement.style.display='none'">Terima</button>
                                <button class="btn btn-outline" style="padding:0.25rem 0.75rem; font-size:0.8rem; color:#ef4444; border-color:#ef4444;" onclick="alert('Penjual ditolak!'); this.parentElement.parentElement.style.display='none'">Tolak</button>
                            </td>
                        </tr>
                        <tr style="border-bottom:1px solid var(--border-color);">
                            <td style="padding:1rem; font-weight:600;">Koperasi Tani Makmur Sentosa</td>
                            <td style="padding:1rem;">koperasi.makmur@email.com<br><span style="font-size:0.85rem; color:var(--text-muted);">+62 812-9988-7766</span></td>
                            <td style="padding:1rem;">24 Mei 2026</td>
                            <td style="padding:1rem;"><span style="background:rgba(245,158,11,0.1); color:#b45309; padding:4px 8px; border-radius:4px; font-size:0.8rem; font-weight:600;">Menunggu Review</span></td>
                            <td style="padding:1rem;">
                                <button class="btn btn-primary" style="padding:0.25rem 0.75rem; font-size:0.8rem; margin-right:5px;" onclick="alert('Penjual diterima!'); this.parentElement.parentElement.style.display='none'">Terima</button>
                                <button class="btn btn-outline" style="padding:0.25rem 0.75rem; font-size:0.8rem; color:#ef4444; border-color:#ef4444;" onclick="alert('Penjual ditolak!'); this.parentElement.parentElement.style.display='none'">Tolak</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
