<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Démarre la session uniquement si elle n'est pas déjà active
}

// Vérification si l'utilisateur est connecté et si son rôle est soit organisateur soit administrateur
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || ($_SESSION['role'] !== 'organisateur' && $_SESSION['role'] !== 'administrateur')) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté ou n'a pas le bon rôle
    header('Location: /src/?page=login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création d'événement</title>
    <link rel="stylesheet" href="/src/assets/CSS/create_event.css">
    <link rel="icon" type="image/x-icon" href="/src/assets/favicon.jpg">
</head>
<body>
    <?php include 'common/header.php'; ?>

    <!-- Message d'erreur -->
    <?php if (isset($_SESSION['error_message'])): ?>
    <div class="error-message">
        <?php 
        echo htmlspecialchars($_SESSION['error_message']); 
        unset($_SESSION['error_message']);
        ?>
    </div>
    <?php endif; ?>

    <!-- Pop-up qui n'apparait que si l'événement a été créé -->
    <div id="popup" class="card" style="display: none;">
      <button class="dismiss" type="button" onclick="closePopup()">×</button>
      <div class="header">
        <div class="div_image_v">
          <div class="image">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <g id="SVGRepo_iconCarrier">
                <path d="M20 7L9.00004 18L3.99994 13" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
              </g>
            </svg>
          </div>
        </div>
        <div class="content">
          <span class="title">Votre événement a été créé avec succès !</span>
          <p class="message">Votre événement sera visible dans les événements à venir après validation par l'administrateur.</p>
          <button id="close-popup" class="popup-button">OK</button>
        </div>
      </div>
    </div>

    <div class="event-container">
        <h1>Créer un événement</h1>
        <form class="create-event-form" method="POST" action="/src/?page=create_event" enctype="multipart/form-data">
            <div class="form-group">
                <label for="event-name">Nom de l'événement</label>
                <input type="text" id="event-name" name="event-name" placeholder="Ex: Soirée caritative" required>
            </div>

            <div class="form-group">
                <label for="description">Description de l'événement</label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label>Exigences pour les participants</label>
                <div id="requirements-container">
                    <div class="requirement-item">
                        <input type="text" name="requirements[]" placeholder="Ex: Apporter des gâteaux" class="requirement-input">
                        <button type="button" class="btn-remove-requirement" onclick="removeRequirement(this)">×</button>
                    </div>
                </div>
                <button type="button" class="btn-add-requirement" onclick="addRequirement()">+ Ajouter une exigence</button>
            </div>

            <div class="form-group">
                <label for="date_planifiee">Date de l'événement*</label>
                <?php 
                $today = date('Y-m-d');
                $minTime = date('H:i');
                ?>
                <input type="date" 
                       id="date_planifiee" 
                       name="date_planifiee" 
                       min="<?= $today ?>" 
                       required>
                <input type="time" 
                       id="heure_planifiee" 
                       name="heure_planifiee" 
                       <?= date('Y-m-d') === $today ? "min=\"$minTime\"" : '' ?> 
                       required>
            </div>

            <div class="form-group">
                <label for="event-duration">Durée (heures)</label>
                <input type="number" id="event-duration" min="1" max="72" name="event-duration" placeholder="Ex: 4" required>
            </div>

            <div class="form-group">
                <label for="event-location">Lieu de l'événement</label>
                <input type="text" id="event-location" name="event-location" placeholder="Entrez une adresse" required>
                <div id="map"></div>
            </div>

            <div class="form-group">
                <label for="participants">Nombre maximum de participants</label>
                <div class="participants-container">
                    <input type="range" id="participants" name="participants" min="1" max="1000" value="50">
                    <span id="participants-value">50</span>
                </div>
            </div>

            <div class="form-group">
                <label for="event-status">Statut de l'événement</label>
                <select id="event-status" name="event-status" required>
                    <option value="ouvert">Ouvert - Les participants peuvent s'inscrire directement</option>
                    <option value="sous_reserve">Sous réserve - Les inscriptions nécessitent une approbation</option>
                    <option value="ferme">Fermé - Les inscriptions sont fermées</option>
                </select>
            </div>

            <div class="form-group">
                <label for="event-image">Image de l'événement</label>
                <div class="file-upload-container">
                    <label for="event-image" class="file-upload-label">
                        <i class="fas fa-cloud-upload-alt"></i>
                        Cliquez ou déposez votre image ici
                    </label>
                    <input type="file" id="event-image" name="event-image" accept="image/*" class="file-upload-input">
                    <div id="image-preview"></div>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-calendar-plus"></i>
                Créer l'événement
            </button>
        </form>
    </div>

    <script>
    // Mise à jour du compteur de participants
    const participantsInput = document.getElementById('participants');
    const participantsValue = document.getElementById('participants-value');
    
    participantsInput.addEventListener('input', function() {
        participantsValue.textContent = this.value;
    });

    // Prévisualisation de l'image
    const imageInput = document.getElementById('event-image');
    const imagePreview = document.getElementById('image-preview');
    const uploadLabel = document.querySelector('.file-upload-label');

    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.innerHTML = `<img src="${e.target.result}" alt="Prévisualisation">`;
                uploadLabel.textContent = file.name;
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.innerHTML = '';
            uploadLabel.innerHTML = '<i class="fas fa-cloud-upload-alt"></i> Cliquez ou déposez votre image ici';
        }
    });
    </script>

    <script src="/src/assets/JS/create_event.js"></script>

    <!-- Déclaration de initMap avant le chargement de l'API -->
    <script>
        window.initMap = function() {
            console.log("Fonction initMap appelée");
            
            try {
                console.log("Vérification de l'élément map...");
                const mapElement = document.getElementById('map');
                if (!mapElement) {
                    console.error("Élément #map non trouvé dans le DOM");
                    return;
                }
                console.log("Élément map trouvé:", mapElement);

                // Centre par défaut sur Paris
                const paris = { lat: 48.8566, lng: 2.3522 };
                console.log("Coordonnées de Paris définies:", paris);
                
                console.log("Tentative de création de la carte...");
                const map = new google.maps.Map(mapElement, {
                    center: paris,
                    zoom: 12
                });
                console.log("Carte créée avec succès");

                console.log("Création du marqueur...");
                const marker = new google.maps.Marker({
                    position: paris,
                    map: map
                });
                console.log("Marqueur créé avec succès");

                console.log("Recherche de l'input location...");
                const input = document.getElementById('event-location');
                if (!input) {
                    console.error("Élément #event-location non trouvé");
                    return;
                }
                console.log("Input location trouvé:", input);

                console.log("Initialisation de l'autocomplete...");
                const autocomplete = new google.maps.places.Autocomplete(input);
                console.log("Autocomplete créé");

                console.log("Liaison de l'autocomplete avec la carte...");
                autocomplete.bindTo('bounds', map);
                console.log("Liaison effectuée");

                console.log("Ajout du listener pour place_changed...");
                autocomplete.addListener('place_changed', function() {
                    console.log("Événement place_changed déclenché");
                    const place = autocomplete.getPlace();
                    console.log("Place récupérée:", place);

                    if (!place.geometry) {
                        console.error("Aucune géométrie trouvée pour:", place);
                        return;
                    }

                    console.log("Géométrie trouvée, mise à jour de la carte...");
                    if (place.geometry.viewport) {
                        console.log("Ajustement aux limites du viewport");
                        map.fitBounds(place.geometry.viewport);
                    } else {
                        console.log("Centrage sur le lieu");
                        map.setCenter(place.geometry.location);
                        map.setZoom(17);
                    }

                    console.log("Mise à jour du marqueur...");
                    marker.setPosition(place.geometry.location);
                    marker.setVisible(true);
                    console.log("Marqueur mis à jour");
                });
                console.log("Listener ajouté avec succès");

            } catch (error) {
                console.error("Erreur lors de l'initialisation de la carte:", error);
                console.error("Stack trace:", error.stack);
            }
        }
    </script>

    <!-- Chargement de l'API Google Maps -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDI0jT3i9a-Qq-F1vd00GWHMCpjmHHmHzM&libraries=places&callback=initMap" async defer></script>

    <script>
    // Gestion des exigences
    function addRequirement() {
        const container = document.getElementById('requirements-container');
        const newItem = document.createElement('div');
        newItem.className = 'requirement-item';
        newItem.innerHTML = `
            <input type="text" name="requirements[]" placeholder="Ex: Apporter des gâteaux" class="requirement-input">
            <button type="button" class="btn-remove-requirement" onclick="removeRequirement(this)">×</button>
        `;
        container.appendChild(newItem);
    }

    function removeRequirement(button) {
        const container = document.getElementById('requirements-container');
        const item = button.parentElement;
        if (container.children.length > 1) {
            container.removeChild(item);
        } else {
            item.querySelector('input').value = '';
        }
    }

    // Si le paramètre success est dans l'URL, afficher le pop-up
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success') && urlParams.get('success') === 'true') {
        document.getElementById('popup').style.display = 'block';
    }

    // Fonction pour fermer le pop-up
    document.getElementById("close-popup").addEventListener("click", function() {
        var popup = document.getElementById('popup');
        if (popup) {
            popup.style.display = "none";  // Cacher le pop-up
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('date_planifiee');
        const timeInput = document.getElementById('heure_planifiee');
        
        // Met à jour la date minimale
        function updateMinDate() {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const formattedDate = `${yyyy}-${mm}-${dd}`;
            
            dateInput.min = formattedDate;
            
            // Si la date sélectionnée est aujourd'hui, on met à jour l'heure minimale
            if (dateInput.value === formattedDate) {
                const hh = String(today.getHours()).padStart(2, '0');
                const min = String(today.getMinutes()).padStart(2, '0');
                timeInput.min = `${hh}:${min}`;
            } else {
                timeInput.min = ''; // Pas de minimum si la date est future
            }
        }

        // Met à jour les contraintes au chargement
        updateMinDate();

        // Met à jour l'heure minimale quand la date change
        dateInput.addEventListener('change', function() {
            const today = new Date();
            const selectedDate = new Date(this.value);
            
            // Réinitialise l'heure si la date sélectionnée est aujourd'hui et l'heure est passée
            if (selectedDate.toDateString() === today.toDateString()) {
                const currentHour = String(today.getHours()).padStart(2, '0');
                const currentMinute = String(today.getMinutes()).padStart(2, '0');
                timeInput.min = `${currentHour}:${currentMinute}`;
                
                if (timeInput.value < timeInput.min) {
                    timeInput.value = timeInput.min;
                }
            } else {
                timeInput.min = '';
            }
        });
    });
    </script>

</body>
</html>
