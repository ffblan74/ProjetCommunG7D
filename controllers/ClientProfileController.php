<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'models/Database.php';

class ClientProfileController {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = Database::getInstance()->getConnection();
        } catch (Exception $e) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }

    public function handleRequest() {
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
            header('Location: /src/?page=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $action = $_POST['action'] ?? '';

        if ($action === 'update_profile') {
            $this->updateProfile($userId);
            return;
        } elseif ($action === 'update_password') {
            $this->updatePassword($userId);
            return;
        }

        // Récupérer les informations de l'utilisateur
        $userData = $this->getUserData($userId);
        
        // Récupérer les événements auxquels l'utilisateur est inscrit
        $userEvents = $this->getUserEvents($userId);
        
        // Compter le nombre total d'événements
        $eventCount = count($userEvents);

        require 'views/client_profil.php';
    }

    private function getUserData($userId) {
        $stmt = $this->pdo->prepare("
            SELECT 
                p.*,
                p.nom_participant as nom
            FROM Participants p
            WHERE p.id_participant = ?
        ");
        $stmt->execute([$userId]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Ajouter une date d'inscription par défaut
        $userData['date_inscription'] = date('Y-m-d H:i:s');
        
        return $userData;
    }

    private function getUserEvents($userId) {
        $stmt = $this->pdo->prepare("
            SELECT e.*, pe.statut as inscription_statut,
                   (SELECT COUNT(*) FROM Participants_Evenement WHERE id_event = e.id_event) as nombre_inscrits
            FROM Evenement e
            JOIN Participants_Evenement pe ON e.id_event = pe.id_event
            WHERE pe.id_participant = ?
            ORDER BY e.date_planifiee DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function updateProfile($userId) {
        $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
        $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

        try {
            $this->pdo->beginTransaction();

            // Mettre à jour la table Participants sans la date de naissance
            $stmt = $this->pdo->prepare("
                UPDATE Participants 
                SET nom_participant = ?, prenom = ?, email = ?
                WHERE id_participant = ?
            ");
            
            // Log des valeurs pour le débogage
            error_log("Mise à jour du profil participant - Valeurs : " . 
                     "nom=" . $nom . 
                     ", prenom=" . $prenom . 
                     ", email=" . $email . 
                     ", userId=" . $userId);
            
            $result = $stmt->execute([$nom, $prenom, $email, $userId]);
            
            if ($result) {
                $this->pdo->commit();
                echo json_encode(['success' => true, 'message' => 'Profil mis à jour avec succès']);
            } else {
                throw new Exception("Échec de la mise à jour");
            }
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Erreur lors de la mise à jour du profil participant : " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour du profil : ' . $e->getMessage()]);
        }
        exit;
    }

    private function updatePassword($userId) {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($newPassword !== $confirmPassword) {
            echo json_encode(['success' => false, 'message' => 'Les mots de passe ne correspondent pas']);
            exit;
        }

        try {
            // Vérifier l'ancien mot de passe
            $stmt = $this->pdo->prepare("SELECT mot_de_passe FROM Participants WHERE id_participant = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            if (!password_verify($currentPassword, $user['mot_de_passe'])) {
                echo json_encode(['success' => false, 'message' => 'Mot de passe actuel incorrect']);
                exit;
            }

            // Mettre à jour le mot de passe
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("UPDATE Participants SET mot_de_passe = ? WHERE id_participant = ?");
            $stmt->execute([$hashedPassword, $userId]);

            echo json_encode(['success' => true, 'message' => 'Mot de passe mis à jour avec succès']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour du mot de passe']);
        }
        exit;
    }
}

// Instancier et exécuter le contrôleur
$controller = new ClientProfileController();
$controller->handleRequest();
