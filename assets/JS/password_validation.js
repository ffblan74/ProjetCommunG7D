document.addEventListener('DOMContentLoaded', function () {
    // ---- Gestion des erreurs lors de la soumission ----
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm-password');
    const form = document.querySelector('form');
    const errorList = document.createElement('ul');
    errorList.style.color = 'red';

    // Vérification des règles du mot de passe à la soumission
    if (form) {
        form.addEventListener('submit', function (e) {
            const password = passwordInput.value;
            const errors = [];

            // Vérification des règles
            if (password.length < 8) {
                errors.push("Le mot de passe doit contenir au moins 8 caractères.");
            }
            if (!/[A-Z]/.test(password)) {
                errors.push("Le mot de passe doit contenir au moins une lettre majuscule.");
            }
            if (!/[!@#$%^&*(),.?\":{}|<>]/.test(password)) {
                errors.push("Le mot de passe doit contenir au moins un caractère spécial.");
            }

            // Affichage des erreurs
            if (errors.length > 0) {
                e.preventDefault();
                errorList.innerHTML = "";
                errors.forEach(error => {
                    const li = document.createElement('li');
                    li.textContent = error;
                    errorList.appendChild(li);
                });
                if (!form.contains(errorList)) {
                    form.insertBefore(errorList, form.querySelector('.btn'));
                }
            }
        });
    }

    // ---- Vérification en direct des mots de passe ----
    const passwordFeedback = document.createElement('p');
    passwordFeedback.style.fontSize = '12px';
    passwordFeedback.style.marginTop = '5px';

    if (confirmPasswordInput) {
        confirmPasswordInput.insertAdjacentElement('afterend', passwordFeedback);

        function checkPasswordMatch() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (confirmPassword.length > 0) {
                if (password === confirmPassword) {
                    passwordFeedback.textContent = "Les mots de passe correspondent.";
                    passwordFeedback.style.color = "green";
                } else {
                    passwordFeedback.textContent = "Les mots de passe ne correspondent pas.";
                    passwordFeedback.style.color = "red";
                }
            } else {
                passwordFeedback.textContent = "";
            }
        }

        passwordInput.addEventListener('input', checkPasswordMatch);
        confirmPasswordInput.addEventListener('input', checkPasswordMatch);
    }

    // ---- Affichage/Masquage des mots de passe ----
    function togglePasswordVisibility(inputFieldId, toggleButtonId) {
        const inputField = document.getElementById(inputFieldId);
        const toggleButton = document.getElementById(toggleButtonId);

        if (inputField && toggleButton) {
            toggleButton.addEventListener('click', function () {
                if (inputField.type === 'password') {
                    inputField.type = 'text';
                    toggleButton.style.opacity = '0.6';
                    toggleButton.src = '../assets/eye-icon.png'; // Icône "œil barré"
                } else {
                    inputField.type = 'password';
                    toggleButton.style.opacity = '1';
                    toggleButton.src = '../assets/eye-icon.png'; // Icône "œil ouvert"
                }
            });
        }
    }

    // Appliquer la fonction pour les champs mot de passe
    togglePasswordVisibility('password', 'toggle-password');
    togglePasswordVisibility('confirm-password', 'toggle-confirm-password');
});
