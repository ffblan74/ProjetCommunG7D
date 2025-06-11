// Fonction pour gérer la sélection des cases à cocher
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    const maxSelections = 3;

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Compte le nombre de cases cochées
            const checkedCount = document.querySelectorAll('input[type="checkbox"]:checked').length;

            // Si le nombre de cases cochées atteint la limite
            if (checkedCount >= maxSelections) {
                checkboxes.forEach(box => {
                    // Désactive les cases restantes non cochées
                    if (!box.checked) {
                        box.disabled = true;
                    }
                });
            } else {
                // Réactive toutes les cases si le nombre de cases cochées est inférieur à la limite
                checkboxes.forEach(box => {
                    box.disabled = false;
                });
            }
        });
    });
});
