<?php
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';

// If already logged in, redirect
if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

// Handle registration form
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'buyer';
    $phone = trim($_POST['phone'] ?? '');
    $storeName = trim($_POST['store_name'] ?? '');
    $storeLocation = trim($_POST['store_location'] ?? '');
    
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Semua field wajib harus diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 8) {
        $error = 'Kata sandi minimal 8 karakter.';
    } elseif (!in_array($role, ['buyer', 'seller'])) {
        $error = 'Peran tidak valid.';
    } else {
        $result = registerUser($name, $email, $password, $role, $phone ?: null, $storeName ?: null, $storeLocation ?: null);
        if ($result['success']) {
            setFlash('success', $result['message']);
            header('Location: ' . BASE_URL . '/login.php');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Freshly</title>
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
            box-sizing: border-box;
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
        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            text-align: center;
        }
        .seller-fields {
            display: none;
            animation: fadeIn 0.3s ease;
        }
        .seller-fields.show {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="nav-brand" style="justify-content: center; margin-bottom: 1rem;">
                <i class="fa-solid fa-leaf"></i> Freshly
            </div>
            <h2>Bergabung dengan Freshly</h2>
            <p class="auth-subtitle">Pilih peran Anda untuk melanjutkan</p>
            
            <?php if ($error): ?>
                <div class="error-msg"><?= sanitize($error) ?></div>
            <?php endif; ?>
            
            <div class="role-selector">
                <div class="role-card <?= ($_POST['role'] ?? 'buyer') === 'buyer' ? 'active' : '' ?>" onclick="selectRole('buyer')" id="roleBuyer">
                    <i class="fa-solid fa-basket-shopping"></i>
                    <div style="font-weight: 600;">Pembeli</div>
                </div>
                <div class="role-card <?= ($_POST['role'] ?? '') === 'seller' ? 'active' : '' ?>" onclick="selectRole('seller')" id="roleSeller">
                    <i class="fa-solid fa-tractor"></i>
                    <div style="font-weight: 600;">Petani/Penjual</div>
                </div>
            </div>

            <form action="" method="POST" id="registerForm">
                <input type="hidden" name="role" id="selectedRole" value="<?= sanitize($_POST['role'] ?? 'buyer') ?>">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" placeholder="Masukkan nama lengkap" required value="<?= sanitize($_POST['name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Masukkan email aktif" required value="<?= sanitize($_POST['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Nomor HP</label>
                    <input type="text" name="phone" class="form-control" placeholder="Masukkan nomor HP" value="<?= sanitize($_POST['phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Kata Sandi</label>
                    <input type="password" name="password" class="form-control" placeholder="Buat kata sandi minimal 8 karakter" required>
                </div>
                
                <div class="seller-fields <?= ($_POST['role'] ?? '') === 'seller' ? 'show' : '' ?>" id="sellerFields">
                    <div class="form-group">
                        <label>Nama Toko / Usaha</label>
                        <input type="text" name="store_name" class="form-control" placeholder="Nama toko atau kebun Anda" value="<?= sanitize($_POST['store_name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Provinsi</label>
                        <select id="provinceSelect" class="form-control" onchange="updateKabupaten()">
                            <option value="">Pilih Provinsi</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kabupaten / Kota</label>
                        <select id="kabupatenSelect" class="form-control" onchange="updateStoreLocation()">
                            <option value="">Pilih Kabupaten/Kota</option>
                        </select>
                    </div>
                    <input type="hidden" name="store_location" id="storeLocationInput" value="<?= sanitize($_POST['store_location'] ?? '') ?>">
                </div>
                
                <button type="submit" class="btn btn-primary btn-full">Daftar Sekarang</button>
            </form>
            <p style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: var(--text-muted);">
                Sudah punya akun? <a href="login.php" style="color: var(--primary-color); font-weight: 600;">Masuk</a>
            </p>
        </div>
    </div>
    <script>
        const regionData = {
            "Aceh": ["Banda Aceh","Langsa","Lhokseumawe","Sabang","Aceh Besar","Aceh Barat","Aceh Selatan","Aceh Tengah","Aceh Timur","Aceh Utara","Pidie","Bireuen"],
            "Sumatera Utara": ["Medan","Binjai","Pematang Siantar","Tebing Tinggi","Padang Sidempuan","Deli Serdang","Langkat","Simalungun","Karo","Toba Samosir","Tapanuli Utara","Mandailing Natal"],
            "Sumatera Barat": ["Padang","Bukittinggi","Payakumbuh","Solok","Padang Panjang","Agam","Tanah Datar","Pesisir Selatan","Lima Puluh Kota","Pasaman","Dharmasraya"],
            "Riau": ["Pekanbaru","Dumai","Kampar","Rokan Hilir","Bengkalis","Siak","Indragiri Hilir","Indragiri Hulu","Kuantan Singingi","Pelalawan"],
            "Kepulauan Riau": ["Batam","Tanjung Pinang","Bintan","Karimun","Lingga","Natuna","Kepulauan Anambas"],
            "Jambi": ["Jambi","Sungai Penuh","Muaro Jambi","Batang Hari","Tanjung Jabung Barat","Tanjung Jabung Timur","Bungo","Tebo","Merangin","Sarolangun","Kerinci"],
            "Sumatera Selatan": ["Palembang","Prabumulih","Lubuk Linggau","Pagar Alam","Ogan Komering Ilir","Muara Enim","Musi Banyuasin","Banyuasin","Ogan Ilir","Lahat"],
            "Bengkulu": ["Bengkulu","Rejang Lebong","Bengkulu Selatan","Bengkulu Utara","Kaur","Seluma","Mukomuko","Lebong","Kepahiang"],
            "Lampung": ["Bandar Lampung","Metro","Lampung Selatan","Lampung Tengah","Lampung Utara","Lampung Barat","Lampung Timur","Tanggamus","Way Kanan","Pringsewu","Pesawaran","Tulang Bawang"],
            "Bangka Belitung": ["Pangkal Pinang","Bangka","Belitung","Bangka Barat","Bangka Tengah","Bangka Selatan","Belitung Timur"],
            "DKI Jakarta": ["Jakarta Pusat","Jakarta Utara","Jakarta Barat","Jakarta Selatan","Jakarta Timur","Kepulauan Seribu"],
            "Jawa Barat": ["Bandung","Bekasi","Bogor","Cimahi","Cirebon","Depok","Sukabumi","Tasikmalaya","Garut","Karawang","Subang","Sumedang","Purwakarta","Cianjur","Majalengka","Kuningan","Indramayu","Pangandaran"],
            "Banten": ["Serang","Cilegon","Tangerang","Tangerang Selatan","Pandeglang","Lebak"],
            "Jawa Tengah": ["Semarang","Solo","Magelang","Pekalongan","Salatiga","Tegal","Surakarta","Klaten","Boyolali","Purwokerto","Cilacap","Kebumen","Purworejo","Wonosobo","Temanggung","Kendal","Demak","Jepara","Kudus","Pati","Blora","Rembang","Brebes","Pemalang","Batang","Banyumas","Karanganyar","Sragen","Wonogiri"],
            "DI Yogyakarta": ["Yogyakarta","Sleman","Bantul","Gunung Kidul","Kulon Progo"],
            "Jawa Timur": ["Surabaya","Malang","Batu","Kediri","Blitar","Mojokerto","Pasuruan","Probolinggo","Madiun","Jember","Banyuwangi","Situbondo","Bondowoso","Lumajang","Tulungagung","Trenggalek","Ponorogo","Ngawi","Magetan","Pacitan","Nganjuk","Tuban","Lamongan","Gresik","Sidoarjo","Bangkalan","Sampang","Pamekasan","Sumenep","Jombang","Bojonegoro"],
            "Bali": ["Denpasar","Badung","Gianyar","Tabanan","Bangli","Klungkung","Karangasem","Buleleng","Jembrana"],
            "Nusa Tenggara Barat": ["Mataram","Lombok Barat","Lombok Tengah","Lombok Timur","Lombok Utara","Sumbawa","Sumbawa Barat","Dompu","Bima"],
            "Nusa Tenggara Timur": ["Kupang","Ende","Flores Timur","Manggarai","Manggarai Barat","Ngada","Sikka","Sumba Barat","Sumba Timur","Timor Tengah Selatan","Timor Tengah Utara","Belu","Alor","Lembata","Rote Ndao"],
            "Kalimantan Barat": ["Pontianak","Singkawang","Sambas","Bengkayang","Landak","Sanggau","Ketapang","Sintang","Kapuas Hulu","Sekadau","Melawi","Kayong Utara","Kubu Raya"],
            "Kalimantan Tengah": ["Palangka Raya","Kapuas","Barito Selatan","Barito Utara","Kotawaringin Barat","Kotawaringin Timur","Katingan","Seruyan","Murung Raya","Gunung Mas","Pulang Pisau","Sukamara","Lamandau","Barito Timur"],
            "Kalimantan Selatan": ["Banjarmasin","Banjarbaru","Banjar","Tanah Laut","Tanah Bumbu","Kotabaru","Tapin","Hulu Sungai Selatan","Hulu Sungai Tengah","Hulu Sungai Utara","Barito Kuala","Tabalong","Balangan"],
            "Kalimantan Timur": ["Samarinda","Balikpapan","Bontang","Kutai Kartanegara","Kutai Barat","Kutai Timur","Berau","Paser","Penajam Paser Utara","Mahakam Ulu"],
            "Kalimantan Utara": ["Tanjung Selor","Tarakan","Bulungan","Malinau","Nunukan","Tana Tidung"],
            "Sulawesi Utara": ["Manado","Bitung","Tomohon","Kotamobagu","Minahasa","Minahasa Utara","Minahasa Selatan","Minahasa Tenggara","Bolaang Mongondow","Kepulauan Sangihe","Kepulauan Talaud","Kepulauan Siau Tagulandang Biaro"],
            "Gorontalo": ["Gorontalo","Gorontalo Utara","Bone Bolango","Boalemo","Pohuwato"],
            "Sulawesi Tengah": ["Palu","Donggala","Sigi","Parigi Moutong","Poso","Tojo Una-Una","Toli-Toli","Banggai","Banggai Kepulauan","Morowali","Buol"],
            "Sulawesi Selatan": ["Makassar","Parepare","Palopo","Maros","Pangkep","Gowa","Takalar","Jeneponto","Bantaeng","Bulukumba","Sinjai","Bone","Soppeng","Wajo","Sidrap","Pinrang","Enrekang","Tana Toraja","Toraja Utara","Luwu","Luwu Utara","Luwu Timur","Barru","Selayar"],
            "Sulawesi Tenggara": ["Kendari","Bau-Bau","Konawe","Konawe Selatan","Kolaka","Kolaka Utara","Muna","Buton","Bombana","Wakatobi"],
            "Sulawesi Barat": ["Mamuju","Majene","Polewali Mandar","Mamasa","Mamuju Tengah","Mamuju Utara"],
            "Maluku": ["Ambon","Tual","Maluku Tengah","Seram Bagian Barat","Seram Bagian Timur","Buru","Kepulauan Aru","Maluku Barat Daya","Maluku Tenggara Barat"],
            "Maluku Utara": ["Ternate","Tidore Kepulauan","Halmahera Utara","Halmahera Selatan","Halmahera Barat","Halmahera Timur","Halmahera Tengah","Kepulauan Sula","Pulau Morotai"],
            "Papua": ["Jayapura","Merauke","Mimika","Jayawijaya","Biak Numfor","Nabire","Keerom","Sarmi","Yapen Waropen","Mappi","Asmat","Boven Digoel","Pegunungan Bintang","Yahukimo","Tolikara","Puncak Jaya"],
            "Papua Barat": ["Manokwari","Sorong","Fakfak","Kaimana","Raja Ampat","Teluk Bintuni","Teluk Wondama","Maybrat","Tambrauw","Sorong Selatan"],
            "Papua Selatan": ["Merauke","Boven Digoel","Mappi","Asmat"],
            "Papua Tengah": ["Nabire","Dogiyai","Paniai","Deiyai","Intan Jaya","Mimika","Puncak","Puncak Jaya"],
            "Papua Pegunungan": ["Jayawijaya","Lanny Jaya","Mamberamo Tengah","Yalimo","Yahukimo","Tolikara","Pegunungan Bintang","Nduga"]
        };
        
        // Populate province dropdown
        const provinceSelect = document.getElementById('provinceSelect');
        const kabupatenSelect = document.getElementById('kabupatenSelect');
        const storeLocationInput = document.getElementById('storeLocationInput');
        
        Object.keys(regionData).sort().forEach(prov => {
            const opt = document.createElement('option');
            opt.value = prov;
            opt.textContent = prov;
            provinceSelect.appendChild(opt);
        });
        
        function updateKabupaten() {
            const prov = provinceSelect.value;
            kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
            storeLocationInput.value = '';
            
            if (prov && regionData[prov]) {
                regionData[prov].forEach(kab => {
                    const opt = document.createElement('option');
                    opt.value = kab;
                    opt.textContent = kab;
                    kabupatenSelect.appendChild(opt);
                });
            }
        }
        
        function updateStoreLocation() {
            const prov = provinceSelect.value;
            const kab = kabupatenSelect.value;
            if (prov && kab) {
                storeLocationInput.value = kab + ', ' + prov;
            } else if (prov) {
                storeLocationInput.value = prov;
            } else {
                storeLocationInput.value = '';
            }
        }
        
        // Restore selection from POST data if available
        (function() {
            const savedLocation = storeLocationInput.value;
            if (savedLocation) {
                const parts = savedLocation.split(', ');
                if (parts.length >= 2) {
                    const kab = parts[0];
                    const prov = parts.slice(1).join(', ');
                    provinceSelect.value = prov;
                    updateKabupaten();
                    kabupatenSelect.value = kab;
                }
            }
        })();
        
        function selectRole(role) {
            document.getElementById('roleBuyer').classList.remove('active');
            document.getElementById('roleSeller').classList.remove('active');
            
            if(role === 'buyer') {
                document.getElementById('roleBuyer').classList.add('active');
                document.getElementById('sellerFields').classList.remove('show');
            } else {
                document.getElementById('roleSeller').classList.add('active');
                document.getElementById('sellerFields').classList.add('show');
            }
            document.getElementById('selectedRole').value = role;
        }
    </script>
</body>
</html>
