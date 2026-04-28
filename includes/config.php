<?php
/**
 * Freshly - Database Configuration
 * Connects to XAMPP MySQL (MariaDB) using PDO
 */

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't show errors to users
ini_set('log_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database credentials (XAMPP default)
define('DB_HOST', 'localhost');
define('DB_NAME', 'freshly');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_PORT', 3306);

// Base URL - adjust if your project is in a subdirectory
define('BASE_URL', '/Agri');

// Upload directory
define('UPLOAD_DIR', __DIR__ . '/../uploads/products/');
define('UPLOAD_URL', BASE_URL . '/uploads/products/');

// Max upload size (5MB)
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);

// Set PHP upload limits
@ini_set('upload_max_filesize', '5M');
@ini_set('post_max_size', '10M');

// Allowed image types
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

/**
 * Get PDO database connection (singleton)
 */
function getDB(): PDO {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // Log error and show friendly message
            error_log("Database connection failed: " . $e->getMessage());
            die('<div style="padding:2rem;text-align:center;font-family:Inter,sans-serif;">
                <h2>⚠️ Database Error</h2>
                <p>Tidak dapat terhubung ke database. Pastikan XAMPP MySQL sudah berjalan.</p>
                <code style="color:#ef4444;">sudo /opt/lampp/lampp startmysql</code>
            </div>');
        }
    }
    
    return $pdo;
}
