<?php
/**
 * Panenly - Messages API
 * Handles AJAX requests for chat
 * 
 * GET: Load messages between two users (?partner_id=X)
 * POST: Send a new message
 */

require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Silakan masuk terlebih dahulu.']);
    exit;
}

$db = getDB();
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $partnerId = (int) ($_GET['partner_id'] ?? 0);
    
    if ($partnerId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Partner ID tidak valid.']);
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
    
    echo json_encode(['success' => true, 'messages' => $messages, 'current_user_id' => $userId]);
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiverId = (int) ($_POST['receiver_id'] ?? 0);
    $message = trim($_POST['message'] ?? '');
    
    if ($receiverId <= 0 || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Data tidak valid.']);
        exit;
    }
    
    $stmt = $db->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $receiverId, $message]);
    
    echo json_encode([
        'success' => true, 
        'message_id' => (int) $db->lastInsertId(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
