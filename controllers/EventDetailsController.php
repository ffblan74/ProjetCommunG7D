<?php
class EventDetailsController {
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
            if (!isset($_GET['id'])) {
                header('Location: /src/?page=explorer');
                exit;
            }

            // Vérifier si l'utilisateur est connecté
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
            $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'administrateur';
            $isOrganizer = isset($_SESSION['role']) && ($_SESSION['role'] === 'organisateur' || $_SESSION['role'] === 'administrateur');

            $eventId = (int)$_GET['id'];
            
            // Récupérer les détails de l'événement
            $sql = "SELECT e.*, o.nom_organisateur, o.prenom,
                          (SELECT COUNT(*) FROM Participants_Evenement WHERE id_event = e.id_event) as nombre_inscrits,
                          e.nom_event as titre,
                          e.date_planifiee as date_event,
                          e.adresse_event as lieu,
                          e.nb_participants as places_disponibles,
                          e.statut,
                          e.image_path
                   FROM Evenement e 
                   LEFT JOIN Organisateur o ON e.id_organisateur = o.id_organisateur 
                   WHERE e.id_event = :id";
            
            // Ajouter la condition de validation sauf pour l'admin et l'organisateur de l'événement
            if (!$isAdmin) {
                $sql .= " AND (e.etat = 'Planifié'";
                if ($isOrganizer) {
                    $sql .= " OR e.id_organisateur = :organizerId";
                }
                $sql .= ")";
            }
            
            $stmt = $this->pdo->prepare($sql);
            $params = ['id' => $eventId];
            if ($isOrganizer && !$isAdmin) {
                $params['organizerId'] = $_SESSION['user_id'];
            }
            $stmt->execute($params);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$event) {
                header('Location: /src/?page=explorer');
                exit;
            }
            
            // Vérifier si l'utilisateur est déjà inscrit et récupérer le statut
            $isRegistered = false;
            $registrationStatus = null;
            if ($isLoggedIn && isset($_SESSION['user_id']) && !$isAdmin) {
                $checkRegistration = $this->pdo->prepare(
                    "SELECT statut FROM Participants_Evenement 
                     WHERE id_event = :eventId AND id_participant = :userId"
                );
                $checkRegistration->execute([
                    'eventId' => $eventId,
                    'userId' => $_SESSION['user_id']
                ]);
                $registration = $checkRegistration->fetch(PDO::FETCH_ASSOC);
                if ($registration) {
                    $isRegistered = true;
                    $registrationStatus = $registration['statut'];
                }
            }

            require_once 'views/event_details.php';
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des détails de l'événement: " . $e->getMessage());
            header('Location: /src/?page=explorer');
            exit;
        }
    }
}
