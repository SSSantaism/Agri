<?php
/**
 * Freshly - Orders API
 * Handles order status updates (for sellers)
 * 
 * POST: Update order status
 */

require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Silakan masuk terlebih dahulu.']);
    exit;
}

$db = getDB();
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'update_status':
        $orderId = (int) ($_POST['order_id'] ?? 0);
        $newStatus = $_POST['status'] ?? '';
        $location = trim($_POST['location'] ?? '');
        $statusText = trim($_POST['status_text'] ?? '');
        
        $allowedStatuses = ['pending', 'processing', 'packing', 'ready_to_ship', 'shipped', 'delivered', 'completed', 'cancelled'];
        if (!in_array($newStatus, $allowedStatuses)) {
            echo json_encode(['success' => false, 'message' => 'Status tidak valid.']);
            exit;
        }
        
        // Verify the order belongs to this seller
        $stmt = $db->prepare("SELECT * FROM orders WHERE id = ? AND seller_id = ?");
        $stmt->execute([$orderId, $_SESSION['user_id']]);
        $order = $stmt->fetch();
        
        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan.']);
            exit;
        }
        
        // Update order status
        $trackingNumber = $order['tracking_number'];
        if ($newStatus === 'shipped' && empty($trackingNumber)) {
            $trackingNumber = generateTrackingNumber();
        }
        
        $stmt = $db->prepare("UPDATE orders SET status = ?, tracking_number = ? WHERE id = ?");
        $stmt->execute([$newStatus, $trackingNumber, $orderId]);
        
        // Add tracking event
        if (!empty($statusText)) {
            $stmt = $db->prepare("INSERT INTO order_tracking (order_id, status_text, location) VALUES (?, ?, ?)");
            $stmt->execute([$orderId, $statusText, $location ?: null]);
        }
        
        echo json_encode(['success' => true, 'message' => 'Status pesanan berhasil diperbarui.']);
        break;
        
    case 'complete':
        // Buyer confirms order received
        $orderId = (int) ($_POST['order_id'] ?? 0);
        
        $stmt = $db->prepare("SELECT * FROM orders WHERE id = ? AND buyer_id = ? AND status = 'delivered'");
        $stmt->execute([$orderId, $_SESSION['user_id']]);
        $order = $stmt->fetch();
        
        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan.']);
            exit;
        }
        
        $stmt = $db->prepare("UPDATE orders SET status = 'completed' WHERE id = ?");
        $stmt->execute([$orderId]);
        
        // Add tracking event
        $stmt = $db->prepare("INSERT INTO order_tracking (order_id, status_text, location) VALUES (?, 'Pesanan selesai - Diterima oleh pembeli', NULL)");
        $stmt->execute([$orderId]);
        
        echo json_encode(['success' => true, 'message' => 'Pesanan dikonfirmasi selesai.']);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak valid.']);
        break;
}
