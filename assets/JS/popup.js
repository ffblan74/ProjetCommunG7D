// Gestion du menu déroulant des villes
const cityButton = document.getElementById('cityButton');
const cityDropdownMenu = document.getElementById('cityDropdownMenu');
const dropdownItems = document.querySelectorAll('.dropdown-item');

// Afficher/Masquer le menu déroulant lorsqu'on clique sur le bouton
cityButton.addEventListener('click', (event) => {
  event.stopPropagation(); // Empêche la propagation du clic
  const isVisible = cityDropdownMenu.style.display === 'block';
  cityDropdownMenu.style.display = isVisible ? 'none' : 'block';
});

// Fermer le menu si on clique en dehors du menu déroulant
document.addEventListener('click', () => {
  cityDropdownMenu.style.display = 'none';
});

// Mettre à jour le texte du bouton avec la ville sélectionnée
dropdownItems.forEach(item => {
  item.addEventListener('click', (event) => {
    cityButton.textContent = event.target.textContent; // Mise à jour du texte du bouton
    cityDropdownMenu.style.display = 'none'; // Fermer le menu
  });
});

// Gestion du pop-up de filtre
const openPopup = document.getElementById('openFilterPopup'); // Bouton pour ouvrir le pop-up
const closePopup = document.getElementById('closeFilterPopup'); // Bouton pour fermer le pop-up
const filterPopup = document.getElementById('filterPopup'); // Conteneur du pop-up

// Ouvrir le pop-up en cliquant sur le bouton "Filtre"
openPopup.addEventListener('click', () => {
  filterPopup.classList.add('visible');
});

// Fermer le pop-up en cliquant sur le bouton "Fermer"
closePopup.addEventListener('click', () => {
  filterPopup.classList.remove('visible');
});

// Fermer le pop-up en cliquant en dehors du contenu
filterPopup.addEventListener('click', (e) => {
  if (e.target === filterPopup) {
    filterPopup.classList.remove('visible');
  }
});

// Gestion du clic sur le bouton "Confirmer"
document.getElementById('confirmFilterPopup').addEventListener('click', function () {
  // Ajoutez ici l'action à exécuter lors de la confirmation
  alert("Filtres confirmés !");
  // Ferme le pop-up après confirmation
  filterPopup.style.display = 'none';
});
