<?php
// Paramètres de connexion
$host = 'romantcham.fr';
$dbname = 'Domotic_db';
$user = 'G7D';
$password = 'rgnefb';

// Connexion à la base
$conn = new mysqli($host, $user, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Préparation de la requête
$id_composant = 1;
$sql = "SELECT id, id_composant, date, valeur FROM mesure WHERE id_composant = ? ORDER BY date DESC";



$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_composant);
$stmt->execute();

$result = $stmt->get_result();

// Affichage
echo "<h2>Mesures pour le composant #$id_composant</h2>";
if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='8'><tr><th>ID</th><th>Date</th><th>Valeur</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['date']}</td>";
        echo "<td>{$row['valeur']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Aucune mesure trouvée pour ce composant.";
}

// Nettoyage
$stmt->close();
$conn->close();
?>
