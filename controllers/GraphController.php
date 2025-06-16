<?php
// controller/GraphController.php
require_once 'model/StatsModel.php';

class GraphController {
    private $model;

    public function __construct($pdo) {
        $this->model = new StatModel($pdo);
    }

    public function afficherGraphique() {
        // Périodes valides
        $periodes = [
            '1h' => '1 HOUR',
            '6h' => '6 HOUR',
            '24h' => '1 DAY',
            '7j' => '7 DAY',
            '30j' => '30 DAY',
            '90j' => '90 DAY',
            '1an' => '365 DAY'
        ];

        // Récupération des valeurs GET
        $periode = $_GET['periode'] ?? '24h';
        $capteurId = $_GET['capteur'] ?? null;

        // Sécurité
        if (!array_key_exists($periode, $periodes)) {
            $periode = '24h';
        }

        $capteurs = $this->model->getCapteurs();

        // Si aucun capteur sélectionné, prendre le 1er
        if ($capteurId === null && count($capteurs) > 0) {
            $capteurId = $capteurs[0]['id'];
        }

        $mesures = $this->model->getMesures($capteurId, $periodes[$periode]);

        // Charger la vue
        require 'view/graph.php';
    }
}
