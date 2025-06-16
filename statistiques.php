<!DOCTYPE html>
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

    <main class="content">
        <h2>Nombre de mesures par capteur</h2>
        <div class="chart-container" id="chart_div1"></div>
    </main>

    
  

    <?php include 'views/common/footer.php'; ?>
</body>
</html>
