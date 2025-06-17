<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// On récupère la valeur du seuil envoyée en POST
$seuil = $_POST['seuil'] ?? null;

// On vérifie que c'est bien un nombre
if (is_numeric($seuil)) {
    // On le stocke dans la session
    $_SESSION['seuil_luminosite'] = floatval($seuil);
    echo json_encode(['status' => 'succes', 'message' => 'Seuil enregistré.', 'seuil' => floatval($seuil)]);
} else {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(['status' => 'erreur', 'message' => 'Valeur non numérique.']);
}
?>