<?php
require_once __DIR__ . '/includes/helpers.php';
requireLogin();

$db = getDB();
$userId = $_SESSION['user_id'];
$currentUser = getCurrentUser();

$selectedItems = $_GET['items'] ?? '';
$inClause = '';
$params = [$userId];

if (!empty($selectedItems)) {
    $itemIds = array_map('intval', explode(',', $selectedItems));
    $itemIds = array_filter($itemIds, fn($id) => $id > 0);
    if (!empty($itemIds)) {
        $placeholders = implode(',', array_fill(0, count($itemIds), '?'));
        $inClause = " AND ci.id IN ($placeholders)";
        $params = array_merge($params, $itemIds);
    }
}

// Get cart items grouped by seller
$stmt = $db->prepare("
    SELECT ci.*, p.name, p.price, p.image_url, p.weight, p.stock, p.seller_id,
           u.store_name, u.name as farmer_name
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.id
    JOIN users u ON p.seller_id = u.id
    WHERE ci.user_id = ? $inClause
    ORDER BY p.seller_id
");
$stmt->execute($params);
$cartItems = $stmt->fetchAll();

if (empty($cartItems)) {
    setFlash('warning', 'Keranjang Anda kosong.');
    header('Location: ' . BASE_URL . '/cart.php');
    exit;
}

$subtotal = 0;
$totalItems = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
    $totalItems += $item['quantity'];
}
$shippingCost = 15000;
$total = $subtotal + $shippingCost;

