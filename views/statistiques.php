<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques</title>
    <link rel="stylesheet" href="assets/CSS/statistiques.css">
    <link rel="stylesheet" href="assets/CSS/footer.css">
    <link rel="stylesheet" href="assets/CSS/header.css">
    <link rel="icon" type="image/jpg" href="assets/images/favicon.jpg">
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
    google.charts.load('current', { packages: ['corechart'] });
    google.charts.setOnLoadCallback(() => {
        drawChart('semaine'); // valeur par défaut
    });

    document.addEventListener("DOMContentLoaded", () => {
        const timeRange = document.getElementById("timeRange");
        timeRange.addEventListener("change", () => {
            drawChart(timeRange.value);
        });
    });

    function drawChart(period) {
        fetch(`controllers/get_chart_data.php?period=${period}`)
            .then(response => response.json())
            .then(result => {
                const data = new google.visualization.DataTable();
                data.addColumn('string', 'Composant');
                data.addColumn('number', 'Nombre de mesures');
                data.addRows(result);

                const options = {
                    title: "Nombre de mesures pour chaque composant",
                    fontName: 'Poppins'
                };

                const chart = new google.visualization.PieChart(document.getElementById('chart_div1'));
                chart.draw(data, options);

                window.addEventListener('resize', () => chart.draw(data, options));
            })
            .catch(error => console.error('Erreur de chargement des données:', error));
    }
</script>
</head>
<body>
    <?php include 'views/common/header.php'; ?>

    <main class="content">
        <h2>Nombre de mesures par capteur</h2>

        <div class="chart-header">
        <label for="timeRange">Période : </label>
        <select id="timeRange">
            <option value="semaine">Semaine</option>
            <option value="mois">Mois</option>
            <option value="annee">Année</option>
        </select>
    </div>

    <div class="chart-container" id="chart_div1"></div>
    </main>


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
    
  

    <?php include 'views/common/footer.php'; ?>
</body>
</html>
