<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/StatsModel.php';
require_once __DIR__ . '/StatisticsController.php';

class HomeController {
    public function handleRequest($pdo) {
        // On récupère les statistiques des capteurs et actionneurs
        $statsController = new StatisticsController($pdo);
        $values = $statsController->getSummaryStats();

        // On récupère les stats sur le nombre d'utilisateurs et de capteurs
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
        // Charger la vue avec les stats


        require 'views/home.php';
    }
}


?>