// Handle checkout submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipientName = trim($_POST['recipient_name'] ?? $currentUser['name']);
    $recipientPhone = trim($_POST['recipient_phone'] ?? $currentUser['phone']);
    $shippingAddress = trim($_POST['shipping_address'] ?? $currentUser['address']);
    $paymentMethod = $_POST['payment_method'] ?? 'cod';
    
    if (empty($recipientName) || empty($recipientPhone) || empty($shippingAddress)) {
        $error = 'Lengkapi data pengiriman.';
    } else {
        try {
            $db->beginTransaction();
            
            // Group items by seller
            $itemsBySeller = [];
            foreach ($cartItems as $item) {
                $itemsBySeller[$item['seller_id']][] = $item;
            }
            
            $lastOrderId = null;
            $orderIds = [];
            foreach ($itemsBySeller as $sellerId => $items) {
                $orderSubtotal = 0;
                foreach ($items as $item) {
                    $orderSubtotal += $item['price'] * $item['quantity'];
                }
                $orderTotal = $orderSubtotal + $shippingCost;
                $orderNumber = generateOrderNumber();
                $trackingNumber = generateTrackingNumber();
                
                $stmt = $db->prepare("
                    INSERT INTO orders (order_number, buyer_id, seller_id, recipient_name, recipient_phone, shipping_address, payment_method, subtotal, shipping_cost, total, status, tracking_number)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)
                ");
                $stmt->execute([$orderNumber, $userId, $sellerId, $recipientName, $recipientPhone, $shippingAddress, $paymentMethod, $orderSubtotal, $shippingCost, $orderTotal, $trackingNumber]);
                $orderId = (int) $db->lastInsertId();
                $lastOrderId = $orderId;
                $orderIds[] = $orderId;
                
                foreach ($items as $item) {
                    $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?,?,?,?)")
                       ->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
                    // Decrease stock
                    $db->prepare("UPDATE products SET stock = stock - ?, sold_count = sold_count + ? WHERE id = ?")
                       ->execute([$item['quantity'], $item['quantity'], $item['product_id']]);
                }
                
                // Initial tracking
                $db->prepare("INSERT INTO order_tracking (order_id, status_text) VALUES (?, 'Pesanan Dibuat')")
                   ->execute([$orderId]);
            }
            
            // Clear checked out items from cart
            $cartItemIds = array_column($cartItems, 'id');
            if (!empty($cartItemIds)) {
                $placeholders = implode(',', array_fill(0, count($cartItemIds), '?'));
                $delParams = array_merge([$userId], $cartItemIds);
                $db->prepare("DELETE FROM cart_items WHERE user_id = ? AND id IN ($placeholders)")->execute($delParams);
            }
            
            $db->commit();
            
            // If multiple orders (multiple sellers), redirect to history
            if (count($orderIds) > 1) {
                setFlash('success', count($orderIds) . ' pesanan berhasil dibuat dari ' . count($orderIds) . ' toko berbeda! Lacak masing-masing pesanan di bawah.');
                header('Location: ' . BASE_URL . '/buyer/history.php');
            } else {
                setFlash('success', 'Pesanan berhasil dibuat!');
                header('Location: ' . BASE_URL . '/tracking.php?order_id=' . $lastOrderId);
            }
            exit;
        } catch (Exception $e) {
            $db->rollBack();
            error_log("Checkout error: " . $e->getMessage());
            $error = 'Terjadi kesalahan saat memproses pesanan.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Freshly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .cart-section { padding:2rem 0; display:flex; gap:2rem; margin-bottom:4rem; }
        .cart-items { flex:2; }
        .cart-summary { flex:1; background:var(--white); padding:1.5rem; border-radius:var(--radius); box-shadow:var(--shadow); height:fit-content; position:sticky; top:100px; }
        .summary-row { display:flex; justify-content:space-between; margin-bottom:1rem; color:var(--text-muted); }
        .summary-total { display:flex; justify-content:space-between; margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border-color); font-weight:700; font-size:1.2rem; }
        .payment-method { border:1px solid var(--border-color); padding:1rem; border-radius:8px; text-align:center; cursor:pointer; font-weight:600; color:var(--text-muted); transition:var(--transition); }
        .payment-method.active { border-color:var(--primary-color); background:rgba(16,185,129,0.05); color:var(--primary-color); }
        .payment-method i { font-size:1.5rem; margin-bottom:0.5rem; }
        .form-control { width:100%; padding:0.75rem; border:1px solid var(--border-color); border-radius:8px; font-family:inherit; outline:none; box-sizing:border-box; }
        .form-control:focus { border-color:var(--primary-color); }
        .error-msg { background:rgba(239,68,68,0.1); color:#ef4444; padding:0.75rem; border-radius:8px; margin-bottom:1rem; text-align:center; }
    </style>
</head>
<body>
    <?php $navbarType = 'simple'; include __DIR__ . '/includes/navbar.php'; ?>
    <div style="padding:0 5%;max-width:1200px;margin:0 auto;">
        <h2 style="margin-top:2rem;">Checkout</h2>
        <?php if(!empty($error)): ?><div class="error-msg"><?= sanitize($error) ?></div><?php endif; ?>
        <form method="POST">
        <div class="cart-section">
            <div class="cart-items">
                <div style="background:var(--white);padding:1.5rem;border-radius:var(--radius);box-shadow:var(--shadow);margin-bottom:1.5rem;">
                    <h3 style="margin-bottom:1rem;">Alamat Pengiriman</h3>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                        <div>
                            <label style="font-size:0.85rem;font-weight:500;display:block;margin-bottom:0.25rem;">Nama Penerima</label>
                            <input type="text" name="recipient_name" class="form-control" value="<?= sanitize($currentUser['name']??'') ?>" required>
                        </div>
                        <div>
                            <label style="font-size:0.85rem;font-weight:500;display:block;margin-bottom:0.25rem;">No. HP</label>
                            <input type="text" name="recipient_phone" class="form-control" value="<?= sanitize($currentUser['phone']??'') ?>" required>
                        </div>
                    </div>
                    <label style="font-size:0.85rem;font-weight:500;display:block;margin-bottom:0.25rem;">Alamat Lengkap</label>
                    <textarea name="shipping_address" class="form-control" rows="2" required><?= sanitize($currentUser['address']??'') ?></textarea>
                </div>
                
                <div style="background:var(--white);padding:1.5rem;border-radius:var(--radius);box-shadow:var(--shadow);margin-bottom:1.5rem;">
                    <h3 style="margin-bottom:1rem;">Barang yang Dibeli</h3>
                    <?php foreach($cartItems as $item): ?>
                    <div style="display:flex;gap:1rem;align-items:center;<?= $item !== end($cartItems)?'margin-bottom:1rem;padding-bottom:1rem;border-bottom:1px solid var(--border-color);':'' ?>">
                        <img src="<?= getProductImage($item['image_url']??'') ?>" style="width:60px;height:60px;border-radius:8px;object-fit:cover;" onerror="this.src='https://via.placeholder.com/60'">
                        <div style="flex:1;">
                            <div style="font-weight:600;"><?= sanitize($item['name']) ?></div>
                            <div style="font-size:0.85rem;color:var(--text-muted);"><?= $item['quantity'] ?> x <?= formatRupiah($item['price']) ?></div>
                        </div>
                        <div style="font-weight:600;color:var(--primary-color);"><?= formatRupiah($item['price']*$item['quantity']) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div style="background:var(--white);padding:1.5rem;border-radius:var(--radius);box-shadow:var(--shadow);">
                    <h3 style="margin-bottom:1rem;">Metode Pembayaran</h3>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;">
                        <label class="payment-method active" onclick="selectPayment(this)">
                            <input type="radio" name="payment_method" value="cod" checked style="display:none;">
                            <i class="fa-solid fa-truck"></i><br>COD
                        </label>
                        <label class="payment-method" onclick="selectPayment(this)">
                            <input type="radio" name="payment_method" value="transfer" style="display:none;">
                            <i class="fa-solid fa-credit-card"></i><br>Transfer Bank
                        </label>
                        <label class="payment-method" onclick="selectPayment(this)">
                            <input type="radio" name="payment_method" value="qris" style="display:none;">
                            <i class="fa-solid fa-qrcode"></i><br>QRIS
                        </label>
                    </div>
                </div>
            </div>
            <div class="cart-summary">
                <h3 style="margin-bottom:1.5rem;">Ringkasan Belanja</h3>
                <div class="summary-row"><span>Total Harga (<?= $totalItems ?> barang)</span><span><?= formatRupiah($subtotal) ?></span></div>
                <div class="summary-row"><span>Ongkos Kirim</span><span><?= formatRupiah($shippingCost) ?></span></div>
                <div class="summary-total"><span>Total Tagihan</span><span><?= formatRupiah($total) ?></span></div>
                <button type="submit" class="btn btn-primary" style="width:100%;margin-top:1.5rem;">Buat Pesanan</button>
            </div>
        </div>
        </form>
    </div>
    <script>
    function selectPayment(el) {
        document.querySelectorAll('.payment-method').forEach(p => p.classList.remove('active'));
        el.classList.add('active');
        el.querySelector('input[type=radio]').checked = true;
    }
    </script>
</body>
</html>
