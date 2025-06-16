<?php
class EventController {
    private $pdo;

    // Accepter la connexion PDO dans le constructeur
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fonction pour gérer la recherche des événements
    public function handleRequest() {
        $searchResults = [];

        // Recherche si une requête est envoyée
        if (isset($_GET['query']) && !empty($_GET['query'])) {
            $searchQuery = trim($_GET['query']);
            $sql = "SELECT * FROM Evenement WHERE etat = 'Planifié' AND (nom_event LIKE :query OR adresse_event LIKE :query OR description LIKE :query)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':query', "%$searchQuery%", PDO::PARAM_STR);
            $stmt->execute();
            $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Afficher tous les événements validés si aucune recherche
            $sql = "SELECT * FROM Evenement WHERE etat = 'Planifié'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $searchResults;
    }

    public function createEvent($eventData) {
        try {
            $query = "INSERT INTO Evenement (
                nom_event, 
                nb_participants, 
                adresse_event, 
                date_planifiee, 
                description, 
                duree, 
                etat, 
                id_organisateur
            ) VALUES (
                :nom_event,
                :nb_participants,
                :adresse_event,
                :date_planifiee,
                :description,
                :duree,
                'En attente',
                :id_organisateur
            )";

            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                'nom_event' => $eventData['nom_event'],
                'nb_participants' => $eventData['nb_participants'],
                'adresse_event' => $eventData['adresse_event'],
                'date_planifiee' => $eventData['date_planifiee'],
                'description' => $eventData['description'],
                'duree' => $eventData['duree'],
                'id_organisateur' => $eventData['id_organisateur']
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("Erreur lors de la création de l'événement : " . $e->getMessage());
            return false;
        }
    }
}
?>
