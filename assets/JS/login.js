document.addEventListener('DOMContentLoaded', function () {
    // Fonction pour afficher/masquer le mot de passe
    function togglePasswordVisibility(inputId, toggleIconId) {
        const passwordInput = document.getElementById(inputId);
        const toggleIcon = document.getElementById(toggleIconId);

        toggleIcon.addEventListener('click', function () {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.src = '../assets/eye-icon.png';
            } else {
                passwordInput.type = 'password';
                toggleIcon.src = '../assets/eye-icon.png';
            }
        });
    }

    // Appliquer au champ mot de passe
    togglePasswordVisibility('password', 'toggle-password');
});
