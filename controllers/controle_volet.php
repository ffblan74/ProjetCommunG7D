<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// --- ID du composant "Moteur Volet" (à vérifier dans votre BDD) ---
$id_composant_volet = 5; 

// --- Vérifier si la commande est reçue ---
if (isset($_POST['commande'])) {
    $commande = floatval($_POST['commande']); // 0.0 pour ouvert, 1.0 pour fermé

    // --- Connexion BDD ---
    $host = 'romantcham.fr'; $dbname = 'Domotic_db'; $user = 'G7D'; $password = 'rgnefb';
    $conn = new mysqli($host, $user, $password, $dbname);
    if ($conn->connect_error) {
        // En cas d'erreur, on arrête tout et on renvoie un message clair
        http_response_code(500); // Erreur serveur
        die(json_encode(['status' => 'erreur', 'message' => 'Erreur de connexion à la base de données.']));
    }

    // --- Insertion de la nouvelle mesure ---
    $stmt = $conn->prepare("INSERT INTO mesure (id_composant, valeur, date) VALUES (?, ?, NOW())");
    
    $stmt->bind_param("id", $id_composant_volet, $commande);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'succes', 'message' => 'Commande volet enregistrée avec la valeur ' . $commande]);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'erreur', 'message' => 'Échec de l\'exécution de la requête.']);
    }
    $stmt->close();
    $conn->close();
} else {
    http_response_code(400); // Mauvaise requête
    echo json_encode(['status' => 'erreur', 'message' => 'Aucune commande reçue.']);
}
?>