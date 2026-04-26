<?php
/**
 * Panenly - Logout Handler
 */
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

logoutUser();

header('Location: ' . BASE_URL . '/login.php');
exit;
