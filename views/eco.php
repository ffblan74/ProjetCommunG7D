<?php
$url = "https://ecoindex.fr/api/data?url=https://light-control.romantcham.fr";
$data = json_decode(file_get_contents($url), true);

if (!$data || isset($data['error'])) {
    echo "<h2>Erreur lors de la récupération des données d'éco-conception.</h2>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Éco-conception du site</title>
    <link rel="stylesheet" href="assets/CSS/eco.css">
</head>
<body>
<main>
    <div class="eco-header">
        <h1>🌿 Analyse éco-conception</h1>
        <p><strong>URL analysée :</strong> https://light-control.romantcham.fr</p>
    </div>

    <div class="eco-results">
        <h2 class="eco-note">Classement : <?= $data['grade'] ?> (<?= $data['ecoIndex'] ?>/100)</h2>
        <div class="eco-metrics">
            <div class="eco-metric">
                <h3>Émissions CO₂</h3>
                <p><?= $data['greenhouseGasEmission'] ?> g</p>
            </div>
            <div class="eco-metric">
                <h3>Énergie consommée</h3>
                <p><?= $data['energyConsumption'] ?> Wh</p>
            </div>
            <div class="eco-metric">
                <h3>Poids de la page</h3>
                <p><?= $data['size'] ?> Ko</p>
            </div>
            <div class="eco-metric">
                <h3>Requêtes HTTP</h3>
                <p><?= $data['requests'] ?></p>
            </div>
            <div class="eco-metric">
                <h3>Noeuds DOM</h3>
                <p><?= $data['domSize'] ?></p>
            </div>
        </div>
    </div>

    <div class="eco-footer">
        <p>Analyse réalisée via <a href="https://www.ecoindex.fr/" target="_blank">ecoindex.fr</a></p>
    </div>
</main>
</body>
</html>
