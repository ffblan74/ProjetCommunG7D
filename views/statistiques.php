<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques</title>
    <link rel="stylesheet" href="assets/CSS/statistiques.css">
    <link rel="stylesheet" href="assets/CSS/footer.css">
    <link rel="stylesheet" href="assets/CSS/header.css">
    <link rel="icon" type="image/jpg" href="assets/images/favicon.png">
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
        google.charts.load('current', { packages: ['corechart'] });
        google.charts.setOnLoadCallback(() => {
            initCharts();
            drawChart('semaine'); // ou '24h'
        });

        function initCharts() {
            const selects = document.querySelectorAll('.periode-select');
            selects.forEach(select => {
                loadChart(select.dataset.capteur, select.value);

                select.addEventListener('change', () => {
                    loadChart(select.dataset.capteur, select.value);
                });
            });
        }

        function loadChart(capteurId, period) {
            fetch(`controllers/get_graph_data.php?capteur=${capteurId}&periode=${period}`) // <- CORRIGÉ ici
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById(`chart-${capteurId}`);

                    if (!data.length || data.length <= 1) {
                        container.innerHTML = "Aucune donnée pour cette période.";
                        return;
                    }

                    const dataTable = new google.visualization.DataTable();
                    dataTable.addColumn('string', 'Date');
                    dataTable.addColumn('number', 'Valeur');
                    dataTable.addRows(data);

                    const options = {
                        title: 'Évolution des mesures',
                        legend: { position: 'bottom' },
                        height: 300
                    };

                    const chart = new google.visualization.LineChart(container);
                    chart.draw(dataTable, options);
                })
                .catch(err => console.error('Erreur lors du chargement du graphique', err));
        }

        document.addEventListener("DOMContentLoaded", () => {
            const timeRange = document.getElementById("timeRange");
            if (timeRange) {
                timeRange.addEventListener("change", () => {
                    drawChart(timeRange.value);
                });
            }
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
                    <li><?= htmlspecialchars($measurement['id']) ?> - <?= htmlspecialchars($measurement['id_composant']) ?> - <?= htmlspecialchars($measurement['date']) ?> - <?= htmlspecialchars($measurement['valeur']) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <!-- Capteurs de température -->
        <div class="stat-item">
            <div class="stat-title">Capteurs de Température</div>
            <p>Température : <?= htmlspecialchars($temperature['valeur']) ?>°C</p>
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
    <?php foreach ($components as $component): ?>
        <?php if ($component['is_capteur']): ?>
            <div class="stat-item">
                <div class="stat-title"><?= htmlspecialchars($component['nom']) ?></div>
                <p>Dernière valeur : 
                    <?php 
                        $latest = $sensorModel->getLatestMeasurementByName($component['nom']);
                        echo htmlspecialchars($latest['valeur']) . ' (' . htmlspecialchars($latest['date']) . ')';
                    ?>
                </p>

                <label for="periode-<?= $component['id'] ?>">Période :</label>
                <select class="periode-select" data-capteur="<?= $component['id'] ?>" id="periode-<?= $component['id'] ?>">
                    <option value="1h">1h</option>
                    <option value="6h">6h</option>
                    <option value="24h" selected>24h</option>
                    <option value="7j">7 jours</option>
                    <option value="30j">30 jours</option>
                    <option value="90j">90 jours</option>
                    <option value="1an">1 an</option>
                </select>

                <div class="chart-container" id="chart-<?= $component['id'] ?>" style="height:300px;"></div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

  

    <?php include 'views/common/footer.php'; ?>
</body>
</html>
