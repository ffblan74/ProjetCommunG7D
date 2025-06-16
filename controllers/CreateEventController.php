<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Assurez-vous qu'aucune sortie n'est envoyée avant l'appel à header()
ob_start(); // Commencer à capturer la sortie

// Vérification de la session et du rôle (organisateur ou administrateur)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || ($_SESSION['role'] !== 'organisateur' && $_SESSION['role'] !== 'administrateur')) {
    header('Location: /src/?page=login');
    exit;
}

require_once __DIR__ . '/../models/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    
    // Récupération et validation de la date et heure
    $date = $_POST['date_planifiee'] ?? '';
    $time = $_POST['heure_planifiee'] ?? '';
    
    if (empty($date) || empty($time)) {
        $errors[] = "La date et l'heure sont requises.";
    } else {
        // Combine la date et l'heure
        $dateTime = new DateTime($date . ' ' . $time);
        $now = new DateTime();
        
        // Vérifie si la date est dans le passé
        if ($dateTime <= $now) {
            $errors[] = "La date et l'heure de l'événement doivent être dans le futur.";
        }
    }

    // Autres validations...
    if (empty($errors)) {
        try {
            // Formatage de la date pour la base de données
            $formattedDateTime = $dateTime->format('Y-m-d H:i:s');
            
            // Création de l'événement...
            // Utilisez $formattedDateTime pour la date_planifiee
            
        } catch (Exception $e) {
            $errors[] = "Une erreur est survenue lors de la création de l'événement.";
            error_log($e->getMessage());
        }
    }

    function generateUniqueId() {
        return mt_rand(1, 2147483647);
    }

    try {
        // Utiliser la classe Database au lieu d'une connexion directe
        $db = Database::getInstance();
        $pdo = $db->getConnection();

        // Récupérer les données du formulaire
        $eventId = generateUniqueId();
        $nomEvent = $_POST['event-name'] ?? '';
        $nbParticipants = $_POST['participants'] ?? 0;
        $adresseEvent = $_POST['event-location'] ?? '';
        $description = $_POST['description'] ?? '';
        $duree = $_POST['event-duration'] ?? 0;
        
        // Gestion de l'upload d'image
        $imagePath = null;
        if (isset($_FILES['event-image']) && $_FILES['event-image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/events/';
            
            // Créer le dossier s'il n'existe pas
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Générer un nom unique pour l'image
            $fileExtension = strtolower(pathinfo($_FILES['event-image']['name'], PATHINFO_EXTENSION));
            $fileName = $eventId . '_' . time() . '.' . $fileExtension;
            $targetPath = $uploadDir . $fileName;
            
            // Vérifier le type de fichier
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($_FILES['event-image']['type'], $allowedTypes)) {
                throw new Exception("Le type de fichier n'est pas autorisé. Utilisez JPG, PNG ou GIF.");
            }
            
            // Déplacer le fichier uploadé
            if (move_uploaded_file($_FILES['event-image']['tmp_name'], $targetPath)) {
                $imagePath = '/public/uploads/events/' . $fileName;
            } else {
                throw new Exception("Erreur lors de l'upload de l'image.");
            }
        }
        
        // Traitement des exigences
        $requirements = $_POST['requirements'] ?? [];
        $requirements = array_filter($requirements, 'strlen');
        $exigences = json_encode($requirements, JSON_UNESCAPED_UNICODE);
        
        // Debug des valeurs reçues
        error_log("Données du formulaire reçues :");
        error_log("Nom : " . $nomEvent);
        error_log("Participants : " . $nbParticipants);
        error_log("Adresse : " . $adresseEvent);
        error_log("Date : " . $formattedDateTime);
        error_log("Description : " . $description);
        error_log("Durée : " . $duree);
        error_log("Exigences : " . $exigences);
        
        // Debug des données de session
        error_log("Données de session :");
        error_log("Session ID: " . session_id());
        error_log("Session status: " . session_status());
        error_log("Session data: " . print_r($_SESSION, true));
        
        // Utiliser l'ID de l'organisateur connecté
        $id_organisateur = $_SESSION['user_id'] ?? null;
        error_log("ID de l'organisateur récupéré : " . ($id_organisateur ?? 'null'));
        
        if (!$id_organisateur) {
            throw new Exception("L'ID de l'organisateur n'est pas disponible dans la session.");
        }

        // Insérer l'événement dans la base de données
        $query = "INSERT INTO Evenement (id_event, nom_event, nb_participants, adresse_event, date_planifiee, description, duree, etat, id_organisateur, exigences, image_path) 
                 VALUES (:id, :nom, :nb_participants, :adresse, :date, :description, :duree, 'En attente', :id_organisateur, :exigences, :image_path)";
        
        $stmt = $pdo->prepare($query);
        $success = $stmt->execute([
            'id' => $eventId,
            'nom' => $nomEvent,
            'nb_participants' => $nbParticipants,
            'adresse' => $adresseEvent,
            'date' => $formattedDateTime,
            'description' => $description,
            'duree' => $duree,
            'id_organisateur' => $id_organisateur,
            'exigences' => $exigences,
            'image_path' => $imagePath
        ]);

        if ($success) {
            header('Location: /src/?page=create_event&success=true');
        } else {
            throw new Exception("Erreur lors de l'insertion dans la base de données.");
        }
        exit;
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Erreur lors de la création de l'événement : " . $e->getMessage();
        header('Location: /src/?page=create_event&error=true');
        exit;
    }
}

// Inclure la vue à la fin
require_once __DIR__ . '/../views/create_event.php';
ob_end_flush();
?>
