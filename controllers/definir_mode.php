<?php
// Démarrer la session pour mémoriser le mode
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer le mode envoyé par la requête POST, avec 'manuel' par défaut
$mode = $_POST['mode'] ?? 'manuel';

// Stocker le mode dans une variable de session
if (in_array($mode, ['manuel', 'automatique'])) {
    $_SESSION['mode_lumiere'] = $mode;
    echo json_encode(['status' => 'succes', 'mode' => $mode]);
} else {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(['status' => 'erreur', 'message' => 'Mode non valide.']);
}
?>