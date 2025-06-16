<?php
// Modèle - Event.php

class Event
{
    private $pdo;

    // Connexion à la base de données
    public function __construct($host, $dbname, $username, $password)
    {
        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    // Recherche des événements
    public function searchEvents($searchQuery = null)
    {
        if ($searchQuery) {
            $sql = "SELECT * FROM Evenement WHERE nom_event LIKE :query OR adresse_event LIKE :query OR description LIKE :query";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':query', "%$searchQuery%", PDO::PARAM_STR);
        } else {
            $sql = "SELECT * FROM Evenement";
            $stmt = $this->pdo->prepare($sql);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fonction pour formater les dates
    public static function formatDate($dateString)
    {
        $date = new DateTime($dateString);
        return $date->format('d/m/y \à H\h');
    }
}


?>
