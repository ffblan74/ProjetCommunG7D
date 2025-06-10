<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure les fichiers nécessaires
require_once __DIR__ . '/../config.php'; // Charger la configuration avant tout
require_once __DIR__ . '/../models/Database.php'; // Connexion à la base de données
require_once __DIR__ . '/../models/StatModel.php'; // Inclure le modèle pour les statistiques

try {
    // Récupérer les statistiques
    $stats = getStats();

    // Obtenir la connexion à la base de données
    $pdo = Database::getInstance()->getConnection();

    // Requête pour récupérer les 6 événements planifiés à venir
    $query = "
        SELECT id_event, nom_event, date_planifiee, description, image_path
        FROM Evenement
        ORDER BY date_planifiee DESC
        LIMIT 6
    ";

    // Exécution de la requête
    $stmt = $pdo->query($query);

    // Vérification si des événements sont retournés
    $events = ($stmt->rowCount() > 0) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

} catch (Exception $e) {
    error_log("Erreur dans HomeController: " . $e->getMessage());
    $events = [];
    $stats = [
        'events' => 0,
        'participants' => 0,
        'support' => 24
    ];
}

// Inclure la vue
require_once __DIR__ . '/../views/Home.php';

?>
