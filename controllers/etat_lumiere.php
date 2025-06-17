<?php
// Ce script ne fait que lire et renvoyer l'état complet du système
header('Content-Type: application/json');

// --- Connexion BDD ---
$host = 'romantcham.fr'; $dbname = 'Domotic_db'; $user = 'G7D'; $password = 'rgnefb';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['etat_valeur' => 0.0, 'etat_texte' => 'Erreur BDD', 'luminosite' => 'N/A']));
}

// --- Réponse par défaut ---
$reponse = [
    'etat_valeur' => 0.0,      // L'état sous forme de nombre (0.0 ou 1.0)
    'etat_texte' => 'Inconnu', // L'état sous forme de texte
    'luminosite' => 'N/A'      // La valeur de luminosité
];

// 1. Récupérer le dernier état de la lumière (servo, id=1)
$id_composant_servo = 1;
$stmt_servo = $conn->prepare("SELECT valeur FROM mesure WHERE id_composant = ? ORDER BY date DESC LIMIT 1");
$stmt_servo->bind_param("i", $id_composant_servo);
$stmt_servo->execute();
$result_servo = $stmt_servo->get_result();
if ($mesure_servo = $result_servo->fetch_assoc()) {
    $reponse['etat_valeur'] = floatval($mesure_servo['valeur']);
    $reponse['etat_texte'] = ($reponse['etat_valeur'] === 1.0) ? 'Allumée' : 'Éteinte';
}
$stmt_servo->close();

// 2. Récupérer la dernière valeur du capteur de lumière (id=2)
$id_composant_lumiere = 2;
$stmt_lumiere = $conn->prepare("SELECT valeur FROM mesure WHERE id_composant = ? ORDER BY date DESC LIMIT 1");
$stmt_lumiere->bind_param("i", $id_composant_lumiere);
$stmt_lumiere->execute();
$result_lumiere = $stmt_lumiere->get_result();
if ($mesure_lumiere = $result_lumiere->fetch_assoc()) {
    $reponse['luminosite'] = floatval($mesure_lumiere['valeur']);
}
$stmt_lumiere->close();

$conn->close();

// 3. Envoyer la réponse complète en JSON
echo json_encode($reponse);
?>