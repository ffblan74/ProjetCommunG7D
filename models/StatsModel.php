<?php
require_once dirname(__FILE__) . '/Database.php';

class StatModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    public function getInfo() {
        // Récupérer les informations de la base de données
        $query = "SELECT * FROM composant";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getMesure() {
        // Récupérer les mesures de la base de données
        $query = "SELECT * FROM mesure";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Récupérer la dernière mesure d'un capteur par son nom
    public function getLatestMeasurementByName($componentName) {
        $query = "
            SELECT m.valeur, m.date 
            FROM mesure m
            INNER JOIN composant c ON m.id_composant = c.id
            WHERE c.nom = :name
            ORDER BY m.date DESC
            LIMIT 1
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['name' => $componentName]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer l'état d'un actionneur par son nom
    public function getActuatorStateByName($componentName) {
        $query = "
            SELECT m.valeur 
            FROM mesure m
            INNER JOIN composant c ON m.id_composant = c.id
            WHERE c.nom = :name
            ORDER BY m.date DESC
            LIMIT 1
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['name' => $componentName]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Récupérer le nombre de mesures d'un capteur
    public function getMeasurementCountByComponent($componentName) {
        $query = "
            SELECT c.nom AS component, COUNT(m.id) AS measurement_count
            FROM composant c
            LEFT JOIN mesure m ON m.id_composant = c.id
            WHERE c.is_capteur = 1
            GROUP BY c.nom
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Construire un graphique pour les mesures
    public function getCapteurs() {
        $sql = "SELECT id, nom FROM composant";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMesures($capteurId, $intervalSql) {
        $sql = "SELECT date AS date_mesure, valeur FROM mesure 
                WHERE id_composant = :capteurId 
                AND date >= NOW() - INTERVAL $intervalSql";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['capteurId' => $capteurId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}