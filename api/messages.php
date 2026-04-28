<?php
/**
 * Freshly - Messages API
 * Handles AJAX requests for chat (point-to-point: buyer <-> seller only)
 * 
 * GET: Load messages between two users (?partner_id=X)
 * POST: Send a new message (with optional product_id context)
 */

require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Silakan masuk terlebih dahulu.']);
    exit;
}

$db = getDB();
$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $partnerId = (int) ($_GET['partner_id'] ?? 0);
    
    if ($partnerId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Partner ID tidak valid.']);
        exit;
    }
    
    // Validate point-to-point: buyer can only chat with seller and vice versa
    $stmt = $db->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$partnerId]);
    $partner = $stmt->fetch();
    
    if (!$partner) {
        echo json_encode(['success' => false, 'message' => 'Partner tidak ditemukan.']);
        exit;
    }
    
    if ($userRole === 'buyer' && $partner['role'] !== 'seller') {
        echo json_encode(['success' => false, 'message' => 'Pembeli hanya dapat mengirim pesan ke penjual.']);
        exit;
    }
    if ($userRole === 'seller' && $partner['role'] !== 'buyer') {
        echo json_encode(['success' => false, 'message' => 'Penjual hanya dapat mengirim pesan ke pembeli.']);
        exit;
    }
    
    // Mark messages as read
    $stmt = $db->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ?");
    $stmt->execute([$partnerId, $userId]);
    
    // Fetch messages between current user and partner
    $stmt = $db->prepare("
        SELECT m.*, u.name as sender_name 
        FROM messages m 
        JOIN users u ON m.sender_id = u.id
        WHERE (m.sender_id = ? AND m.receiver_id = ?)
           OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.created_at ASC
        LIMIT 100
    ");
    $stmt->execute([$userId, $partnerId, $partnerId, $userId]);
    $messages = $stmt->fetchAll();
    
    // Get product context if exists for this conversation
    $productContext = null;
    $productId = (int) ($_GET['product_id'] ?? 0);
    if ($productId > 0) {
        $stmt = $db->prepare("SELECT p.id, p.name, p.price, p.image_url, p.weight FROM products p WHERE p.id = ? AND p.is_active = 1");
        $stmt->execute([$productId]);
        $productContext = $stmt->fetch();
        if ($productContext) {
            $productContext['image_url'] = getProductImage($productContext['image_url'] ?? '');
            $productContext['price_formatted'] = formatRupiah($productContext['price']);
        }
    }
    
    // Also check if there's a product context from the first message in the conversation
    if (!$productContext) {
        $stmt = $db->prepare("
            SELECT p.id, p.name, p.price, p.image_url, p.weight 
            FROM messages m 
            JOIN products p ON m.product_id = p.id 
            WHERE ((m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?))
            AND m.product_id IS NOT NULL
            ORDER BY m.created_at ASC LIMIT 1
        ");
        $stmt->execute([$userId, $partnerId, $partnerId, $userId]);
        $productContext = $stmt->fetch();
        if ($productContext) {
            $productContext['image_url'] = getProductImage($productContext['image_url'] ?? '');
            $productContext['price_formatted'] = formatRupiah($productContext['price']);
        }
    }
    
    echo json_encode([
        'success' => true, 
        'messages' => $messages, 
        'current_user_id' => $userId,
        'product_context' => $productContext
    ]);
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiverId = (int) ($_POST['receiver_id'] ?? 0);
    $message = trim($_POST['message'] ?? '');
    $productId = (int) ($_POST['product_id'] ?? 0) ?: null;
    
    if ($receiverId <= 0 || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Data tidak valid.']);
        exit;
    }
    
    // Validate point-to-point: buyer can only chat with seller and vice versa
    $stmt = $db->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$receiverId]);
    $receiver = $stmt->fetch();
    
    if (!$receiver) {
        echo json_encode(['success' => false, 'message' => 'Penerima tidak ditemukan.']);
        exit;
    }
    
    if ($userRole === 'buyer' && $receiver['role'] !== 'seller') {
        echo json_encode(['success' => false, 'message' => 'Pembeli hanya dapat mengirim pesan ke penjual.']);
        exit;
    }
    if ($userRole === 'seller' && $receiver['role'] !== 'buyer') {
        echo json_encode(['success' => false, 'message' => 'Penjual hanya dapat mengirim pesan ke pembeli.']);
        exit;
    }
    
    $stmt = $db->prepare("INSERT INTO messages (sender_id, receiver_id, product_id, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $receiverId, $productId, $message]);
    
    echo json_encode([
        'success' => true, 
        'message_id' => (int) $db->lastInsertId(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
