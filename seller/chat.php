<?php
require_once __DIR__ . '/../includes/helpers.php';
requireApprovedSeller();

$db = getDB();
$userId = $_SESSION['user_id'];

// Sellers can only chat with buyers
$partnerRole = 'buyer';

// Get conversation partners (only buyers)
$stmt = $db->prepare("
    SELECT u.id, u.name, u.store_name, u.role,
        (SELECT message FROM messages WHERE (sender_id=u.id AND receiver_id=?) OR (sender_id=? AND receiver_id=u.id) ORDER BY created_at DESC LIMIT 1) as last_message,
        (SELECT created_at FROM messages WHERE (sender_id=u.id AND receiver_id=?) OR (sender_id=? AND receiver_id=u.id) ORDER BY created_at DESC LIMIT 1) as last_time,
        (SELECT COUNT(*) FROM messages WHERE sender_id=u.id AND receiver_id=? AND is_read=0) as unread
    FROM users u
    WHERE u.id != ? AND u.role = ? AND u.id IN (
        SELECT DISTINCT sender_id FROM messages WHERE receiver_id = ?
        UNION
        SELECT DISTINCT receiver_id FROM messages WHERE sender_id = ?
    )
    ORDER BY last_time DESC
");
$stmt->execute([$userId,$userId,$userId,$userId,$userId,$userId,$partnerRole,$userId,$userId]);
$partners = $stmt->fetchAll();

// Check if partner is selected via URL
$selectedPartner = (int) ($_GET['partner'] ?? 0);
$productId = (int) ($_GET['product_id'] ?? 0);

if ($selectedPartner > 0) {
    $found = false;
    foreach ($partners as $p) { if ($p['id'] == $selectedPartner) { $found = true; break; } }
    if (!$found) {
        $stmt = $db->prepare("SELECT id, name, store_name, role FROM users WHERE id=? AND role=?");
        $stmt->execute([$selectedPartner, $partnerRole]);
        $newPartner = $stmt->fetch();
        if ($newPartner) {
            $newPartner['last_message'] = '';
            $newPartner['last_time'] = null;
            $newPartner['unread'] = 0;
            array_unshift($partners, $newPartner);
        }
    }
}
if ($selectedPartner <= 0 && !empty($partners)) {
    $selectedPartner = $partners[0]['id'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Masuk - Freshly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .dashboard-container { display:flex; min-height:100vh; background:var(--bg-color); }
        .sidebar { width:250px; background:var(--white); border-right:1px solid var(--border-color); padding:1.5rem; flex-shrink:0; }
        .main-content { flex:1; padding:2rem; overflow-y:auto; display:flex; flex-direction:column; }
        .sidebar-menu { list-style:none; margin-top:2rem; }
        .sidebar-menu li { margin-bottom:0.5rem; }
        .sidebar-menu a { display:flex; align-items:center; gap:10px; padding:0.75rem 1rem; color:var(--text-muted); border-radius:8px; font-weight:500; text-decoration:none; }
        .sidebar-menu a.active { background:rgba(16,185,129,0.1); color:var(--primary-color); }
        .sidebar-menu a:hover:not(.active) { background:var(--bg-color); color:var(--text-main); }
        
        .chat-container { display:flex; flex:1; background:var(--white); border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; min-height:0; }
        .chat-sidebar-inner { width:280px; border-right:1px solid var(--border-color); display:flex; flex-direction:column; flex-shrink:0; }
        .chat-list { flex:1; overflow-y:auto; }
        .chat-user { display:flex; gap:10px; padding:1rem; border-bottom:1px solid var(--border-color); cursor:pointer; transition:var(--transition); text-decoration:none; color:inherit; }
        .chat-user:hover, .chat-user.active { background:rgba(16,185,129,0.05); }
        .chat-user img { width:40px; height:40px; border-radius:50%; }
        .chat-main { flex:1; display:flex; flex-direction:column; min-width:0; }
        .chat-header { padding:1rem; border-bottom:1px solid var(--border-color); display:flex; align-items:center; gap:10px; font-weight:600; }
        .chat-messages { flex:1; padding:1.5rem; overflow-y:auto; background:var(--bg-color); display:flex; flex-direction:column; gap:1rem; }
        .msg { max-width:70%; padding:0.75rem 1rem; border-radius:12px; font-size:0.95rem; }
        .msg.received { background:var(--white); align-self:flex-start; border-bottom-left-radius:0; box-shadow:0 1px 2px rgba(0,0,0,0.05); }
        .msg.sent { background:var(--primary-color); color:white; align-self:flex-end; border-bottom-right-radius:0; }
        .chat-input { padding:1rem; border-top:1px solid var(--border-color); display:flex; gap:10px; }
        .chat-input input { flex:1; padding:0.75rem 1rem; border:1px solid var(--border-color); border-radius:20px; outline:none; font-family:inherit; }
        .chat-input input:focus { border-color:var(--primary-color); }
        .btn-send { background:var(--primary-color); color:white; border:none; width:45px; height:45px; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:var(--transition); flex-shrink:0; }
        .btn-send:hover { background:var(--primary-dark); }
        .unread-dot { background:var(--primary-color); width:8px; height:8px; border-radius:50%; flex-shrink:0; }
        .product-context-card {
            display:flex; gap:12px; padding:12px; background:var(--white);
            border:1px solid var(--border-color); border-radius:10px;
            box-shadow:0 1px 3px rgba(0,0,0,0.06); align-self:stretch;
        }
        .product-context-card img {
            width:60px; height:60px; object-fit:cover; border-radius:8px; flex-shrink:0;
        }
        .product-context-card .product-ctx-info { flex:1; min-width:0; }
        .product-context-card .product-ctx-name {
            font-weight:600; font-size:0.9rem; color:var(--text-main);
            white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
        }
        .product-context-card .product-ctx-price {
            font-weight:700; font-size:0.95rem; color:var(--primary-color); margin-top:2px;
        }
        .product-context-card .product-ctx-weight {
            font-size:0.8rem; color:var(--text-muted);
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Seller Sidebar -->
        <aside class="sidebar">
            <a href="../index.php" class="nav-brand" style="margin-bottom:2rem;"><i class="fa-solid fa-leaf"></i> Freshly</a>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
                <li><a href="products.php"><i class="fa-solid fa-box"></i> Kelola Produk</a></li>
                <li><a href="chat.php" class="active"><i class="fa-solid fa-message"></i> Pesan Masuk</a></li>
                <li><a href="profile.php"><i class="fa-solid fa-store"></i> Profil Toko</a></li>
                <li><a href="<?= BASE_URL ?>/includes/logout.php" style="color:#ef4444;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <h2 style="margin-bottom:1.5rem;">Pesan Masuk</h2>
            <div class="chat-container">
                <div class="chat-sidebar-inner">
                    <div style="padding:1rem;border-bottom:1px solid var(--border-color);font-weight:600;font-size:0.95rem;">
                        <i class="fa-solid fa-comments" style="color:var(--primary-color);"></i> Daftar Percakapan
                    </div>
                    <div class="chat-list">
                        <?php if(empty($partners)): ?>
                        <div style="padding:2rem;text-align:center;color:var(--text-muted);font-size:0.9rem;">Belum ada pesan masuk dari pembeli.</div>
                        <?php endif; ?>
                        <?php foreach($partners as $p): ?>
                        <a href="?partner=<?= $p['id'] ?>" class="chat-user <?= $selectedPartner==$p['id']?'active':'' ?>">
                            <img src="<?= getAvatarUrl($p['name']) ?>">
                            <div style="flex:1;min-width:0;">
                                <div style="font-weight:600;font-size:0.95rem;"><?= sanitize($p['name']) ?></div>
                                <div style="font-size:0.8rem;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:160px;"><?= sanitize($p['last_message']??'') ?></div>
                            </div>
                            <?php if($p['unread']>0): ?><div class="unread-dot"></div><?php endif; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="chat-main">
                    <?php if($selectedPartner > 0):
                        $partnerName = '';
                        foreach($partners as $p) { if($p['id']==$selectedPartner) { $partnerName = $p['name']; break; } }
                    ?>
                    <div class="chat-header">
                        <img src="<?= getAvatarUrl($partnerName) ?>" style="width:35px;border-radius:50%;">
                        <?= sanitize($partnerName) ?>
                        <span style="font-size:0.8rem;color:var(--text-muted);font-weight:400;margin-left:auto;">Pembeli</span>
                    </div>
                    <div class="chat-messages" id="messageBox"></div>
                    <div class="chat-input">
                        <input type="text" id="chatInput" placeholder="Ketik balasan...">
                        <button class="btn-send" onclick="sendMessage()"><i class="fa-solid fa-paper-plane"></i></button>
                    </div>
                    <?php else: ?>
                    <div style="flex:1;display:flex;align-items:center;justify-content:center;color:var(--text-muted);">
                        <div style="text-align:center;"><i class="fa-solid fa-message" style="font-size:3rem;opacity:0.2;margin-bottom:1rem;display:block;"></i>Pilih percakapan di samping</div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <?php if($selectedPartner > 0): ?>
    <script>
    const partnerId = <?= $selectedPartner ?>;
    const productId = <?= $productId ?: 'null' ?>;
    const baseUrl = '<?= BASE_URL ?>';
    
    function loadMessages() {
        let url = baseUrl + '/api/messages.php?partner_id=' + partnerId;
        if (productId) url += '&product_id=' + productId;
        
        fetch(url)
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;
            const box = document.getElementById('messageBox');
            box.innerHTML = '';
            
            // Show product context card above messages
            if (data.product_context) {
                const card = document.createElement('div');
                card.className = 'product-context-card';
                card.innerHTML = `
                    <img src="${data.product_context.image_url}" alt="${data.product_context.name}" onerror="this.src='https://via.placeholder.com/60?text=F'">
                    <div class="product-ctx-info">
                        <div class="product-ctx-name">${data.product_context.name}</div>
                        <div class="product-ctx-price">${data.product_context.price_formatted}</div>
                        <div class="product-ctx-weight">${data.product_context.weight || ''}</div>
                    </div>
                `;
                box.appendChild(card);
            }
            
            data.messages.forEach(m => {
                const div = document.createElement('div');
                div.className = 'msg ' + (m.sender_id == data.current_user_id ? 'sent' : 'received');
                div.textContent = m.message;
                box.appendChild(div);
            });
            box.scrollTop = box.scrollHeight;
        });
    }
    
    function sendMessage() {
        const input = document.getElementById('chatInput');
        const msg = input.value.trim();
        if (!msg) return;
        
        let body = 'receiver_id=' + partnerId + '&message=' + encodeURIComponent(msg);
        if (productId) body += '&product_id=' + productId;
        
        fetch(baseUrl + '/api/messages.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: body
        }).then(r => r.json()).then(data => {
            if (data.success) {
                input.value = '';
                loadMessages();
            }
        });
    }
    
    document.getElementById('chatInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') sendMessage();
    });
    
    loadMessages();
    setInterval(loadMessages, 5000);
    </script>
    <?php endif; ?>
</body>
</html>
