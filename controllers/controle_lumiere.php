<?php
// On s'assure que le script ne renvoie pas d'avertissements qui casseraient la réponse
error_reporting(0);

// 🔹 Étape 1 : récupérer la commande
$commande = $_POST['commande'] ?? $_GET['commande'] ?? null;

if ($commande === null) {
    die("❌ Erreur: Aucune commande reçue.");
}

// 🔹 Étape 2 : enregistrer la commande dans la BDD
$host = 'romantcham.fr';
$dbname = 'Domotic_db';
$user = 'G7D';
$password = 'rgnefb';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("❌ Erreur de connexion BDD : " . $conn->connect_error);
}

// On s'assure que la commande est un nombre flottant
$valeur = floatval($commande);
$id_composant = 1; // Le servo/actuateur
$timestamp = date("Y-m-d H:i:s");

$sql = "INSERT INTO mesure (id_composant, date, valeur) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("❌ Erreur prepare SQL : " . $conn->error);
}

$stmt->bind_param("isd", $id_composant, $timestamp, $valeur);

$message_bdd = "";
if ($stmt->execute()) {
    $message_bdd = "✔ Mesure enregistrée en BDD.";
} else {
    $message_bdd = "❌ Erreur d'insertion BDD : " . $stmt->error;
}
$stmt->close();
$conn->close();

// 🔹 Étape 3 : envoyer la commande au port série
$message_serial = "";
if (!function_exists('dio_open')) {
    $message_serial = "❌ Erreur: extension DIO non activée.";
} else {
    $port = 'COM7';
    $baud = 9600;
    exec("mode {$port} baud={$baud} data=8 stop=1 parity=n");
    
    // On met @ pour supprimer les avertissements si le port n'est pas accessible
    $serial = @dio_open("\\\\.\\{$port}", O_RDWR);
    if (!$serial) {
        $message_serial = "❌ Erreur: ouverture du port série {$port} échouée.";
    } else {
        dio_write($serial, strval($commande));
        dio_close($serial);
        $message_serial = "✔ Commande '{$commande}' envoyée à la TIVA.";
    }
}

// On renvoie le rapport complet
echo "Rapport de controle_lumiere.php:\n";
echo "1. " . $message_bdd . "\n";
echo "2. " . $message_serial;
?>