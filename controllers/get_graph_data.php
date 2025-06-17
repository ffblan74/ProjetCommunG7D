<?php
header('Content-Type: application/json');

require_once '../models/Database.php';
require_once '../models/StatsModel.php';

try {
    $pdo = Database::getInstance()->getConnection();
    $model = new StatModel($pdo);

    $capteurId = isset($_GET['capteur']) ? intval($_GET['capteur']) : 1;
    $periode = $_GET['periode'] ?? '24h';

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

    $mesures = $model->getMesures($capteurId, $periodes[$periode]);

    // Ajout de l'entête pour Google Charts
    $data = [["Date", "Valeur"]];

    foreach ($mesures as $row) {
        // Format ISO 8601, ex : 2025-06-12T11:48:00
        $dateIso = date('Y-m-d\TH:i:s', strtotime($row['date_mesure']));
        $valeur = (float)$row['valeur'];

        $data[] = [$dateIso, $valeur];
    }

    echo json_encode($data);

} catch (Exception $e) {
    echo json_encode(["error" => "Erreur serveur"]);
}
