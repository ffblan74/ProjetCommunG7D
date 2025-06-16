<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'models/UserModel.php';
require_once 'models/EventModel.php';
require_once 'models/Database.php';

class DashboardController {
    private $userModel;
    private $eventModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->eventModel = new EventModel();
    }

    public function handleRequest() {
        // Vérifier si l'utilisateur est connecté et est un organisateur
        if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'participant') {
            header('Location: /?page=login');
            exit;
        }

        $action = $_GET['action'] ?? 'view';
        
        switch ($action) {
            case 'view':
                $this->showDashboard();
                break;
            case 'update_event_status':
                $this->updateEventStatus();
                break;
            case 'manage_participants':
                $this->manageParticipants();
                break;
            case 'update_profile':
                $this->updateProfile();
                break;
            case 'update_password':
                $this->updatePassword();
                break;
            case 'update_event':
                $this->updateEvent();
                break;
            default:
                $this->showDashboard();
        }
    }

    private function showDashboard() {
        $userData = $this->userModel->getUserData($_SESSION['email']);
        $view = $_GET['view'] ?? 'overview';
        $currentPage = $view;

        // Données communes
        $data = [
            'userData' => $userData,
            'currentPage' => $currentPage
        ];

        // Données spécifiques selon la vue
        switch ($view) {
            case 'overview':
                $events = $this->eventModel->getEventsByOrganizer($_SESSION['user_id']);
                $pendingRequests = $this->eventModel->getPendingParticipationRequests($_SESSION['user_id']);
                $data['stats'] = $this->getStats($_SESSION['user_id']);
                $contentFile = 'views/dashboard/overview.php';
                break;

            case 'events':
                $data['events'] = $this->eventModel->getEventsByOrganizer($_SESSION['user_id']);
                $contentFile = 'views/dashboard/events.php';
                break;

            case 'requests':
                $data['pendingRequests'] = $this->eventModel->getPendingParticipationRequests($_SESSION['user_id']);
                $contentFile = 'views/dashboard/requests.php';
                break;

            case 'profile':
                $contentFile = 'views/dashboard/profile.php';
                break;

            case 'edit_event':
                if (!isset($_GET['id'])) {
                    header('Location: /src/?page=dashboard&view=events');
                    exit;
                }
                $eventId = $_GET['id'];
                $event = $this->eventModel->getEventById($eventId);
                
                // Vérifier que l'événement existe et appartient à l'organisateur
                if (!$event || $event['id_organisateur'] != $_SESSION['user_id']) {
                    header('Location: /src/?page=dashboard&view=events');
                    exit;
                }
                
                $data['event'] = $event;
                $contentFile = 'views/dashboard/edit_event.php';
                break;

            default:
                header('Location: /src/?page=dashboard&view=overview');
                exit;
        }

        // Extraire les variables pour les rendre disponibles dans la vue
        extract($data);
        
        // Charger le template principal
        require 'views/dashboard/organizer_layout.php';
    }

    private function updateEventStatus() {
        if (!isset($_POST['event_id']) || !isset($_POST['status'])) {
            echo json_encode(['success' => false, 'message' => 'Données manquantes']);
            return;
        }

        $eventId = $_POST['event_id'];
        $status = $_POST['status'];
        $result = $this->eventModel->updateEventStatus($eventId, $status);
        
        echo json_encode(['success' => $result]);
    }

    private function manageParticipants() {
        if (!isset($_POST['event_id']) || !isset($_POST['participant_id']) || !isset($_POST['action'])) {
            echo json_encode(['success' => false, 'message' => 'Données manquantes']);
            return;
        }

        $eventId = $_POST['event_id'];
        $participantId = $_POST['participant_id'];
        $action = $_POST['action']; // 'approve' ou 'reject'

        $result = $this->eventModel->handleParticipationRequest($eventId, $participantId, $action);
        
        echo json_encode(['success' => $result]);
    }

    private function updateProfile() {
        error_log("Début de updateProfile dans DashboardController");
        error_log("POST data: " . print_r($_POST, true));
        error_log("SESSION data: " . print_r($_SESSION, true));
        
        if (!isset($_SESSION['user_id'])) {
            error_log("user_id non défini dans la session");
            echo json_encode(['success' => false, 'message' => 'Session invalide']);
            return;
        }

        if (!isset($_POST['prenom']) || !isset($_POST['nom']) || !isset($_POST['email'])) {
            error_log("Données manquantes dans POST: prenom=" . isset($_POST['prenom']) . 
                     ", nom=" . isset($_POST['nom']) . 
                     ", email=" . isset($_POST['email']));
            echo json_encode(['success' => false, 'message' => 'Données manquantes']);
            return;
        }

        $userData = [
            'id' => $_SESSION['user_id'],
            'prenom' => $_POST['prenom'],
            'nom' => $_POST['nom'],
            'email' => $_POST['email'],
            'telephone' => $_POST['telephone'] ?? null
        ];

        error_log("userData préparé: " . print_r($userData, true));

        // Gestion de l'upload de la photo de profil
        if (isset($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] === UPLOAD_ERR_OK) {
            error_log("Upload de photo détecté");
            $uploadDir = 'assets/images/profile/';
            
            // Créer le répertoire s'il n'existe pas
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
                error_log("Création du répertoire: " . $uploadDir);
            }
            
            $fileExtension = strtolower(pathinfo($_FILES['photo_profil']['name'], PATHINFO_EXTENSION));
            $newFileName = uniqid() . '.' . $fileExtension;
            $uploadFile = $uploadDir . $newFileName;

            error_log("Tentative d'upload vers: " . $uploadFile);
            
            if (move_uploaded_file($_FILES['photo_profil']['tmp_name'], $uploadFile)) {
                error_log("Upload réussi");
                $userData['photo_profil'] = $newFileName;
            } else {
                error_log("Échec de l'upload. Upload error: " . $_FILES['photo_profil']['error']);
                error_log("Détails du fichier: " . print_r($_FILES['photo_profil'], true));
            }
        }

        try {
            $result = $this->userModel->updateUser($userData);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            error_log("Exception lors de la mise à jour: " . $e->getMessage());
            echo json_encode([
                'success' => false, 
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
            ]);
        }
    }

    private function updatePassword() {
        try {
            if (!isset($_POST['current_password']) || !isset($_POST['new_password']) || !isset($_POST['confirm_password'])) {
                echo json_encode(['success' => false, 'message' => 'Données manquantes']);
                return;
            }

            if ($_POST['new_password'] !== $_POST['confirm_password']) {
                echo json_encode(['success' => false, 'message' => 'Les mots de passe ne correspondent pas']);
                return;
            }

            $password = $_POST['new_password'];
            
            // Vérification de la longueur minimale
            if (strlen($password) < 8) {
                echo json_encode(['success' => false, 'message' => 'Le mot de passe doit contenir au moins 8 caractères']);
                return;
            }

            // Vérification de la présence d'au moins une lettre majuscule
            if (!preg_match('/[A-Z]/', $password)) {
                echo json_encode(['success' => false, 'message' => 'Le mot de passe doit contenir au moins une lettre majuscule']);
                return;
            }

            // Vérification de la présence d'au moins une lettre minuscule
            if (!preg_match('/[a-z]/', $password)) {
                echo json_encode(['success' => false, 'message' => 'Le mot de passe doit contenir au moins une lettre minuscule']);
                return;
            }

            // Vérification de la présence d'au moins un chiffre
            if (!preg_match('/[0-9]/', $password)) {
                echo json_encode(['success' => false, 'message' => 'Le mot de passe doit contenir au moins un chiffre']);
                return;
            }

            // Vérification de la présence d'au moins un caractère spécial
            if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
                echo json_encode(['success' => false, 'message' => 'Le mot de passe doit contenir au moins un caractère spécial (!@#$%^&*(),.?":{}|<>)']);
                return;
            }

            $result = $this->userModel->updatePassword(
                $_SESSION['user_id'],
                $_POST['current_password'],
                $_POST['new_password']
            );

            echo json_encode(['success' => true, 'message' => 'Mot de passe mis à jour avec succès']);
        } catch (Exception $e) {
            error_log("Erreur lors de la mise à jour du mot de passe: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function updateEvent() {
        if (!isset($_POST['event_id']) || !isset($_POST['nom_event']) || !isset($_POST['description']) || 
            !isset($_POST['date_planifiee']) || !isset($_POST['duree']) || !isset($_POST['adresse_event']) || 
            !isset($_POST['nb_participants']) || !isset($_POST['statut'])) {
            echo json_encode(['success' => false, 'message' => 'Données manquantes']);
            return;
        }

        $eventId = $_POST['event_id'];
        
        // Vérifier que l'événement appartient à l'organisateur
        $event = $this->eventModel->getEventById($eventId);
        if (!$event || $event['id_organisateur'] != $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'Vous n\'êtes pas autorisé à modifier cet événement']);
            return;
        }

        $eventData = [
            'nom_event' => $_POST['nom_event'],
            'description' => $_POST['description'],
            'date_planifiee' => $_POST['date_planifiee'],
            'duree' => $_POST['duree'],
            'adresse_event' => $_POST['adresse_event'],
            'capacite_max' => $_POST['nb_participants'],
            'statut' => $_POST['statut']
        ];

        $result = $this->eventModel->updateEvent($eventId, $eventData);
        echo json_encode(['success' => $result]);
    }

    private function getStats($organizerId) {
        $eventModel = new EventModel();
        $stats = [];
        
        // Statistiques de base
        $stats['total_events'] = $eventModel->getTotalEvents($organizerId);
        $stats['total_participants'] = $eventModel->getTotalParticipants($organizerId);
        $stats['pending_requests'] = $eventModel->getPendingRequestsCount($organizerId);
        $stats['upcoming_events'] = $eventModel->getUpcomingEventsCount($organizerId);
        
        // Statistiques pour le graphique en donut
        $stats['events_open'] = $eventModel->getEventCountByStatus($organizerId, 'ouvert');
        $stats['events_pending'] = $eventModel->getEventCountByStatus($organizerId, 'sous_reserve');
        $stats['events_closed'] = $eventModel->getEventCountByStatus($organizerId, 'ferme');
        
        // Statistiques pour le graphique linéaire
        $monthlyStats = $eventModel->getMonthlyParticipants($organizerId);
        $stats['months'] = array_map(function($item) {
            return date('M Y', strtotime($item['month']));
        }, $monthlyStats);
        $stats['monthly_participants'] = array_map(function($item) {
            return $item['count'];
        }, $monthlyStats);
        
        return $stats;
    }
} 