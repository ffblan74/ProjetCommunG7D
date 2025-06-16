<?php
header('Content-Type: application/json');

$host = 'romantcham.fr';
$dbname = 'Domotic_db';
$user = 'G7D';
$password_db = 'rgnefb';
$conn = new mysqli($host, $user, $password_db, $dbname);

if ($conn->connect_error) {
    die(json_encode([]));
}

$period = $_GET['period'] ?? 'semaine';

// Adapte la clause WHERE selon la période
switch ($period) {
    case 'mois':
        $date_limit = "DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        break;
    case 'annee':
        $date_limit = "DATE_SUB(NOW(), INTERVAL 1 YEAR)";
        break;
    case 'semaine':
    default:
        $date_limit = "DATE_SUB(NOW(), INTERVAL 1 WEEK)";
        break;
}

$sql = "
    SELECT composant.nom AS composant, COUNT(mesure.id) AS nbMesures
    FROM composant
    JOIN mesure ON mesure.id_composant = composant.id
    WHERE composant.is_capteur = 1 AND mesure.date >= $date_limit
    GROUP BY composant.nom;
";

$result = $conn->query($sql);
$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [$row["composant"], (int)$row["nbMesures"]];
    }
}

$conn->close();
echo json_encode($data);
?>

