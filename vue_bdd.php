<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vue de la Base de Données</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        h1 {
            text-align: center;
            color: #2c3e50;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 15px;
            margin-top: 0;
        }
        h2 {
            color: #34495e;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
            margin-top: 40px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #ddd;
        }
        thead th {
            background-color: #3498db;
            color: #ffffff;
            font-weight: 600;
        }
        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tbody tr:hover {
            background-color: #eaf4fb;
        }
        .empty-message {
            padding: 15px;
            background-color: #fef8e7;
            border: 1px solid #f39c12;
            color: #d35400;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Vue de la Base de Données : Domotic_db</h1>

        <?php
        // --- Paramètres de la base de données ---
        $host = 'romantcham.fr';
        $dbname = 'Domotic_db';
        $user = 'G7D';
        $password = 'rgnefb';

        // --- Connexion à la BDD ---
        $conn = new mysqli($host, $user, $password, $dbname);

        // Vérification de la connexion
        if ($conn->connect_error) {
            die("<h2>❌ Erreur de Connexion</h2><p>La connexion à la base de données a échoué : " . $conn->connect_error . "</p>");
        }

        // --- Liste des tables à afficher ---
        $tables = ['utilisateur', 'mesure', 'composant'];

        // Boucle sur chaque table pour l'afficher
        foreach ($tables as $table) {
            echo "<h2>Table : `{$table}`</h2>";

            // Exécute la requête pour récupérer toutes les données de la table
            // On trie par ID en ordre décroissant pour voir les dernières entrées en premier
            $sql = "SELECT * FROM `{$table}` ORDER BY id DESC";
            $result = $conn->query($sql);

            // Vérifie s'il y a des résultats
            if ($result && $result->num_rows > 0) {
                echo "<table>";
                
                // --- Création de l'en-tête du tableau (les noms des colonnes) ---
                echo "<thead><tr>";
                $fields = $result->fetch_fields();
                foreach ($fields as $field) {
                    echo "<th>" . htmlspecialchars($field->name) . "</th>";
                }
                echo "</tr></thead>";

                // --- Création du corps du tableau (les données) ---
                echo "<tbody>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    foreach ($row as $data) {
                        // On protège les données avant de les afficher
                        echo "<td>" . htmlspecialchars($data) . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</tbody>";

                echo "</table>";
            } else {
                // Message si la table est vide
                echo "<p class='empty-message'>La table `{$table}` est vide ou une erreur est survenue.</p>";
            }
        }

        // Fermeture de la connexion
        $conn->close();
        ?>
    </div>
</body>
</html>