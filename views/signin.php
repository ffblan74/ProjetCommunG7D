<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte</title>
    <link rel="stylesheet" href="assets/CSS/signin.css">
    <link rel="stylesheet" href="assets/CSS/popup.css">
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.png">
    <style>
        .password-input-container {
            position: relative;
            width: 100%;
        }

        .password-input-container input {
            width: 100%;
            height: 40px;
            padding-right: 40px;
            box-sizing: border-box;
        }

        .password-input-container img {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            width: 35px;
            height: 35px;
            opacity: 0.8;
        }

        /* Style pour la bulle d'information */
        .password-tooltip {
            display: none;
            position: absolute;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            width: 250px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            font-size: 13px;
            color: #666;
            top: calc(100% + 10px);
            left: 0;
        }

        .password-tooltip ul {
            margin: 0;
            padding-left: 20px;
        }

        .password-tooltip li {
            margin: 5px 0;
        }

        .password-input-container:hover .password-tooltip {
            display: block;
        }

        /* Style moderne pour les boutons radio */
        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }

        .radio-option {
            flex: 1;
            position: relative;
        }

        .radio-option input[type="radio"] {
            display: none;
        }

        .radio-option label {
            display: block;
            padding: 12px 20px;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            font-weight: 500;
            color: #495057;
            transition: all 0.2s ease;
        }

        .radio-option input[type="radio"]:checked + label {
            background: #4CAF50;
            border-color: #4CAF50;
            color: white;
            box-shadow: 0 2px 4px rgba(76, 175, 80, 0.15);
        }

        .radio-option label:hover {
            border-color: #4CAF50;
            background: #f1f8f1;
            color: #4CAF50;
        }

        .radio-option input[type="radio"]:checked + label:hover {
            background: #43A047;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Pop-up de confirmation -->
    <div class="popup-overlay <?= $showPopup ? 'show' : '' ?>" id="successPopup">
        <div class="popup">
            <div class="popup-icon">
                <svg viewBox="0 0 24 24">
                    <path class="checkmark" fill="none" d="M4 12l6 6L20 6" />
                </svg>
            </div>
            <h2 class="popup-title">Inscription réussie !</h2>
            <p class="popup-message">
                Merci de vous être inscrit. Vous pouvez maintenant vous connecter pour accéder à toutes les fonctionnalités.
            </p>
            <a href="?page=login" class="popup-button">Se connecter</a>
        </div>
    </div>

    <div class="signin-container">
        <div class="signin-box">
            <h1>Créer un compte</h1>
            
            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="?page=signin">
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" placeholder="Votre nom" required>
                </div>


                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Votre adresse email" required>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <div class="password-input-container">
                        <input type="password" id="password" name="password" placeholder="Votre mot de passe" required>
                        <img src="assets/images/eye-icon.png" alt="Afficher le mot de passe" id="toggle-password">
                        <div class="password-tooltip">
                            <strong>Le mot de passe doit contenir :</strong>
                            <ul>
                                <li>Au moins 8 caractères</li>
                                <li>Au moins une majuscule</li>
                                <li>Au moins une minuscule</li>
                                <li>Au moins un chiffre</li>
                                <li>Au moins un caractère spécial (!@#$%^&*)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm-password">Confirmer le mot de passe</label>
                    <div class="password-input-container">
                        <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirmez votre mot de passe" required>
                        <img src="assets/images/eye-icon.png" alt="Afficher le mot de passe" id="toggle-confirm-password">
                    </div>
                    <div id="password-feedback" class="password-feedback"></div>
                </div>

                

                <button type="submit" class="btn">S'inscrire</button>
            </form>

            <div class="links">
                <a href="?page=login">Déjà inscrit ? Se connecter</a>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion du popup
        const popup = document.getElementById('successPopup');
        if (popup.classList.contains('show')) {
            document.body.style.overflow = 'hidden';
        }

        popup.addEventListener('click', function(e) {
            if (e.target === popup) {
                window.location.href = '?page=login';
            }
        });

        // Gestion des boutons œil pour les mots de passe
        function togglePassword(inputId, toggleId) {
            const input = document.getElementById(inputId);
            const toggle = document.getElementById(toggleId);

            toggle.addEventListener('click', function() {
                if (input.type === 'password') {
                    input.type = 'text';
                    toggle.style.opacity = '1';
                } else {
                    input.type = 'password';
                    toggle.style.opacity = '0.8';
                }
            });
        }

        togglePassword('password', 'toggle-password');
        togglePassword('confirm-password', 'toggle-confirm-password');

        // Validation des mots de passe
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm-password');
        const passwordFeedback = document.getElementById('password-feedback');

        function validatePasswords() {
            const passwordValue = passwordInput.value;
            const confirmPasswordValue = confirmPasswordInput.value;

            if (confirmPasswordValue.length > 0) {
                if (passwordValue === confirmPasswordValue) {
                    passwordFeedback.textContent = '';
                } else {
                    passwordFeedback.textContent = "Les mots de passe ne correspondent pas";
                    passwordFeedback.classList.add('invalid');
                    passwordFeedback.classList.remove('valid');
                }
            } else {
                passwordFeedback.textContent = '';
            }
        }

        confirmPasswordInput.addEventListener('input', validatePasswords);
    });
    </script>
</body>
</html>
