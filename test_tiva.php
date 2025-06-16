<?php
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Contrôle LED et Servomoteur Tiva</title>
    <style>
        body { font-family: sans-serif; text-align: center; }
        .container { margin-top: 50px; }
        button { font-size: 1.2em; padding: 10px 20px; margin: 5px; cursor: pointer; }
        .output { background-color: #f0f0f0; border: 1px solid #ccc; padding: 15px; margin-top: 20px; text-align: left; display: inline-block; }
    </style>
</head>
<body>

<div class="container">
    <h1>Contrôleur de LED et Servomoteur pour Tiva LaunchPad</h1>
    <form method="get">
        <h2>Contrôle des LEDs</h2>
        <button type="submit" name="led" value="R" style="background-color: #ffcccc;">Rouge ON</button>
        <button type="submit" name="led" value="r" style="background-color: #ffcccc;">Rouge OFF</button>
        <br>
        <button type="submit" name="led" value="G" style="background-color: #ccffcc;">Vert ON</button>
        <button type="submit" name="led" value="g" style="background-color: #ccffcc;">Vert OFF</button>
        <br>
        <button type="submit" name="led" value="B" style="background-color: #ccccff;">Bleu ON</button>
        <button type="submit" name="led" value="b" style="background-color: #ccccff;">Bleu OFF</button>
        <br><br>

        <h2>Contrôle du Servomoteur</h2>
        <button type="submit" name="servo" value="1" style="background-color: #ffe0b3;">Allumer Interrupteur</button>
        <button type="submit" name="servo" value="9" style="background-color: #ffe0b3;">Servomoteur Centre</button>
        <button type="submit" name="servo" value="0" style="background-color: #ffe0b3;">Éteindre Interrupteur</button>
    </form>

    <div class="output">
    <?php
    // Variables de configuration (en français !)
    $nom_port_serie = 'COM7'; // Modifier ici si nécessaire, doit correspondre au port de la Tiva
    $vitesse_baud = 9600;
    $delai_reponse_ms = 500; // Délai maximal pour attendre une réponse de la Tiva (0.5 secondes)
    // Le délai entre les commandes n'est plus géré côté PHP pour le servomoteur,
    // car la Tiva s'occupe elle-même du mouvement complet (aller-retour).

    // --- Fonction utilitaire pour un affichage propre ---
    function afficher_sortie($texte_a_afficher) {
        echo htmlspecialchars($texte_a_afficher) . "<br>\n";
        // Assure que la sortie est envoyée immédiatement au navigateur
        flush();
        ob_flush();
    }

    // --- Début du traitement PHP ---
    // Vérifie si l'extension PHP Direct IO (dio) est activée
    if (!function_exists('dio_open')) {
        die("Erreur : L'extension PHP Direct IO (dio) n'est pas activée. Vérifiez votre configuration PHP (php.ini).");
    }

    // Vérifie si une commande a été envoyée via les boutons
    if (isset($_GET['led']) || isset($_GET['servo'])) {
        $commande_led = isset($_GET['led']) ? $_GET['led'] : '';
        $commande_servo = isset($_GET['servo']) ? $_GET['servo'] : '';
        // La commande à envoyer est soit une LED, soit une commande de servomoteur
        $commande_a_envoyer = $commande_led ?: $commande_servo;

        afficher_sortie("Préparation de l'envoi de la commande...");

        // Configure les paramètres du port COM via la commande système (spécifique à Windows)
        // Ceci est important pour que le port série soit configuré correctement avant d'être ouvert par dio.
        exec("mode {$nom_port_serie} baud={$vitesse_baud} data=8 stop=1 parity=n");

        // Tente d'ouvrir le port série en mode lecture/écriture
        $descripteur_port_serie = dio_open("\\\\.\\{$nom_port_serie}", O_RDWR);

        // Vérifie si l'ouverture du port série a réussi
        if (!$descripteur_port_serie) {
            afficher_sortie("ERREUR : Impossible d'ouvrir le port série {$nom_port_serie}.");
            afficher_sortie("Vérifiez que le port est correct, qu'il n'est pas déjà utilisé, et que les droits sont suffisants.");
        } else {
            afficher_sortie("Port série {$nom_port_serie} ouvert. Envoi de la commande '{$commande_a_envoyer}'...");

            // Envoie la commande appropriée
            dio_write($descripteur_port_serie, $commande_a_envoyer);
            afficher_sortie("Commande envoyée à la Tiva.");

            // --- Lecture de la réponse de la Tiva ---
            $reponse_tiva = '';
            $temps_debut = microtime(true); // Enregistre l'heure de début pour le timeout

            // Boucle de lecture tant que le délai n'est pas dépassé
            while ((microtime(true) - $temps_debut) < ($delai_reponse_ms / 1000)) {
                $donnees_lues = dio_read($descripteur_port_serie, 1); // Lit un caractère à la fois
                // Si aucune donnée n'est lue ou si c'est faux, attend un peu et réessaie
                if ($donnees_lues === false || $donnees_lues === '') {
                    usleep(10000); // Attend 10 millisecondes
                    continue;
                }
                $reponse_tiva .= $donnees_lues; // Ajoute le caractère à la réponse

                // Si la réponse contient un saut de ligne, on considère qu'elle est complète
                if (str_contains($reponse_tiva, "\n")) {
                    break;
                }
            }

            // Affiche la réponse ou un message de timeout
            if (!empty($reponse_tiva)) {
                afficher_sortie("Réponse de la Tiva: " . trim($reponse_tiva));
            } else {
                afficher_sortie("Aucune réponse de la Tiva reçue dans le délai imparti ({$delai_reponse_ms} ms).");
            }

            // Ferme le port série
            dio_close($descripteur_port_serie);
            afficher_sortie("Port série fermé.");
        }

    } else {
        // Message initial si aucun bouton n'a été cliqué
        afficher_sortie("Veuillez cliquer sur un bouton pour envoyer une commande à la Tiva.");
    }
    ?>
    </div>
</div>

</body>
</html>
