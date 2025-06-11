// Variables
const participantsSlider = document.getElementById('participants');
const participantsCount = document.getElementById('participants-count');
const addRequirementButton = document.getElementById('add-requirement');
const requirementsContainer = document.getElementById('requirements-container');
const eventDate = document.getElementById('event-date');
const locationInput = document.getElementById('event-location');
const form = document.querySelector('.create-event-form');

// Slider
participantsSlider.addEventListener('input', () => {
    participantsCount.textContent = participantsSlider.value;
});

// Exigences
addRequirementButton.addEventListener('click', () => {
    const newRequirement = document.createElement('div');
    newRequirement.classList.add('requirement-item');

    const requirementName = document.createElement('input');
    requirementName.type = 'text';
    requirementName.name = 'nomExigences[]';
    requirementName.placeholder = "Nom de l'exigence";

    const requirementQuantity = document.createElement('input');
    requirementQuantity.type = 'number';
    requirementQuantity.name = 'quantiteExigences[]';
    requirementQuantity.placeholder = "Quantité";
    requirementQuantity.min = 1;

    newRequirement.appendChild(requirementName);
    newRequirement.appendChild(requirementQuantity);
    requirementsContainer.insertBefore(newRequirement, addRequirementButton);
});

// Envoyer exigences au formulaire
form.addEventListener('submit', (event) => {
    const requirementInputs = document.querySelectorAll('.requirement-item input');
    requirementInputs.forEach((input) => {
        if (input.value.trim() === "") {
            alert("Veuillez remplir tous les champs d'exigence.");
            event.preventDefault();
        }
    });
});

// Date d'evenement
const validateEventDate = () => {
    const today = new Date().toISOString().split('T')[0];
    eventDate.setAttribute('min', today);
};
validateEventDate();

// Google Maps
let map;
let marker;
let autocomplete;

console.log("Script Google Maps chargé");

function initMap() {
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
        map = new google.maps.Map(mapElement, {
            center: paris,
            zoom: 12
        });
        console.log("Carte créée avec succès");

        console.log("Création du marqueur...");
        marker = new google.maps.Marker({
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
        autocomplete = new google.maps.places.Autocomplete(input);
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

// Vérification du chargement de l'API Google Maps
window.addEventListener('load', () => {
    console.log("Page chargée, vérification de google.maps...");
    if (typeof google === 'undefined') {
        console.error("L'API Google Maps n'est pas chargée");
    } else {
        console.log("L'API Google Maps est disponible");
    }
});


