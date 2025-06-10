<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO(
        "mysql:host=herogu.garageisep.com;dbname=MWbQLlb4xO_plan_it;charset=utf8",
        "4RyHVlPLcU_plan_it",
        "NgBH69FRGlIomlni"
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion réussie à la base de données!";
    
    // Test simple query
    $stmt = $pdo->query("SELECT 1");
    echo "\nTest de requête réussi!";
    
} catch (PDOException $e) {
    echo "Erreur de connexion détaillée : " . $e->getMessage();
}
?> 