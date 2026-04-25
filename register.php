<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Panenly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bg-color);
            padding: 2rem;
        }
        .auth-card {
            background: var(--white);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 450px;
        }
        .auth-card h2 {
            margin-bottom: 0.5rem;
            text-align: center;
            font-size: 1.8rem;
            color: var(--text-main);
        }
        .auth-subtitle {
            text-align: center;
            color: var(--text-muted);
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            font-family: inherit;
            outline: none;
            transition: var(--transition);
        }
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }
        .role-selector {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .role-card {
            flex: 1;
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }
        .role-card.active {
            border-color: var(--primary-color);
            background-color: rgba(16, 185, 129, 0.05);
        }
        .role-card i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--text-muted);
        }
        .role-card.active i {
            color: var(--primary-color);
        }
        .btn-full {
            width: 100%;
            padding: 0.8rem;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="nav-brand" style="justify-content: center; margin-bottom: 1rem;">
                <i class="fa-solid fa-leaf"></i> Panenly
            </div>
            <h2>Bergabung dengan Panenly</h2>
            <p class="auth-subtitle">Pilih peran Anda untuk melanjutkan</p>
            
            <div class="role-selector">
                <div class="role-card active" onclick="selectRole('buyer')" id="roleBuyer">
                    <i class="fa-solid fa-basket-shopping"></i>
                    <div style="font-weight: 600;">Pembeli</div>
                </div>
                <div class="role-card" onclick="selectRole('seller')" id="roleSeller">
                    <i class="fa-solid fa-tractor"></i>
                    <div style="font-weight: 600;">Petani/Penjual</div>
                </div>
            </div>

            <form action="#" method="POST" id="registerForm">
                <input type="hidden" id="selectedRole" value="buyer">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" class="form-control" placeholder="Masukkan nama lengkap" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" placeholder="Masukkan email aktif" required>
                </div>
                <div class="form-group">
                    <label>Kata Sandi</label>
                    <input type="password" class="form-control" placeholder="Buat kata sandi minimal 8 karakter" required>
                </div>
                <button type="submit" class="btn btn-primary btn-full">Daftar Sekarang</button>
            </form>
            <p style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: var(--text-muted);">
                Sudah punya akun? <a href="login.php" style="color: var(--primary-color); font-weight: 600;">Masuk</a>
            </p>
        </div>
    </div>
    <script>
        function selectRole(role) {
            document.getElementById('roleBuyer').classList.remove('active');
            document.getElementById('roleSeller').classList.remove('active');
            
            if(role === 'buyer') {
                document.getElementById('roleBuyer').classList.add('active');
            } else {
                document.getElementById('roleSeller').classList.add('active');
            }
            document.getElementById('selectedRole').value = role;
        }

        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Mock register logic
            const role = document.getElementById('selectedRole').value;
            if(role === 'seller') {
                alert('Pendaftaran sebagai penjual berhasil! Menunggu persetujuan Admin.');
                window.location.href = 'login.php';
            } else {
                alert('Pendaftaran berhasil! Silakan masuk.');
                window.location.href = 'login.php';
            }
        });
    </script>
</body>
</html>
