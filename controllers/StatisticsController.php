<?php

require_once 'models/StatsModel.php';

class StatisticsController {
    private $sensorModel;
    // private $weatherModel;

    public function __construct($db) {
        $this->sensorModel = new StatModel($db);
        // $this->weatherModel = new WeatherModel();
    }
    public function handleRequest() {
        // Afficher les noms des capteurs et actionneurs
        $components = $this->sensorModel->getInfo();
        // Récupérer les mesures
        $measurements = $this->sensorModel->getMesure();
        // Récupérer le nombre de mesures pour chaque capteur
        $measurementCounts = $this->sensorModel->getMeasurementCountByComponent('température');
        $measurementCounts = array_column($measurementCounts, 'measurement_count', 'component');
        // Récupérer les données des capteurs
        $temperature = $this->sensorModel->getLatestMeasurementByName('Capteur Température');
        $humidite = $this->sensorModel->getLatestMeasurementByName('Capteur Humidité');
        $lightSensor = $this->sensorModel->getLatestMeasurementByName('lumière');
        $lightSwitch = $this->sensorModel->getActuatorStateByName('servo');
        $shutterMotor = $this->sensorModel->getActuatorStateByName('moteur');

        // Récupérer les prévisions météo
        // $weatherForecast = $this->weatherModel->getDailyForecast();
        $sensorModel = $this->sensorModel;
        // Charger la vue
        require 'views/statistiques.php';
    }
    // Méthode pour obtenir les statistiques résumées pour la page d'accueil
    public function getSummaryStats() {
        return [
            'temperature'   => $this->sensorModel->getLatestMeasurementByName('Capteur Température')['valeur'],
            'humidite'      => $this->sensorModel->getLatestMeasurementByName('Capteur Humidité')['valeur'],
            'luminosite'    => $this->sensorModel->getLatestMeasurementByName('Capteur lumière')['valeur'],
            'etatLumiere'    => $this->sensorModel->getActuatorStateByName('servo')['valeur'],
            'etatVolets'   => $this->sensorModel->getActuatorStateByName('moteur')['valeur'],
        ];
    }
}