<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'models/Database.php';

class EventRegistrationController {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = Database::getInstance()->getConnection();
        } catch (Exception $e) {
            $this->sendJsonResponse(false, "Erreur de connexion à la base de données");
            exit;
        }
    }

    public function handleRequest() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
            $this->sendJsonResponse(false, "Vous devez être connecté pour vous inscrire");
            exit;
        }

        // Vérifier que l'utilisateur est bien un participant
        if ($_SESSION['role'] !== 'participant') {
            $this->sendJsonResponse(false, "Seuls les participants peuvent s'inscrire aux événements");
            exit;
        }

        if (!isset($_POST['event_id'])) {
            $this->sendJsonResponse(false, "ID de l'événement manquant");
            exit;
        }

        $eventId = (int)$_POST['event_id'];
        $userId = $_SESSION['user_id'];
        $action = isset($_GET['action']) ? $_GET['action'] : '';

        try {
            switch ($action) {
                case 'register':
                    $this->registerForEvent($eventId, $userId);
                    break;
                case 'cancel':
                    $this->cancelRegistration($eventId, $userId);
                    break;
                default:
                    $this->sendJsonResponse(false, "Action non valide");
                    break;
            }
        } catch (Exception $e) {
            $this->sendJsonResponse(false, "Une erreur est survenue: " . $e->getMessage());
        }
    }

    private function registerForEvent($eventId, $userId) {
        try {
            // Vérifier si l'événement existe et s'il y a des places disponibles
            $stmt = $this->pdo->prepare("
                SELECT e.*, 
                       (SELECT COUNT(*) FROM Participants_Evenement WHERE id_event = e.id_event) as nombre_inscrits
                FROM Evenement e 
                WHERE e.id_event = ?
            ");
            $stmt->execute([$eventId]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$event) {
                $this->sendJsonResponse(false, "Événement non trouvé");
                return;
            }

            // Vérifier si l'utilisateur n'est pas déjà inscrit
            $stmt = $this->pdo->prepare("
                SELECT * FROM Participants_Evenement 
                WHERE id_event = ? AND id_participant = ?
            ");
            $stmt->execute([$eventId, $userId]);
            if ($stmt->fetch()) {
                $this->sendJsonResponse(false, "Vous êtes déjà inscrit à cet événement");
                return;
            }

            // Vérifier s'il reste des places
            if ($event['nombre_inscrits'] >= $event['nb_participants']) {
                $this->sendJsonResponse(false, "Désolé, l'événement est complet");
                return;
            }

            // Déterminer le statut de l'inscription
            $status = $event['statut'] === 'sous_reserve' ? 'en_attente' : 'approuvé';

            // Inscrire l'utilisateur
            $stmt = $this->pdo->prepare("
                INSERT INTO Participants_Evenement (id_event, id_participant, statut, date_inscription) 
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$eventId, $userId, $status]);

            $message = $status === 'en_attente' 
                ? "Votre demande de participation a été enregistrée et est en attente d'approbation"
                : "Inscription confirmée";

            $this->sendJsonResponse(true, $message);
        } catch (Exception $e) {
            $this->sendJsonResponse(false, "Erreur lors de l'inscription: " . $e->getMessage());
        }
    }

    private function cancelRegistration($eventId, $userId) {
        try {
            // Vérifier si l'inscription existe
            $stmt = $this->pdo->prepare("
                SELECT * FROM Participants_Evenement 
                WHERE id_event = ? AND id_participant = ?
            ");
            $stmt->execute([$eventId, $userId]);
            if (!$stmt->fetch()) {
                $this->sendJsonResponse(false, "Vous n'êtes pas inscrit à cet événement");
                return;
            }

            // Supprimer l'inscription
            $stmt = $this->pdo->prepare("
                DELETE FROM Participants_Evenement 
                WHERE id_event = ? AND id_participant = ?
            ");
            $stmt->execute([$eventId, $userId]);

            $this->sendJsonResponse(true, "Désinscription effectuée avec succès");
        } catch (Exception $e) {
            $this->sendJsonResponse(false, "Erreur lors de la désinscription: " . $e->getMessage());
        }
    }

    private function sendJsonResponse($success, $message) {
        echo json_encode([
            'success' => $success,
            'message' => $message
        ]);
        exit;
    }
}

// Instancier et exécuter le contrôleur
$controller = new EventRegistrationController();
$controller->handleRequest(); 