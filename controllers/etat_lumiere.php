<?php
$host = 'romantcham.fr';
$dbname = 'Domotic_db';
$user = 'G7D';
$password = 'rgnefb';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo "Erreur connexion DB";
    exit;
}

$id_composant_servomoteur = 1; // Remplace par l'ID réel de ton capteur lumière

$sql = "SELECT valeur FROM mesure WHERE id_composant = ? ORDER BY date DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("d", $id_composant_servomoteur);
$stmt->execute();
$stmt->bind_result($valeur);

if ($stmt->fetch()) {
    echo ($valeur == 1.0) ? "Allumée" : "Éteinte";
} else {
    echo "Inconnue";
}

$stmt->close();
$conn->close();
?>
