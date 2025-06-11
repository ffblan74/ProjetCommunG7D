// Gestion de la navigation active dans la sidebar
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer le paramètre view de l'URL
    const urlParams = new URLSearchParams(window.location.search);
    const currentView = urlParams.get('view') || 'overview';

    // Mettre à jour la classe active
    const activeLink = document.querySelector(`.sidebar nav a[href*="view=${currentView}"]`);
    if (activeLink) {
        document.querySelectorAll('.sidebar nav li').forEach(li => li.classList.remove('active'));
        activeLink.parentElement.classList.add('active');
    }
}); 