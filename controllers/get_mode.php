<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Renvoie le mode actuel, ou 'manuel' par défaut
echo json_encode(['mode' => $_SESSION['mode_lumiere'] ?? 'manuel']);
?>