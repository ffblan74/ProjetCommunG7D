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
    <style>
        .mode-controle { display: flex; gap: 10px; margin-bottom: 20px; justify-content: center; }
        .bouton.active { border: 2px solid #ff8c00; box-shadow: 0 0 5px #ff8c00; }
        .manuel-lumiere button:disabled { background-color: #ccc; cursor: not-allowed; opacity: 0.6; }
        .info-secondaire { margin-top: 8px; font-size: 0.9em; color: #666; }
    </style>
</head>
<body>
    <?php include 'views/common/header.php'; ?>

    <main class="page">
        <div class="carte-meteo">
            <?php include 'controllers/meteo.php'; ?>
        </div>
        <div class="manuel-lumiere">
            <h2>💡 Salon - <span class="texte-orange">Lumière</span></h2>

            <div class="mode-controle">
                <button id="btn-manuel" class="bouton" onclick="definirMode('manuel')">Manuel</button>
                <button id="btn-auto" class="bouton" onclick="definirMode('automatique')">Automatique</button>
            </div>
            <button class="bouton allumer" onclick="sendCommand('1.0')">Allumer</button>
            <button class="bouton eteindre" onclick="sendCommand('0.0')">Éteindre</button>
            <div id="status">État : Chargement...</div>
            <div id="luminosity-status" class="info-secondaire"></div> 
        </div>
    </main>

<script>
let intervalleAutomatique = null;

function sendCommand(cmd) {
    fetch('controllers/controle_lumiere.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `commande=${cmd}`
    })
    .then(res => res.text())
    .then(() => fetcherEtatActuel());
}

function definirMode(mode) {
    if (intervalleAutomatique) { clearInterval(intervalleAutomatique); }
    fetch('controllers/definir_mode.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `mode=${mode}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'succes') {
            mettreAJourUI(mode);
            if (mode === 'automatique') {
                verifierEtatAutomatique();
                intervalleAutomatique = setInterval(verifierEtatAutomatique, 7000);
            }
        }
    });
}

function verifierEtatAutomatique() {
    fetch('controllers/gestion_automatique.php')
    .then(res => res.json())
    .then(data => {
        if(data.action === 'erreur') {
            console.error("Erreur du script automatique:", data.message);
            return;
        }

        const etat = (data.action === 'changement') ? data.nouvel_etat : data.etat_actuel;
        const texte_etat = (etat === 1.0) ? "Allumée" : "Éteinte";
        document.getElementById("status").innerText = `État : ${texte_etat} (Auto)`;
        document.getElementById("luminosity-status").innerText = `Luminosité : ${data.luminosite}`;
    })
    .catch(error => {
        console.error('Erreur de parsing JSON ou de réseau:', error);
        document.getElementById("status").innerText = "Erreur de com. (Auto)";
    });
}

function mettreAJourUI(mode) {
    const btnManuel = document.getElementById('btn-manuel');
    const btnAuto = document.getElementById('btn-auto');
    const btnAllumer = document.querySelector('.bouton.allumer');
    const btnEteindre = a= document.querySelector('.bouton.eteindre');

    if (mode === 'automatique') {
        btnAuto.classList.add('active');
        btnManuel.classList.remove('active');
        btnAllumer.disabled = true;
        btnEteindre.disabled = true;
    } else {
        btnManuel.classList.add('active');
        btnAuto.classList.remove('active');
        btnAllumer.disabled = false;
        btnEteindre.disabled = false;
        fetcherEtatActuel();
    }
}

function fetcherEtatActuel() {
    fetch('controllers/etat_lumiere.php')
        .then(res => res.json())
        .then(data => {
            document.getElementById("status").innerText = "État : " + data.etat;
            document.getElementById("luminosity-status").innerText = "Luminosité : " + data.luminosite;
        })
        .catch(err => {
            document.getElementById("status").innerText = "État : erreur";
            console.error(err);
        });
}

window.addEventListener('DOMContentLoaded', () => {
    fetch('controllers/get_mode.php')
        .then(res => res.json())
        .then(data => {
            definirMode(data.mode);
        });
});
</script>
    <?php include 'views/common/footer.php'; ?>
</body>
</html>