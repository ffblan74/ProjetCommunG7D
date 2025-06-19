<?php
require_once '../database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);
    exit;
}

$setting = $_POST['setting'] ?? null;
$value = $_POST['value'] ?? null;

if (!$setting || $value === null) {
    echo json_encode(['status' => 'error', 'message' => 'Paramètres manquants']);
    exit;
}

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM accessibilite WHERE id = 1");
    $stmt->execute();
    $exists = $stmt->fetchColumn();
    
    if ($exists) {
        $stmt = $conn->prepare("UPDATE accessibilite SET $setting = :value WHERE id = 1");
    } else {
        $stmt = $conn->prepare("INSERT INTO accessibilite (id, $setting) VALUES (1, :value)");
    }
    
    $stmt->bindParam(':value', $value);
    $stmt->execute();
    
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Erreur de base de données']);
}