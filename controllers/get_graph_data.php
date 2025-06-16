<?php
header('Content-Type: application/json');

require_once '../models/Database.php';
require_once '../models/StatsModel.php';

try {
    $pdo = Database::getInstance()->getConnection(); // Utilise ton singleton de connexion
    $model = new StatModel($pdo);

    // Paramètres GET
    $capteurId = isset($_GET['capteur']) ? intval($_GET['capteur']) : 1;
    $periode = $_GET['periode'] ?? '24h';

    // Définition des périodes valides
    $periodes = [
        '1h' => '1 HOUR',
        '6h' => '6 HOUR',
        '24h' => '1 DAY',
        '7j' => '7 DAY',
        '30j' => '30 DAY',
        '90j' => '90 DAY',
        '1an' => '365 DAY'
    ];

    if (!array_key_exists($periode, $periodes)) {
        $periode = '24h';
    }

    // Récupérer les mesures
    $mesures = $model->getMesures($capteurId, $periodes[$periode]);

    // Formatage pour Google Charts
    $data = [["Date", "Valeur"]];
    foreach ($mesures as $row) {
        $data[] = [$row['date_mesure'], (float)$row['valeur']];
    }

    echo json_encode($data);

} catch (Exception $e) {
    echo json_encode(["error" => "Erreur serveur"]);
}
