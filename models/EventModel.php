<?php
class EventModel {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = Database::getInstance()->getConnection();
        } catch (Exception $e) {
            throw new Exception("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }

    public function getEventsByOrganizer($organizerId) {
        $query = "SELECT e.*,
                    COUNT(DISTINCT CASE WHEN pe.statut = 'approuvé' THEN pe.id_participant END) as participants_approuves,
                    COUNT(DISTINCT CASE WHEN pe.statut = 'en_attente' THEN pe.id_participant END) as participants_en_attente,
                    e.nb_participants as capacite_max,
                    e.etat
                 FROM Evenement e 
                 LEFT JOIN Participants_Evenement pe ON e.id_event = pe.id_event
                 WHERE e.id_organisateur = :organizerId 
                 GROUP BY e.id_event
                 ORDER BY e.date_planifiee DESC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['organizerId' => $organizerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPendingParticipationRequests($organizerId) {
        $query = "SELECT pe.*, e.nom_event, p.nom_participant, p.prenom
                 FROM Participants_Evenement pe
                 JOIN Evenement e ON pe.id_event = e.id_event
                 JOIN Participants p ON pe.id_participant = p.id_participant
                 WHERE e.id_organisateur = :organizerId 
                 AND pe.statut = 'en_attente'
                 ORDER BY pe.date_inscription DESC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['organizerId' => $organizerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalParticipants($organizerId) {
        $query = "SELECT COUNT(DISTINCT pe.id_participant) as total
                 FROM Participants_Evenement pe
                 JOIN Evenement e ON pe.id_event = e.id_event
                 WHERE e.id_organisateur = :organizerId 
                 AND pe.statut = 'approuvé'";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['organizerId' => $organizerId]);
        return $stmt->fetchColumn();
    }

    public function getUpcomingEventsCount($organizerId) {
        $query = "SELECT COUNT(*) FROM Evenement 
                 WHERE id_organisateur = :organizerId 
                 AND date_planifiee > NOW()";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['organizerId' => $organizerId]);
        return $stmt->fetchColumn();
    }

    public function updateEventStatus($eventId, $status) {
        // Vérifier que le status est valide
        if (!in_array($status, ['ouvert', 'sous_reserve', 'ferme'])) {
            return false;
        }

        $query = "UPDATE Evenement 
                 SET statut = :status 
                 WHERE id_event = :eventId";
        
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            'status' => $status,
            'eventId' => $eventId
        ]);
    }

    public function handleParticipationRequest($eventId, $participantId, $action) {
        // Vérifier que l'action est valide
        if (!in_array($action, ['approve', 'reject'])) {
            return false;
        }

        $status = ($action === 'approve') ? 'approuvé' : 'refusé';
        
        $query = "UPDATE Participants_Evenement 
                 SET statut = :status,
                     date_modification = NOW()
                 WHERE id_event = :eventId 
                 AND id_participant = :participantId";
        
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            'status' => $status,
            'eventId' => $eventId,
            'participantId' => $participantId
        ]);
    }

    public function registerForEvent($eventId, $participantId) {
        // Vérifier d'abord le statut de l'événement
        $event = $this->getEventById($eventId);
        if (!$event) {
            return ['success' => false, 'message' => 'Événement non trouvé'];
        }

        // Vérifier si l'événement est complet
        if ($event['nb_participants'] >= $event['capacite_max']) {
            return ['success' => false, 'message' => 'Événement complet'];
        }

        // Déterminer le statut initial selon le type d'événement
        $initialStatus = ($event['statut'] === 'sous_reserve') ? 'en_attente' : 'approuvé';

        try {
            $query = "INSERT INTO Participants_Evenement 
                     (id_event, id_participant, statut, date_inscription) 
                     VALUES (:eventId, :participantId, :status, NOW())";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                'eventId' => $eventId,
                'participantId' => $participantId,
                'status' => $initialStatus
            ]);

            // Mettre à jour le nombre de participants
            $updateQuery = "UPDATE Evenement 
                          SET nb_participants = nb_participants + 1 
                          WHERE id_event = :eventId";
            $updateStmt = $this->pdo->prepare($updateQuery);
            $updateStmt->execute(['eventId' => $eventId]);

            return [
                'success' => true,
                'message' => ($initialStatus === 'en_attente') 
                    ? 'Inscription en attente d\'approbation' 
                    : 'Inscription confirmée'
            ];
        } catch (PDOException $e) {
            error_log("Erreur SQL lors de l'inscription: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de l\'inscription: ' . $e->getMessage()];
        }
    }

    public function getEventById($eventId) {
        $query = "SELECT e.*, o.id_organisateur, COUNT(p.id_participant) as participants_approuves,
                 (SELECT COUNT(*) FROM Participants_Evenement WHERE id_event = e.id_event AND statut = 'en_attente') as participants_en_attente
                 FROM Evenement e
                 LEFT JOIN Organisateur o ON e.id_organisateur = o.id_organisateur
                 LEFT JOIN Participants_Evenement p ON e.id_event = p.id_event AND p.statut = 'approuvé'
                 WHERE e.id_event = ?
                 GROUP BY e.id_event";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$eventId]);
        return $stmt->fetch();
    }

    public function cancelRegistration($eventId, $participantId) {
        $query = "DELETE FROM Participants_Evenement 
                 WHERE id_event = :eventId 
                 AND id_participant = :participantId";
        
        $stmt = $this->pdo->prepare($query);
        $success = $stmt->execute([
            'eventId' => $eventId,
            'participantId' => $participantId
        ]);

        if ($success) {
            // Mettre à jour le nombre de participants
            $updateQuery = "UPDATE Evenement 
                          SET nb_participants = nb_participants - 1 
                          WHERE id_event = :eventId";
            $updateStmt = $this->pdo->prepare($updateQuery);
            $updateStmt->execute(['eventId' => $eventId]);
        }

        return $success;
    }

    public function getRegistrationStatus($eventId, $participantId) {
        $query = "SELECT statut 
                 FROM Participants_Evenement 
                 WHERE id_event = :eventId 
                 AND id_participant = :participantId";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'eventId' => $eventId,
            'participantId' => $participantId
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['statut'] : null;
    }

    public function updateEvent($eventId, $eventData) {
        $query = "UPDATE Evenement SET 
            nom_event = ?,
            description = ?,
            date_planifiee = ?,
            duree = ?,
            adresse_event = ?,
            capacite_max = ?,
            statut = ?
            WHERE id_event = ?";
        
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            $eventData['nom_event'],
            $eventData['description'],
            $eventData['date_planifiee'],
            $eventData['duree'],
            $eventData['adresse_event'],
            $eventData['capacite_max'],
            $eventData['statut'],
            $eventId
        ]);
    }

    public function getTotalEvents($organizerId) {
        $query = "SELECT COUNT(*) FROM Evenement WHERE id_organisateur = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$organizerId]);
        return $stmt->fetchColumn();
    }

    public function getPendingRequestsCount($organizerId) {
        $query = "SELECT COUNT(*) FROM Participants_Evenement pe
                 JOIN Evenement e ON pe.id_event = e.id_event
                 WHERE e.id_organisateur = ? AND pe.statut = 'en_attente'";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$organizerId]);
        return $stmt->fetchColumn();
    }

    public function getEventCountByStatus($organizerId, $status) {
        $query = "SELECT COUNT(*) FROM Evenement 
                 WHERE id_organisateur = ? AND statut = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$organizerId, $status]);
        return $stmt->fetchColumn();
    }

    public function getMonthlyParticipants($organizerId) {
        $query = "SELECT 
                    DATE_FORMAT(pe.date_inscription, '%Y-%m') as month,
                    COUNT(DISTINCT pe.id_participant) as count
                 FROM Participants_Evenement pe
                 JOIN Evenement e ON pe.id_event = e.id_event
                 WHERE e.id_organisateur = ?
                    AND pe.statut = 'approuvé'
                    AND pe.date_inscription >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                 GROUP BY month
                 ORDER BY month ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$organizerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateUser($userId, $userData) {
        $query = "UPDATE Utilisateur SET 
                    prenom = ?,
                    nom = ?,
                    email = ?,
                    telephone = ?,
                    photo_profil = COALESCE(?, photo_profil)
                 WHERE id_utilisateur = ?";
        
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            $userData['prenom'],
            $userData['nom'],
            $userData['email'],
            $userData['telephone'],
            $userData['photo_profil'] ?? null,
            $userId
        ]);
    }

    public function updatePassword($userId, $newPassword) {
        $query = "UPDATE Utilisateur SET 
                    mot_de_passe = ?
                 WHERE id_utilisateur = ?";
        
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            password_hash($newPassword, PASSWORD_DEFAULT),
            $userId
        ]);
    }
} 