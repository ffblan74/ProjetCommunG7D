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

    
  

    <?php include 'views/common/footer.php'; ?>
</body>
</html>
