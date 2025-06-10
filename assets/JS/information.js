// Récupérer les éléments du DOM
const dropdownButton = document.getElementById("dropdownButton");
const dropdownContent = document.getElementById("dropdownContent");

// Ajouter un événement pour afficher/masquer le menu
dropdownButton.addEventListener("click", function (event) {
    event.preventDefault(); // Empêcher le lien par défaut
    dropdownContent.style.display =
        dropdownContent.style.display === "block" ? "none" : "block";
});
