<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques des Capteurs et Actionneurs</title>
    <link rel="stylesheet" href="assets/CSS/statistiques.css">
</head>
<body>
    <!-- Header -->
    <?php include 'views/common/header.php'; ?>
    <h1>Statistiques des Capteurs et Actionneurs</h1>
    <div class="stat-container">
        <!-- Liste des composants -->
        <div class="stat-item">
            <div class="stat-title">Composants</div>
            <ul>
                <?php foreach ($components as $component): ?>
                    <li><?= htmlspecialchars($component['id']) ?> - <?= htmlspecialchars($component['nom']) ?> - <?= htmlspecialchars($component['is_capteur']) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <!-- Liste des mesures -->
        <div class="stat-item">
            <div class="stat-title">Mesures</div>
            <ul>
                <?php foreach ($measurements as $measurement): ?>
                    <li><?= htmlspecialchars($measurement['id']) ?> - <?= htmlspecialchars($measurement['id_composant']) ?> - <?= htmlspecialchars($component['date']) ?> - <?= htmlspecialchars($component['valeur']) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <!-- Capteurs de température -->
        <div class="stat-item">
            <div class="stat-title">Capteurs de Température</div>
            <p>Température intérieure : <?= htmlspecialchars($temperatureInterior['valeur']) ?>°C</p>
            <p>Température extérieure : <?= htmlspecialchars($temperatureExterior['valeur']) ?>°C</p>
            <div class="stat-title">Prévisions météo :</div>
            <ul>
                <?php foreach ($weatherForecast as $forecast): ?>
                    <li><?= htmlspecialchars($forecast['time']) ?> : <?= htmlspecialchars($forecast['temperature']) ?>°C</li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Capteur de lumière -->
        <div class="stat-item">
            <div class="stat-title">Capteur de Lumière</div>
            <p>Luminosité actuelle : <?= htmlspecialchars($lightSensor['valeur']) ?> lux</p>
        </div>

        <!-- Servomoteur pour l'interrupteur -->
        <div class="stat-item">
            <div class="stat-title">État de l'Interrupteur</div>
            <p>Lumière : <?= $lightSwitch['state'] ? 'Allumée' : 'Éteinte' ?></p>
        </div>

        <!-- Moteur pour les volets -->
        <div class="stat-item">
            <div class="stat-title">État des Volets</div>
            <p>Volets : <?= $shutterMotor['state'] ? 'Ouverts' : 'Fermés' ?></p>
        </div>
    </div>
</body>
</html>