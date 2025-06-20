<?php
header('Content-Type: application/json');

$id_composant_volet = 5;

// --- Connexion BDD ---
$host = 'romantcham.fr'; 
$dbname = 'Domotic_db'; 
$user = 'G7D'; 
$password = 'rgnefb';
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    // Si la connexion échoue, on renvoie une erreur claire.
    http_response_code(500);
    die(json_encode(['etat_texte' => 'Erreur BDD']));
}

// On prépare une réponse par défaut au cas où aucune mesure ne serait trouvée.
$reponse = ['etat_texte' => 'Inconnu'];

// On exécute la requête pour LIRE la dernière valeur du bon composant.
$stmt = $conn->prepare("SELECT valeur FROM mesure WHERE id_composant = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("i", $id_composant_volet);
$stmt->execute();
$result = $stmt->get_result();


// S'il trouve une mesure...
if ($mesure = $result->fetch_assoc()) {
    $valeur = floatval($mesure['valeur']);
    // On traduit la valeur 0 ou 1 en texte.
    $reponse['etat_texte'] = ($valeur == 1.0) ? 'Fermés' : 'Ouverts';
    // Ajoute la valeur brute aussi dans la réponse pour debug
    $reponse['valeur_brute'] = $valeur;
}


$stmt->close();
$conn->close();

// On renvoie la réponse en JSON au JavaScript.
echo json_encode($reponse);
?>