<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si le mode n'est pas 'automatique', on ne fait rien.
if (($_SESSION['mode_lumiere'] ?? 'manuel') !== 'automatique') {
    die(json_encode(['action' => 'aucune', 'raison' => 'Mode manuel actif']));
}

// --- Paramètres ---
define('SEUIL_LUMINOSITE_BASSE', 500); //valeur sous laquelle on allume
define('ID_COMPOSANT_SERVO', 1);
define('ID_COMPOSANT_LUMIERE', 2);

// --- Connexion BDD ---
$host = 'romantcham.fr'; $dbname = 'Domotic_db'; $user = 'G7D'; $password = 'rgnefb';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) { die(json_encode(['action' => 'erreur', 'message' => 'DB Error'])); }

// 1. Récupérer la dernière mesure du capteur de lumière (id=2)
$stmt_capteur = $conn->prepare("SELECT valeur FROM mesure WHERE id_composant = ? ORDER BY date DESC LIMIT 1");
$stmt_capteur->bind_param("i", ID_COMPOSANT_LUMIERE);
$stmt_capteur->execute();
$mesure_capteur = $stmt_capteur->get_result()->fetch_assoc();
$valeur_luminosite = $mesure_capteur ? floatval($mesure_capteur['valeur']) : null;
$stmt_capteur->close();

if ($valeur_luminosite === null) { die(json_encode(['action' => 'aucune', 'raison' => 'Pas de mesure de luminosité'])); }

// 2. Déterminer l'état désiré (1.0 = ON, 0.0 = OFF)
$etat_desire = ($valeur_luminosite < SEUIL_LUMINOSITE_BASSE) ? 1.0 : 0.0;

// 3. Récupérer le dernier état connu du servo (id=1)
$stmt_servo = $conn->prepare("SELECT valeur FROM mesure WHERE id_composant = ? ORDER BY date DESC LIMIT 1");
$stmt_servo->bind_param("i", ID_COMPOSANT_SERVO);
$stmt_servo->execute();
$mesure_servo = $stmt_servo->get_result()->fetch_assoc();
$etat_actuel = $mesure_servo ? floatval($mesure_servo['valeur']) : -1.0; // -1.0 pour état inconnu
$stmt_servo->close();

// 4. Si l'état a besoin de changer, on agit
if ($etat_desire !== $etat_actuel) {
    // a. Enregistrer la commande dans la BDD
    $stmt_insert = $conn->prepare("INSERT INTO mesure (id_composant, date, valeur) VALUES (?, ?, ?)");
    $timestamp = date("Y-m-d H:i:s");
    $stmt_insert->bind_param("isd", ID_COMPOSANT_SERVO, $timestamp, $etat_desire);
    $stmt_insert->execute();
    $stmt_insert->close();
    
    // b. Envoyer la commande au port série
    $port = 'COM7'; $baud = 9600;
    if (function_exists('dio_open')) {
        exec("mode {$port} baud={$baud} data=8 stop=1 parity=n");
        $serial = dio_open("\\\\.\\{$port}", O_RDWR);
        if ($serial) {
            dio_write($serial, strval($etat_desire));
            dio_close($serial);
        }
    }
    // IMPORTANT : On renvoie le JSON après avoir agi
    echo json_encode(['action' => 'changement', 'nouvel_etat' => $etat_desire, 'luminosite' => $valeur_luminosite]);

} else {
    // Si aucun changement n'est nécessaire
    echo json_encode(['action' => 'aucune', 'raison' => 'État déjà correct', 'etat_actuel' => $etat_actuel, 'luminosite' => $valeur_luminosite]);
}
$conn->close();
?>