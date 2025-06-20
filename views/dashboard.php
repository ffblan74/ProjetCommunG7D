<?php

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/CSS/dashboard.css">
    <link rel="stylesheet" href="assets/CSS/footer.css">
    <link rel="stylesheet" href="assets/CSS/header.css">
    <link rel="icon" type="image/jpg" href="assets/images/favicon.png">
    <script src="assets/JS/dashboard.js"></script>

</head>
<body>
    <?php include 'views/common/header.php'; ?>

    <main class="page">

        <div class="carte-meteo">
            <?php include 'controllers/meteo.php'; ?>
        </div>

        <div class="colonne-centrale">
            <div class="manuel-lumiere">
            <h2>Lumière - <span class="texte-bleu">Contrôler</span></h2>
                <div class="mode-controle">
                    <button id="btn-manuel" class="bouton" onclick="definirMode('manuel')">Manuel</button>
                    <button id="btn-auto" class="bouton" onclick="definirMode('automatique')">Automatique</button>
                </div>
                <div class="allumereteindre">
                    <button class="bouton allumer" onclick="sendCommand('1')">Allumer</button>
                    <button class="bouton eteindre" onclick="sendCommand('0')">Éteindre</button>
                </div>
                <div id="zone-seuil-auto">
                    <label for="input-seuil">Seuil d'allumage (en lx) :</label>
                    <input type="number" id="input-seuil" class="bouton" min="0" step="50">
                    <button class="bouton" onclick="sauvegarderSeuil()">OK</button>
                    <br/>
                    <span id="confirmation-seuil"></span>
                </div>
                <div id="status">État : Chargement...</div>
                <div id="luminosity-status" class="info-secondaire"></div> 
            </div>

            <div class="manuel-volets">
            <h2><i class="fa-solid fa-blinds-open"></i> Volets - <span class="texte-bleu">Position</span></h2>
                <div class="allumereteindre">
                    <button class="bouton ouvrir" onclick="sendCommandVolet('0')">Ouvrir</button>
                    <button class="bouton fermer" onclick="sendCommandVolet('1')">Fermer</button>
                </div>
                <div id="status-volet">État : Chargement...</div>
            </div>
        </div>

        <div class="carte-capteurs">
            <h2>État des Appareils</h2>
            <ul id="liste-capteurs-dynamique" class="liste-capteurs">
            </ul>
        </div>

    </main>

    <?php include 'views/common/footer.php'; ?>
</body>
</html>