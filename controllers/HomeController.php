<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/Database.php';

// On initialise le tableau de stats uniquement avec ce qui nous est utile
$stats = [
    'utilisateurs' => 0,
    'capteurs'     => 0,
    'support'      => 24       // Valeur statique
];

try {
    $pdo = Database::getInstance()->getConnection();

    // --- 1. Calcul du nombre d'utilisateurs ---
    $queryUtilisateurs = "SELECT COUNT(*) FROM utilisateur";
    $stmtUtilisateurs = $pdo->query($queryUtilisateurs);
    $stats['utilisateurs'] = $stmtUtilisateurs->fetchColumn();

    // --- 2. Calcul du nombre de capteurs ---
    $queryCapteurs = "SELECT COUNT(*) FROM composant WHERE is_capteur = TRUE";
    $stmtCapteurs = $pdo->query($queryCapteurs);
    $stats['capteurs'] = $stmtCapteurs->fetchColumn();

} catch (Exception $e) {
    // En cas d'erreur, on l'enregistre dans les logs du serveur.
    // Pour le débogage, vous pouvez remplacer par : die("Erreur: " . $e->getMessage());
    error_log("Erreur dans HomeController: " . $e->getMessage());
}

// La vue reçoit le tableau $stats avec les bonnes données.
require_once __DIR__ . '/../views/Home.php';
?>