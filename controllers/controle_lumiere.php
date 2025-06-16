<?php
// 🔹 Étape 1 : récupérer la commande D'ABORD
$commande = $_POST['commande'] ?? $_GET['commande'] ?? null;

if (!$commande) {
    die("Aucune commande reçue.");
}

// 🔹 Étape 2 : enregistrer la commande dans la BDD

$host = 'romantcham.fr';
$dbname = 'Domotic_db';
$user = 'G7D';
$password = 'rgnefb';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$valeur = floatval($commande);
$id_composant = 1;
$timestamp = date("Y-m-d H:i:s");

// 🔸 Toujours spécifier les colonnes explicitement !
$sql = "INSERT INTO mesure (id_composant, date, valeur) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erreur prepare : " . $conn->error);
}

$stmt->bind_param("isd", $id_composant, $timestamp, $valeur);

if ($stmt->execute()) {
    echo "✔ Mesure enregistrée<br>";
} else {
    echo "❌ Erreur d'insertion : " . $stmt->error . "<br>";
}

$stmt->close();
$conn->close();

// 🔹 Étape 3 : envoyer la commande au port série
$port = 'COM7';
$baud = 9600;

if (!function_exists('dio_open')) {
    die("Erreur : extension dio non activée.");
}

exec("mode {$port} baud={$baud} data=8 stop=1 parity=n");

$serial = dio_open("\\\\.\\{$port}", O_RDWR);
if (!$serial) {
    die("Erreur : ouverture port série échouée.");
}

dio_write($serial, $commande);
echo "Commande '{$commande}' envoyée à la TIVA.";
?>
