<?php
// On indique que la réponse sera au format JSON
header('Content-Type: application/json');

// --- Connexion BDD ---
$host = 'romantcham.fr';
$dbname = 'Domotic_db';
$user = 'G7D';
$password = 'rgnefb';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['etat' => 'Erreur BDD', 'luminosite' => null]));
}

// --- Préparation de la réponse ---
$reponse = [
    'etat' => 'Inconnu',
    'luminosite' => 'N/A' // Valeur par défaut
];

// 1. Récupérer le dernier état de la lumière (servo, id=1)
$id_composant_servo = 1;
$stmt_servo = $conn->prepare("SELECT valeur FROM mesure WHERE id_composant = ? ORDER BY date DESC LIMIT 1");
$stmt_servo->bind_param("i", $id_composant_servo);
$stmt_servo->execute();
$result_servo = $stmt_servo->get_result();
if ($mesure_servo = $result_servo->fetch_assoc()) {
    $reponse['etat'] = (floatval($mesure_servo['valeur']) === 1.0) ? 'Allumée' : 'Éteinte';
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