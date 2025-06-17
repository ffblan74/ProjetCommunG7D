<?php
$conn = new mysqli('romantcham.fr', 'G7D', 'rgnefb', 'Domotic_db');
if ($conn->connect_error) die("Erreur connexion : " . $conn->connect_error);

$stmt = $conn->prepare("INSERT INTO mesure (id_composant, `date`, valeur) VALUES (?, ?, ?)");
$valeur = 1.0;
$date = date("Y-m-d H:i:s");
$id_composant = 4;

$stmt->bind_param("isd", $id_composant, $date, $valeur);

if ($stmt->execute()) {
    echo "✔ Insertion OK";
} else {
    echo "❌ Erreur SQL : " . $stmt->error;
}
$stmt->close();
$conn->close();
