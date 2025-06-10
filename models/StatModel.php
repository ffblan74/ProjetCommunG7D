<?php
require_once dirname(__FILE__) . '/Database.php';

// StatModel.php

function getStats() {
    try {
        $pdo = Database::getInstance()->getConnection();
        
        // Requêtes pour récupérer les statistiques
        $stmtEvents = $pdo->query('SELECT COUNT(*) AS total_events FROM Evenement');
        $totalEvents = $stmtEvents->fetch(PDO::FETCH_ASSOC)['total_events'];

        $stmtParticipants = $pdo->query('SELECT COUNT(*) AS total_participants FROM Participants');
        $totalParticipants = $stmtParticipants->fetch(PDO::FETCH_ASSOC)['total_participants'];

        // Retour des statistiques sous forme de tableau associatif
        return [
            'events' => $totalEvents,
            'participants' => $totalParticipants,
            'support' => 24, // Nombre fixe (24/7 support client)
        ];
    } catch (Exception $e) {
        error_log("Erreur dans getStats: " . $e->getMessage());
        return [
            'events' => 0,
            'participants' => 0,
            'support' => 24,
            'error' => 'Erreur de récupération des données'
        ];
    }
}
?>