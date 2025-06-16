<?php
class ExploreController {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = Database::getInstance()->getConnection();
        } catch (Exception $e) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }

    public function handleRequest() {
        try {
            // Récupérer les filtres
            $filters = [
                'localisation' => isset($_GET['localisation']) ? $_GET['localisation'] : '',
                'date' => isset($_GET['date']) ? $_GET['date'] : '',
                'participants' => isset($_GET['participants']) ? (int)$_GET['participants'] : null
            ];

            // Construction de la requête de base
            $sql = "SELECT e.*, o.nom_organisateur, o.prenom 
                   FROM Evenement e 
                   LEFT JOIN Organisateur o ON e.id_organisateur = o.id_organisateur 
                   WHERE (e.etat = 'Planifié' OR e.statut = 'sous_reserve')";
            $params = [];

            // Ajouter la condition de recherche si présente
            if (isset($_GET['query']) && !empty($_GET['query'])) {
                $sql .= " AND (e.nom_event LIKE :query 
                        OR e.adresse_event LIKE :query 
                        OR e.description LIKE :query)";
                $params['query'] = "%" . trim($_GET['query']) . "%";
            }

            // Ajouter les conditions selon les filtres
            if (!empty($filters['localisation'])) {
                $sql .= " AND e.adresse_event LIKE :localisation";
                $params['localisation'] = "%" . $filters['localisation'] . "%";
            }

            if (!empty($filters['date'])) {
                $sql .= " AND DATE(e.date_planifiee) = :date";
                $params['date'] = $filters['date'];
            }

            if (!empty($filters['participants'])) {
                $sql .= " AND e.nb_participants <= :participants";
                $params['participants'] = $filters['participants'];
            }

            // Trier par date
            $sql .= " ORDER BY e.date_planifiee ASC";

            // Préparer et exécuter la requête
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Récupérer les localisations uniques pour le filtre
            $localisationStmt = $this->pdo->query("SELECT DISTINCT adresse_event FROM Evenement WHERE (etat = 'Planifié' OR statut = 'sous_reserve') AND adresse_event IS NOT NULL ORDER BY adresse_event");
            $localisations = $localisationStmt->fetchAll(PDO::FETCH_COLUMN);

            require_once 'views/explore.php';
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des événements: " . $e->getMessage());
            $events = [];
            $localisations = [];
            require_once 'views/explore.php';
        }
    }
} 