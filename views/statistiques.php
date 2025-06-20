<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques</title>
    <link rel="stylesheet" href="assets/CSS/accessibilite.css">
    <link rel="stylesheet" href="assets/CSS/statistiques.css">
    <link rel="stylesheet" href="assets/CSS/footer.css">
    <link rel="stylesheet" href="assets/CSS/header.css">
    <link rel="icon" type="image/jpg" href="assets/images/favicon.png">
    <script src="assets/JS/changeColors.js"></script>
    <script src="assets/JS/accessibilite.js"></script>
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
            // Définit la durée en millisecondes selon la période sélectionnée
            const now = new Date();
            let intervalMs;

            switch (period) {
                case '1h': intervalMs = 1 * 60 * 60 * 1000; break;
                case '6h': intervalMs = 6 * 60 * 60 * 1000; break;
                case '24h': intervalMs = 24 * 60 * 60 * 1000; break;
                case '7j': intervalMs = 7 * 24 * 60 * 60 * 1000; break;
                case '30j': intervalMs = 30 * 24 * 60 * 60 * 1000; break;
                case '90j': intervalMs = 90 * 24 * 60 * 60 * 1000; break;
                case '1an': intervalMs = 365 * 24 * 60 * 60 * 1000; break;
                default: intervalMs = 24 * 60 * 60 * 1000;
            }

            const startDate = new Date(now.getTime() - intervalMs);

            fetch(`controllers/get_graph_data.php?capteur=${capteurId}&periode=${period}`)
                .then(res => res.json())
                .then(data => {
                    if (!data.length) return;

                    const dataTable = new google.visualization.DataTable();
                    dataTable.addColumn('date', 'Date'); // Changement ici : colonne date
                    dataTable.addColumn('number', 'Valeur');

                    // On skip la première ligne ["Date", "Valeur"]
                    for (let i = 1; i < data.length; i++) {
                        // Convertir la date string en objet Date
                        // Attention au format date côté PHP / JSON, ici je pars sur ISO standard
                        const date = new Date(data[i][0]);
                        const value = parseFloat(data[i][1]);
                        dataTable.addRow([date, value]);
                    }

                    const options = {
                        title: 'Évolution des mesures',
                        legend: { position: 'bottom' },
                        height: 300,
                        interpolateNulls: true, // lissage si données manquantes
                        hAxis: {
                            viewWindow: {
                                min: startDate,
                                max: now
                            },
                            format: 'dd/MM HH:mm',
                            gridlines: { count: 10 }
                        }
                    };

                    const chart = new google.visualization.LineChart(document.getElementById(`chart-${capteurId}`));
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



    <h1>Statistiques des Capteurs et Actionneurs</h1>
    <div class="stat-container">
        <!-- Liste des composants -->
        <div class="stat-item full-width">
    <div class="stat-title">Composants</div>
    <table class="components-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Type</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($components as $component): ?>
                <tr>
                    <td><?= htmlspecialchars($component['id']) ?></td>
                    <td><?= htmlspecialchars($component['nom']) ?></td>
                    <td class="<?= $component['is_capteur'] ? 'capteur' : 'actionneur' ?>">
                        <?= $component['is_capteur'] ? 'Capteur' : 'Actionneur' ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

        <!-- Capteur de lumière -->
      
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
