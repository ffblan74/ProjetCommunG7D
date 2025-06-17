<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// On indique que la réponse sera au format JSON
header('Content-Type: application/json');

// On prépare une réponse avec le mode ET le seuil
$reponse = [
    'mode' => $_SESSION['mode_lumiere'] ?? 'manuel',
    'seuil' => $_SESSION['seuil_luminosite'] ?? 500 // On renvoie le seuil, ou 500 par défaut
];

echo json_encode($reponse);
?>