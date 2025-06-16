<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure la vue
require __DIR__ . '/../views/dashboard.php';
?>
