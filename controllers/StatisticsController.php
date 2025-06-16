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
        $measurements = $this->sensorModel->getMesure();
        // Récupérer le nombre de mesures pour chaque capteur
        $measurementCounts = $this->sensorModel->getMeasurementCountByComponent('température');
        $measurementCounts = array_column($measurementCounts, 'measurement_count', 'component');
        // Récupérer les données des capteurs
        $temperatureInterior = $this->sensorModel->getLatestMeasurementByName('température');
        $lightSensor = $this->sensorModel->getLatestMeasurementByName('lumière');
        $lightSwitch = $this->sensorModel->getActuatorStateByName('servo');
        $shutterMotor = $this->sensorModel->getActuatorStateByName('moteur');

        // Récupérer les prévisions météo
        // $weatherForecast = $this->weatherModel->getDailyForecast();

        // Charger la vue
        require 'views/statistiques.php';
    }
}