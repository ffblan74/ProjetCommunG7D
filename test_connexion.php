<?php
require_once './models/Database.php'; // Assure-toi que ce fichier contient ta classe Database

try {
    // Récupère l'instance de connexion
    $db = Database::getInstance()->getConnection();

    // Requête pour lire la première ligne
    $stmt = $db->query("SELECT * FROM composant LIMIT 1");

    // Récupération du résultat
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        echo "<pre>";
        print_r($row);
        echo "</pre>";
    } else {
        echo "Aucune donnée trouvée dans la table 'test'.";
    }

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
