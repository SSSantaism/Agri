<?php
/**
 * Freshly - Cart API
 * Handles AJAX requests for cart operations
 * 
 * POST actions:
 *   - add: Add product to cart
 *   - update: Update quantity
 *   - remove: Remove item from cart
 */

require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Silakan masuk terlebih dahulu.', 'redirect' => BASE_URL . '/login.php']);
    exit;
}

$action = $_POST['action'] ?? '';
$db = getDB();

switch ($action) {
    case 'add':
        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 1);
        
        if ($productId <= 0 || $quantity <= 0) {
            echo json_encode(['success' => false, 'message' => 'Data tidak valid.']);
            exit;
        }
        
        // Check product exists and has stock
        $stmt = $db->prepare("SELECT id, stock, name FROM products WHERE id = ? AND is_active = 1");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan.']);
            exit;
        }
        
        if ($product['stock'] < $quantity) {
            echo json_encode(['success' => false, 'message' => 'Stok tidak mencukupi.']);
            exit;
        }
        
        // Insert or update cart item (ON DUPLICATE KEY UPDATE)
        $stmt = $db->prepare("
            INSERT INTO cart_items (user_id, product_id, quantity) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
        ");
        $stmt->execute([$_SESSION['user_id'], $productId, $quantity]);
        
        $cartCount = getCartCount();
        echo json_encode([
            'success' => true, 
            'message' => $product['name'] . ' ditambahkan ke keranjang!',
            'cart_count' => $cartCount
        ]);
        break;
        
    case 'update':
        $cartId = (int) ($_POST['cart_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 1);
        
        if ($cartId <= 0 || $quantity < 0) {
            echo json_encode(['success' => false, 'message' => 'Data tidak valid.']);
            exit;
        }
        
        if ($quantity === 0) {
            // Remove item
            $stmt = $db->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
            $stmt->execute([$cartId, $_SESSION['user_id']]);
        } else {
            // Check stock
            $stmt = $db->prepare("
                SELECT p.stock FROM cart_items ci 
                JOIN products p ON ci.product_id = p.id 
                WHERE ci.id = ? AND ci.user_id = ?
            ");
            $stmt->execute([$cartId, $_SESSION['user_id']]);
            $item = $stmt->fetch();
            
            if ($item && $quantity > $item['stock']) {
                echo json_encode(['success' => false, 'message' => 'Stok tidak mencukupi.']);
                exit;
            }
            
            $stmt = $db->prepare("UPDATE cart_items SET quantity = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$quantity, $cartId, $_SESSION['user_id']]);
        }
        
        $cartCount = getCartCount();
        echo json_encode(['success' => true, 'cart_count' => $cartCount]);
        break;
        
    case 'remove':
        $cartId = (int) ($_POST['cart_id'] ?? 0);
        
        $stmt = $db->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
        $stmt->execute([$cartId, $_SESSION['user_id']]);
        
        $cartCount = getCartCount();
        echo json_encode(['success' => true, 'message' => 'Item dihapus dari keranjang.', 'cart_count' => $cartCount]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak valid.']);
        break;
}
