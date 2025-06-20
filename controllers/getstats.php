<?php
header('Content-Type: application/json');

// Inclure la connexion existante
require '../config.php';

try {
    // Utiliser la connexion PDO de database.php

    $pdo = new PDO(
        "mysql:host=romantcham.fr;dbname=Domotic_db;charset=utf8",
        "G7D",
        "rgnefb"
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    


    // Requêtes pour récupérer les statistiques
    $stmtCapteurs = $pdo->query('SELECT COUNT(*) AS total_capteurs FROM composant WHERE is_capteur = 1');
    $totalCapteurs = $stmtCapteurs->fetch(PDO::FETCH_ASSOC)['total_capteurs'];

    $stmtUtilisateurs = $pdo->query('SELECT COUNT(*) AS total_utilisateurs FROM utilisateur');
    $totalUtilisateurs = $stmtUtilisateurs->fetch(PDO::FETCH_ASSOC)['total_utilisateurs'];


    // Retour des statistiques au format JSON
    echo json_encode([
        'capteurs' => $totalCapteurs,
        'utilisateurs' => $totalUtilisateurs,
    ]);
} catch (PDOException $e) {
    // En cas d'erreur, retour d'un message explicite
    echo json_encode(['error' => 'Erreur de connexion : ' . $e->getMessage()]);
}
?>
