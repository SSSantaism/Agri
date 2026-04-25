<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penjual - Panenly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }
        .stat-title {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-main);
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
                <li><a href="dashboard.php" class="active"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
                <li><a href="products.php"><i class="fa-solid fa-box"></i> Kelola Produk</a></li>
                <li><a href="../chat.php"><i class="fa-solid fa-message"></i> Pesan Masuk</a></li>
                <li><a href="profile.php"><i class="fa-solid fa-store"></i> Profil Toko</a></li>
                <li><a href="#" style="color:#ef4444;" onclick="localStorage.clear(); window.location.href='../login.php'"><i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar</a></li>
            </ul>
        </aside>
        
        <main class="main-content">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
                <h2>Ringkasan Toko Pak Tono</h2>
                <button class="btn btn-primary" onclick="window.location.href='products.php'">+ Tambah Produk Baru</button>
            </div>
            
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-title">Pendapatan Bulan Ini</div>
                    <div class="stat-value">Rp4.500.000</div>
                    <div style="font-size:0.8rem; color:#10b981; margin-top:5px;"><i class="fa-solid fa-arrow-trend-up"></i> +15% dari bulan lalu</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Pesanan Baru</div>
                    <div class="stat-value">24</div>
                    <div style="font-size:0.8rem; color:var(--text-muted); margin-top:5px;">5 Menunggu Dikirim</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Pre-Order Aktif</div>
                    <div class="stat-value">12</div>
                    <div style="font-size:0.8rem; color:var(--text-muted); margin-top:5px;">Panen 15 Mei 2026</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Rating Toko</div>
                    <div class="stat-value"><i class="fa-solid fa-star" style="color:#f59e0b; font-size:1.2rem;"></i> 4.9</div>
                    <div style="font-size:0.8rem; color:var(--text-muted); margin-top:5px;">Dari 320 ulasan</div>
                </div>
            </div>

            <div class="chart-container">
                <h3 style="margin-bottom:1.5rem;">Statistik Penjualan</h3>
                <canvas id="salesChart" height="100"></canvas>
            </div>
        </main>
    </div>

    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [{
                    label: 'Pendapatan (Juta Rupiah)',
                    data: [2.1, 2.8, 3.2, 3.8, 4.5, 0],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top', }
                }
            }
        });
    </script>
</body>
</html>
