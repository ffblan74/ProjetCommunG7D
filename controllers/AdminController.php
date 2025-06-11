<?php
require_once dirname(__FILE__) . '/../models/Database.php';
require_once dirname(__FILE__) . '/../models/Event.php';

class AdminController {
    private $pdo;
    
    public function __construct() {
        try {
            $this->pdo = Database::getInstance()->getConnection();
        } catch (Exception $e) {
            error_log("Erreur de connexion détaillée : " . $e->getMessage());
            throw new Exception("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }

    public function handleRequest() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérification si l'utilisateur est connecté et est un administrateur
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'administrateur') {
            // Redirection vers la page d'accueil si l'utilisateur n'est pas un administrateur
            header('Location: /src/?page=home');
            exit;
        }

        $section = $_GET['section'] ?? 'dashboard';
        $action = $_GET['action'] ?? null;

        // Gérer les actions
        if ($action) {
            $id = $_GET['id'] ?? null;
            if ($id) {
                switch($action) {
                    case 'approve':
                        if ($this->approveEvent($id)) {
                            header('Location: /src/?page=admin&section=dashboard');
                            exit;
                        }
                        break;
                    case 'reject':
                        if ($this->rejectEvent($id)) {
                            header('Location: /src/?page=admin&section=dashboard');
                            exit;
                        }
                        break;
                    case 'delete':
                        if ($section === 'events') {
                            $this->deleteEvent($id);
                        } elseif ($section === 'users') {
                            $this->deleteUser($id);
                        } elseif ($section === 'organisateurs') {
                            $this->deleteOrganisateur($id);
                        }
                        break;
                    case 'update':
                        if ($section === 'users' && isset($_POST['nom'], $_POST['prenom'], $_POST['email'])) {
                            $this->updateUser($id, $_POST['nom'], $_POST['prenom'], $_POST['email']);
                        } elseif ($section === 'organisateurs' && isset($_POST['nom'], $_POST['prenom'], $_POST['email'])) {
                            $this->updateOrganisateur($id, $_POST['nom'], $_POST['prenom'], $_POST['email']);
                        }
                        break;
                }
            }
        }

        // Charger les données selon la section
        try {
            switch ($section) {
                case 'dashboard':
                    $pendingEvents = $this->getPendingEvents();
                    break;
                case 'events':
                    $events = $this->getAllEvents();
                    break;
                case 'users':
                    $users = $this->getAllUsers();
                    $organisateurs = $this->getAllOrganisateurs();
                    break;
            }
        } catch (Exception $e) {
            $error = "Erreur détaillée: " . $e->getMessage();
            error_log($error);
        }

        // Afficher la vue
        require_once dirname(__FILE__) . '/../views/admin/dashboard.php';
    }

    public function getPendingEvents() {
        try {
            $query = "SELECT e.*, o.nom_organisateur, o.prenom 
                      FROM Evenement e 
                      LEFT JOIN Organisateur o ON e.id_organisateur = o.id_organisateur 
                      WHERE e.etat = 'En attente'
                      ORDER BY e.date_planifiee ASC";
            
            error_log("Executing pending events query: " . $query);
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Found " . count($results) . " pending events");
            error_log("Results: " . print_r($results, true));
            
            return $results;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des événements en attente : " . $e->getMessage());
            error_log("Stack trace : " . $e->getTraceAsString());
            return [];
        }
    }

    public function approveEvent($eventId) {
        try {
            $query = "UPDATE Evenement SET etat = 'Planifié' WHERE id_event = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['id' => $eventId]);
            return true;
        } catch (PDOException $e) {
            error_log("Erreur lors de l'approbation de l'événement : " . $e->getMessage());
            return false;
        }
    }

    public function rejectEvent($eventId) {
        try {
            $query = "UPDATE Evenement SET etat = 'Annulé' WHERE id_event = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['id' => $eventId]);
            return true;
        } catch (PDOException $e) {
            error_log("Erreur lors du refus de l'événement : " . $e->getMessage());
            return false;
        }
    }

    private function getAllEvents() {
        try {
            $query = "
                SELECT 
                    e.*,
                    o.nom_organisateur,
                    o.prenom,
                    (SELECT COUNT(*) FROM Inscriptions WHERE id_event = e.id_event) as nombre_inscrits
                FROM Evenement e 
                LEFT JOIN Organisateur o ON e.id_organisateur = o.id_organisateur
                ORDER BY e.date_planifiee DESC
            ";
            return $this->pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur SQL: " . $e->getMessage());
        }
    }

    private function getAllUsers() {
        $query = "
            SELECT *
            FROM Participants
            ORDER BY id_participant DESC
        ";
        return $this->pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getAllOrganisateurs() {
        $query = "
            SELECT *
            FROM Organisateur
            ORDER BY id_organisateur DESC
        ";
        return $this->pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    private function deleteEvent($id) {
        $query = "DELETE FROM Evenement WHERE id_event = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$id]);
    }

    private function deleteUser($id) {
        $query = "DELETE FROM Participants WHERE id_participant = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$id]);
    }

    private function deleteOrganisateur($id) {
        $query = "DELETE FROM Organisateur WHERE id_organisateur = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$id]);
    }

    private function updateUser($id, $nom, $prenom, $email, $role = null) {
        $query = "UPDATE Participants SET nom_participant = ?, prenom = ?, email = ?, role = ? WHERE id_participant = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$nom, $prenom, $email, $role ?? 'utilisateur', $id]);
    }

    private function updateOrganisateur($id, $nom, $prenom, $email, $role = null) {
        $query = "UPDATE Organisateur SET nom_organisateur = ?, prenom = ?, email = ?, role = ? WHERE id_organisateur = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$nom, $prenom, $email, $role ?? 'organisateur', $id]);
    }

    private function updateEvent($id, $data) {
        $query = "UPDATE Evenement SET 
            nom_event = ?,
            nb_participants = ?,
            adresse_event = ?,
            date_planifiee = ?,
            description = ?,
            duree = ?,
            etat = ?
            WHERE id_event = ?";
        
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            $data['nom_event'],
            $data['nb_participants'],
            $data['adresse_event'],
            $data['date_planifiee'],
            $data['description'],
            $data['duree'],
            $data['etat'],
            $id
        ]);
    }

    // Ajouter une méthode pour vérifier les permissions
    private function checkAdminPermissions() {
        // À implémenter : vérification que l'utilisateur connecté est bien un admin
        // Cette méthode devrait être appelée avant chaque action sensible
        return true; // Pour l'instant, on retourne toujours true
    }
}

// Instancier et exécuter le contrôleur
$controller = new AdminController();
$controller->handleRequest();
?>