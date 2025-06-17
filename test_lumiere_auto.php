<?php
// Fichier : inserer_mesure.php
// But : Insérer manuellement une valeur dans la table 'mesure' pour les tests.


$id_composant_a_inserer = 2; // <<< On simule le capteur de lumière.

// Quelle valeur voulez-vous insérer ?
// Pour la lumière (ID 2): Une petite valeur (ex: 150) simule l'obscurité.
//                         Une grande valeur (ex: 800) simule la lumière du jour.
// Pour le servo (ID 1):   1.0 pour "Allumé", 0.0 pour "Éteint".
$valeur_a_inserer = 400; // <<< On simule une pièce sombre.




// --- Ajout d'un peu de style pour la lisibilité ---
echo '<style>body { font-family: sans-serif; line-height: 1.6; padding: 20px; background-color: #f5f5f5; } .container { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); } li { margin-bottom: 8px; } </style>';
echo '<div class="container">';


// --- Paramètres de la base de données ---
$host = 'romantcham.fr';
$dbname = 'Domotic_db';
$user = 'G7D';
$password = 'rgnefb';

// --- Connexion à la BDD ---
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("<h1>❌ Erreur de connexion</h1><p>Détail : " . $conn->connect_error . "</p>");
}

// --- Préparation et exécution de la requête ---
$date_actuelle = date("Y-m-d H:i:s");
$sql = "INSERT INTO mesure (id_composant, date, valeur) VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("<h1>❌ Erreur de préparation</h1><p>Détail : " . $conn->error . "</p>");
}

// "isd" = integer, string, double
$stmt->bind_param("isd", $id_composant_a_inserer, $date_actuelle, $valeur_a_inserer);

if ($stmt->execute()) {
    echo "<h1>✅ Succès !</h1>";
    echo "<p>La mesure suivante a bien été insérée dans la base de données :</p>";
    echo "<ul>";
    echo "<li><b>ID Composant :</b> " . htmlspecialchars($id_composant_a_inserer) . "</li>";
    echo "<li><b>Date :</b> " . htmlspecialchars($date_actuelle) . "</li>";
    echo "<li><b>Valeur :</b> " . htmlspecialchars($valeur_a_inserer) . "</li>";
    echo "</ul>";
    echo "<hr><p>Vous pouvez maintenant rafraîchir votre <b>Dashboard</b> et activer le mode automatique pour voir l'effet.</p>";
} else {
    echo "<h1>❌ Erreur !</h1>";
    echo "<p>L'insertion dans la base de données a échoué.</p>";
    echo "<p><b>Erreur MySQL :</b> " . htmlspecialchars($stmt->error) . "</p>";
}

// --- Fermeture ---
$stmt->close();
$conn->close();

echo '</div>';

?>