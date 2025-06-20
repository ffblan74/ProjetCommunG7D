
let intervalleAutomatique = null;
let intervalleEtatLumiere = null;

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

                 document.getElementById("status").innerText = `État : ${data.etat_texte}`; 
                 document.getElementById("luminosity-status").innerText = `Luminosité actuelle : ${luminosite_actuelle} lx`;

                 fetcherEtatActuel(); 
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
    // 1. On arrête le minuteur "automatique" s'il tourne
    if (intervalleAutomatique) {
        clearInterval(intervalleAutomatique);
        intervalleAutomatique = null; // <--- AJOUT OU MODIFICATION ICI
    }
    // 2. On arrête le minuteur "état lumière" s'il tourne (important pour éviter les doublons)
    if (intervalleEtatLumiere) { // <--- AJOUT OU MODIFICATION ICI
        clearInterval(intervalleEtatLumiere); // <--- AJOUT OU MODIFICATION ICI
        intervalleEtatLumiere = null; // <--- AJOUT OU MODIFICATION ICI
    }

    fetch('controllers/definir_mode.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: `mode=${mode}` })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'succes') {
            mettreAJourUI(mode);

            // 3. On démarre (ou redémarre) le minuteur pour l'affichage de l'état de la lumière
            fetcherEtatActuel(); // On met à jour l'état tout de suite une première fois
            intervalleEtatLumiere = setInterval(fetcherEtatActuel, 3000); // <--- AJOUT OU MODIFICATION ICI : On le fait toutes les 3 secondes

            // 4. Si on est en mode automatique, on démarre aussi son propre minuteur de décision
            if (mode === 'automatique') {
                verifierEtatAutomatique();
                intervalleAutomatique = setInterval(verifierEtatAutomatique, 5000);
            }
        }
    });
}



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
} }




function rafraichirCapteurs() {
    fetch('controllers/get_valeurs_capteurs_json.php')
        .then(res => res.json())
        .then(data => {
            const liste = document.getElementById('liste-capteurs-dynamique');
            liste.innerHTML = ''; // On vide la liste précédente

            // On boucle sur les données reçues et on crée le HTML
            data.forEach(capteur => {
                const item = document.createElement('li');
                item.innerHTML = `
                    <span class="nom-capteur">${capteur.icone} ${capteur.nom}</span>
                    <span class="valeur-capteur">${capteur.valeur}</span>
                `;
                liste.appendChild(item);
            });
        })
        .catch(error => console.error('Erreur de rafraîchissement des capteurs:', error));
}

// ================= Fonctions pour les VOLETS =================

// Envoie la commande "ouvrir" ou "fermer" au serveur
function sendCommandVolet(cmd) {
    fetch('controllers/controle_volet.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `commande=${cmd}`
    })
    .then(res => res.text())
    .then(responseText => {
        console.log("Réponse du serveur (volet):", responseText);
        // On met à jour l'affichage de l'état juste après la commande
        setTimeout(fetcherEtatVolet, 500); 
    });
}

// Récupère l'état actuel des volets et met à jour l'affichage
function fetcherEtatVolet() {
    fetch('controllers/etat_volet.php')
    .then(res => res.json())
    .then(data => {
        console.log('Etat volet reçu:', data);
        document.getElementById("status-volet").innerText = `État : ${data.etat_texte}`;
    })
    
    .catch(err => {
        document.getElementById("status-volet").innerText = "État : erreur";
        console.error(err);
    });
}





window.addEventListener('DOMContentLoaded', () => { 
    fetch('controllers/get_mode.php').then(res => res.json()).then(data => { 
        document.getElementById('input-seuil').value = data.seuil; 
        definirMode(data.mode); 
        rafraichirCapteurs(); 
        setInterval(rafraichirCapteurs, 5000);

        // Gestion des boutons pour les volets
        fetcherEtatVolet();
        setInterval(fetcherEtatVolet, 5000);
    }); });