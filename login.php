<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Panenly</title>
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
            max-width: 400px;
        }
        .auth-card h2 {
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 1.8rem;
            color: var(--text-main);
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
        .auth-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
        }
        .auth-links a {
            color: var(--primary-color);
            font-weight: 500;
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
            <div class="nav-brand" style="justify-content: center; margin-bottom: 2rem;">
                <i class="fa-solid fa-leaf"></i> Panenly
            </div>
            <h2>Selamat Datang Kembali</h2>
            <form action="#" method="POST" id="loginForm">
                <div class="form-group">
                    <label>Email (Ketik 'admin' atau 'seller' untuk testing)</label>
                    <input type="text" id="email" class="form-control" placeholder="Masukkan email anda" required>
                </div>
                <div class="form-group">
                    <label>Kata Sandi</label>
                    <input type="password" id="password" class="form-control" placeholder="Masukkan kata sandi" required>
                </div>
                <div class="auth-links">
                    <label style="display:flex; align-items:center; gap:5px; cursor:pointer;">
                        <input type="checkbox" style="accent-color: var(--primary-color);"> Ingat Saya
                    </label>
                    <a href="#">Lupa sandi?</a>
                </div>
                <button type="submit" class="btn btn-primary btn-full">Masuk</button>
            </form>
            <p style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: var(--text-muted);">
                Belum punya akun? <a href="register.php" style="color: var(--primary-color); font-weight: 600;">Daftar di sini</a>
            </p>
        </div>
    </div>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Mock logic
            const email = document.getElementById('email').value.toLowerCase();
            if(email.includes('admin')) {
                localStorage.setItem('userRole', 'admin');
                window.location.href = 'admin/dashboard.php';
            } else if(email.includes('seller')) {
                localStorage.setItem('userRole', 'seller');
                window.location.href = 'seller/dashboard.php';
            } else {
                localStorage.setItem('userRole', 'buyer');
                window.location.href = 'index.php';
            }
        });
    </script>
</body>
</html>
