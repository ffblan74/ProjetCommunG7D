
let intervalleAutomatique = null;


function verifierEtatAutomatique() {
    // 1. On demande au serveur l'état actuel des capteurs
    fetch('controllers/etat_lumiere.php')
        .then(res => res.json())
        .then(data => {
            // Si on n'a pas de valeur de luminosité, on ne fait rien
            if (data.luminosite === 'N/A') return;

            // 2. On récupère les informations pour la décision
            const etat_actuel = data.etat_valeur; // 0.0 ou 1.0
            const luminosite_actuelle = data.luminosite;
            const seuil = parseFloat(document.getElementById('input-seuil').value);

            // 3. Le JavaScript prend la décision
            // Rappel de la logique : si luminosité > seuil, on ALLUME (1.0)
            const etat_desire = (luminosite_actuelle < seuil) ? 1.0 : 0.0;
            
            // 4. On compare l'état actuel et l'état désiré
            if (etat_desire !== etat_actuel) {
                // Si c'est différent, on envoie la commande pour changer l'état
                console.log(`Action requise: passer de ${etat_actuel} à ${etat_desire} car luminosité (${luminosite_actuelle}) > seuil (${seuil})`);
                sendCommand(etat_desire.toString()); // On appelle le contrôleur simple
            } else {
                 // Si c'est identique, on ne fait rien, juste on met à jour l'affichage
                 document.getElementById("status").innerText = `État : ${data.etat_texte} (Auto)`;
                 document.getElementById("luminosity-status").innerText = `Luminosité actuelle : ${luminosite_actuelle} lx`;
            }
        })
        .catch(error => {
            console.error('Erreur durant la vérification automatique:', error);
            document.getElementById("status").innerText = "Erreur de com. (Auto)";
        });
}

// Envoie une commande au contrôleur simple
function sendCommand(cmd) {
    fetch('controllers/controle_lumiere.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `commande=${cmd}`
    })
    .then(res => res.text())
    .then(responseText => { // On renomme la variable pour plus de clarté
        console.log(responseText); // Affiche le rapport du script PHP
        
        setTimeout(fetcherEtatActuel, 500);
    });
}

// Met à jour l'affichage de l'état (manuel et automatique)
function fetcherEtatActuel() {
    fetch('controllers/etat_lumiere.php')
        .then(res => res.json())
        .then(data => {
            const mode_actuel = document.getElementById('btn-auto').classList.contains('active') ? "(Auto)" : "(Manuel)";
            document.getElementById("status").innerText = `État : ${data.etat_texte} ${mode_actuel}`;
            document.getElementById("luminosity-status").innerText = "Luminosité actuelle : " + data.luminosite + " lx";
        })
        .catch(err => {
            document.getElementById("status").innerText = "État : erreur";
            console.error(err);
        });
}

// Le reste des fonctions pour gérer l'UI et la sauvegarde du seuil (inchangées)
function definirMode(mode) { 
    if (intervalleAutomatique) { clearInterval(intervalleAutomatique); } 
    fetch('controllers/definir_mode.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: `mode=${mode}` }).then(res => res.json()).then(data => { if (data.status === 'succes') { mettreAJourUI(mode); 
        if (mode === 'automatique') { verifierEtatAutomatique(); intervalleAutomatique = setInterval(verifierEtatAutomatique, 5000); } } 
    }); }
function sauvegarderSeuil() { 
    const seuil = document.getElementById('input-seuil').value; 
    const confirmation = document.getElementById('confirmation-seuil'); 
    fetch('controllers/sauvegarder_seuil.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: `seuil=${seuil}` }).then(res => res.json()).then(data => { if(data.status === 'succes') { confirmation.innerText = "✔ Enregistré !"; 
        setTimeout(() => { confirmation.innerText = ""; }, 2000); 
        if(document.getElementById('btn-auto').classList.contains('active')) { verifierEtatAutomatique(); } } }); }
function mettreAJourUI(mode) { 
    const btnManuel = document.getElementById('btn-manuel'); 
    const btnAuto = document.getElementById('btn-auto'); 
    const btnAllumer = document.querySelector('.bouton.allumer'); 
    const btnEteindre = document.querySelector('.bouton.eteindre'); 
    const zoneSeuil = document.getElementById('zone-seuil-auto'); 
    if (mode === 'automatique') { btnAuto.classList.add('active'); 
        btnManuel.classList.remove('active'); 
        btnAllumer.disabled = true; 
        btnEteindre.disabled = true; 
        zoneSeuil.style.display = 'block'; } 
        else { btnManuel.classList.add('active'); 
            btnAuto.classList.remove('active'); 
            btnAllumer.disabled = false; 
            btnEteindre.disabled = false; 
            zoneSeuil.style.display = 'none'; 
            fetcherEtatActuel(); } }
window.addEventListener('DOMContentLoaded', () => { fetch('controllers/get_mode.php').then(res => res.json()).then(data => { document.getElementById('input-seuil').value = data.seuil; definirMode(data.mode); }); });