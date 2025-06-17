<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/CSS/dashboard.css">
    <link rel="stylesheet" href="assets/CSS/footer.css">
    <link rel="stylesheet" href="assets/CSS/header.css">
    <link rel="icon" type="image/jpg" href="assets/images/favicon.jpg">
</head>

<body>

    <?php include 'views/common/header.php'; ?>


    <main class="page">

    <div class ="carte-meteo">
        <?php include 'controllers/meteo.php'; ?>
</div>    

    <div class="manuel-lumiere">
    <h2>💡 Salon - <span class="texte-orange">Lumière</span></h2>
    <button class="bouton allumer" onclick="sendCommand('1.0')">Allumer</button>
    <button class="bouton eteindre" onclick="sendCommand('0.0')">Éteindre</button>

  

    <div id="status"></div>

    </div>

    </main>

<script>

        function sendCommand(cmd) {
    fetch('controllers/controle_lumiere.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `commande=${cmd}`
    })
    .then(res => res.text())
    .then(response => {
        const texte = (cmd === '1.0') ? "Allumée" : "Éteinte";
        document.getElementById("status").innerText = "État : " + texte;
    })
    .catch(err => {
        document.getElementById("status").innerText = "Erreur de communication.";
        console.error(err);
    });
}

window.addEventListener('DOMContentLoaded', () => {
    fetch('controllers/etat_lumiere.php')
        .then(res => res.text())
        .then(texte => {
            document.getElementById("status").innerText = "État : " + texte;
        })
        .catch(err => {
            document.getElementById("status").innerText = "État : erreur";
            console.error(err);
        });
});


</script>




<?php include 'views/common/footer.php'; ?>


</body>


</html>