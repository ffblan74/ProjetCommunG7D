<?php
$composants_a_chercher = [
    2 => ['nom' => 'Luminosité', 'unite' => 'lx',    'icone' => '☀️'],
    3 => ['nom' => 'Température', 'unite' => '°C',    'icone' => '🌡️'],
    4 => ['nom' => 'Distance',    'unite' => 'cm',    'icone' => '📏'],
    5 => ['nom' => 'Volets',      'type' => 'etat',  'icone' => '🪟'],
];

$host = 'romantcham.fr';
$dbname = 'Domotic_db';
$user = 'G7D';
$password = 'rgnefb';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    echo "<h2>❌ Erreur Capteurs</h2>";
    echo "<p>Connexion à la base de données échouée.</p>";
    exit;
}

echo "<h2>État des Appareils</h2>";
echo "<ul class='liste-capteurs'>";

foreach ($composants_a_chercher as $id => $details) {
    $stmt = $conn->prepare("SELECT valeur FROM mesure WHERE id_composant = ? ORDER BY date DESC LIMIT 1");
    if (!$stmt) continue;

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultat = $stmt->get_result();
    
    $valeur_formatee = "N/A";

    if ($mesure = $resultat->fetch_assoc()) {
        $valeur_brute = floatval($mesure['valeur']);

        if (isset($details['type']) && $details['type'] === 'etat') {
            $valeur_formatee = ($valeur_brute === 1.0) ? "Fermés" : "Ouverts";
        } else {
            $valeur_formatee = number_format($valeur_brute, 1, ',', ' ') . " " . $details['unite'];
        }
    }
    
    echo "<li>";
    echo "  <span class='nom-capteur'>{$details['icone']} {$details['nom']}</span>";
    echo "  <span class='valeur-capteur'>{$valeur_formatee}</span>";
    echo "</li>";

    $stmt->close();
}

echo "</ul>";

$conn->close();
?>