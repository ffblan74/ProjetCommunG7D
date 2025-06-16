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
    <script type="text/javascript">
        google.charts.load('current', {packages: ['corechart']});
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts(){
            <?php

            $host = 'romantcham.fr';
            $dbname = 'Domotic_db';
            $user = 'G7D';
            $password_db = 'rgnefb';
            $conn = new mysqli($host, $user, $password_db, $dbname);

            if ($conn -> connect_error){
                die("Erreur de connection". $conn -> connect_error);
            }

            $sql = "SELECT composant.nom AS composant, COUNT(mesure.id) AS nbMesures FROM composant
                         JOIN mesure ON mesure.id_composant = composant.id WHERE composant.is_capteur = 1 GROUP BY composant.nom;";

            $result = $conn -> query($sql);

            if($result-> num_rows >0){
                echo "var data1 = new google.visualization.DataTable();";
                echo "data1.addColumn('string', 'Composant');";
                echo "data1.addColumn('number', 'Nombre de mesures');";
                echo "data1.addRows([";
                    while($row = $result -> fetch_assoc()) {
                    echo "['". $row["composant"] , "' , " . $row["nbMesures"] . "],";
                    };
                echo "]);";
            }

            $conn-> close();
            ?>

            var options = {
                title: "Nombre de mesures pour chaque composant",
                fontName : 'Poppins'
            };
            var chart1 = new google.visualization.PieChart( document.getElementById("chart_div1"));
            window.addEventListener('resize', () => chart1.draw(data1, options));
            chart1.draw(data1, options);

        }
    </script>    
</head>
<body>
    <?php include 'views/common/header.php'; ?>
    <h1>Statistiques des Capteurs et Actionneurs</h1>
    
    <main class="content">
        <h2>Nombre de mesures par capteur</h2>
        <div class="chart-container" id="chart_div1"></div>
    </main>
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
