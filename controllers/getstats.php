<?php
// Inclure la connexion existante
require '../config.php';

try {
    // Utiliser la connexion PDO de database.php
    global $pdo;

    // Requêtes pour récupérer les statistiques
    $stmtEvents = $pdo->query('SELECT COUNT(*) AS total_events FROM Evenement');
    $totalEvents = $stmtEvents->fetch(PDO::FETCH_ASSOC)['total_events'];

    $stmtParticipants = $pdo->query('SELECT COUNT(*) AS total_participants FROM Participants');
    $totalParticipants = $stmtParticipants->fetch(PDO::FETCH_ASSOC)['total_participants'];

    // Retour des statistiques au format JSON
    echo json_encode([
        'events' => $totalEvents,
        'participants' => $totalParticipants,
        'support' => 24, // Nombre fixe (24/7 support client)
    ]);
} catch (PDOException $e) {
    // En cas d'erreur, retour d'un message explicite
    echo json_encode(['error' => 'Erreur de connexion : ' . $e->getMessage()]);
}
?>
