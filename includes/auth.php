<?php
/**
 * Freshly - Authentication Functions
 */

require_once __DIR__ . '/config.php';

/**
 * Register a new user
 * @return array ['success' => bool, 'message' => string, 'user_id' => int|null]
 */
function registerUser(string $name, string $email, string $password, string $role, ?string $phone = null, ?string $storeName = null, ?string $storeLocation = null): array {
    $db = getDB();
    
    // Check if email already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Email sudah terdaftar. Silakan gunakan email lain.', 'user_id' => null];
    }
    
    // Validate password length
    if (strlen($password) < 8) {
        return ['success' => false, 'message' => 'Kata sandi minimal 8 karakter.', 'user_id' => null];
    }
    
    // Hash password
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Determine seller_status
    $sellerStatus = ($role === 'seller') ? 'pending' : null;
    
    try {
        $stmt = $db->prepare("
            INSERT INTO users (name, email, password_hash, phone, role, seller_status, store_name, store_location) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$name, $email, $hash, $phone, $role, $sellerStatus, $storeName, $storeLocation]);
        
        $userId = (int) $db->lastInsertId();
        
        $message = ($role === 'seller') 
            ? 'Pendaftaran sebagai penjual berhasil! Menunggu persetujuan Admin.'
            : 'Pendaftaran berhasil! Silakan masuk.';
        
        return ['success' => true, 'message' => $message, 'user_id' => $userId];
    } catch (PDOException $e) {
        error_log("Registration error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.', 'user_id' => null];
    }
}

/**
 * Login user
 * @return array ['success' => bool, 'message' => string, 'user' => array|null]
 */
function loginUser(string $email, string $password): array {
    $db = getDB();
    
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($password, $user['password_hash'])) {
        return ['success' => false, 'message' => 'Email atau kata sandi salah.', 'user' => null];
    }
    
    // Check if seller is approved
    if ($user['role'] === 'seller' && $user['seller_status'] !== 'approved') {
        if ($user['seller_status'] === 'pending') {
            return ['success' => false, 'message' => 'Akun penjual Anda masih menunggu persetujuan admin.', 'user' => null];
        }
        if ($user['seller_status'] === 'rejected') {
            return ['success' => false, 'message' => 'Maaf, pendaftaran penjual Anda ditolak oleh admin.', 'user' => null];
        }
    }
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['user_name'] = $user['name'];
    
    return ['success' => true, 'message' => 'Login berhasil!', 'user' => $user];
}

/**
 * Logout user
 */
function logoutUser(): void {
    session_unset();
    session_destroy();
}
