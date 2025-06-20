<?php
header('Content-Type: application/json'); // Très important : indique que la réponse est du JSON

$composants_a_chercher = [
    2 => ['nom' => 'Luminosité', 'unite' => 'lx',    'icone' => '☀️'],
    6 => ['nom' => 'Température', 'unite' => '°C',    'icone' => '🌡️'],
    4 => ['nom' => 'Distance',    'unite' => 'cm',    'icone' => '📏'],
    7 => ['nom' => 'Humidité',    'unite' => '%',     'icone' => '💧'],
    5 => ['nom' => 'Volets',      'type' => 'etat',  'icone' => '🪟'],
];

$host = 'romantcham.fr';
$dbname = 'Domotic_db';
$user = 'G7D';
$password = 'rgnefb';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['erreur' => 'Connexion BDD échouée']));
}

$donnees_capteurs = []; // On va stocker les résultats ici

foreach ($composants_a_chercher as $id => $details) {
    $stmt = $conn->prepare("SELECT valeur FROM mesure WHERE id_composant = ?  ORDER BY id DESC LIMIT 1");
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

    // On ajoute les données formatées au tableau de réponse
    $donnees_capteurs[] = [
        'nom' => $details['nom'],
        'icone' => $details['icone'],
        'valeur' => $valeur_formatee
    ];
    $stmt->close();
}
$conn->close();

// On encode le tableau final en JSON et on l'envoie
echo json_encode($donnees_capteurs);
?>