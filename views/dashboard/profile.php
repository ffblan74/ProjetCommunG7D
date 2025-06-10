<?php require_once 'views/dashboard/organizer_layout.php'; ?>

<!-- Popup de succès -->
<div id="successPopup" class="popup-overlay" style="display: none;">
    <div class="popup">
        <div class="popup-content">
            <i class="fas fa-check-circle success-icon"></i>
            <h3>Succès</h3>
            <p id="popupMessage">Les modifications ont été enregistrées avec succès.</p>
            <button onclick="closePopup()" class="btn btn-primary">Fermer</button>
        </div>
    </div>
</div>

<!-- Popup d'erreur -->
<div id="errorPopup" class="popup-overlay" style="display: none;">
    <div class="popup">
        <div class="popup-content">
            <i class="fas fa-exclamation-circle error-icon"></i>
            <h3>Erreur</h3>
            <p id="errorMessage"></p>
            <button onclick="closePopup()" class="btn btn-primary">Fermer</button>
        </div>
    </div>
</div>

<style>
.popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.popup {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    max-width: 400px;
    width: 90%;
}

.popup-content {
    text-align: center;
}

.success-icon {
    color: #28a745;
    font-size: 48px;
    margin-bottom: 15px;
}

.error-icon {
    color: #dc3545;
    font-size: 48px;
    margin-bottom: 15px;
}

.popup h3 {
    margin: 10px 0;
    color: #333;
}

.popup p {
    margin: 15px 0;
    color: #666;
}

.popup button {
    padding: 8px 20px;
    border: none;
    border-radius: 4px;
    background-color: #007bff;
    color: white;
    cursor: pointer;
    transition: background-color 0.3s;
}

.popup button:hover {
    background-color: #0056b3;
}

.password-input-container {
    position: relative;
    display: flex;
    align-items: center;
}

.password-input-container input {
    width: 100%;
    padding-right: 35px;
}

.toggle-password {
    position: absolute;
    right: 10px;
    cursor: pointer;
    color: #666;
    transition: color 0.3s;
}

.toggle-password:hover {
    color: #333;
}
</style>

<div class="event-form">
    <h1>Mon Profil</h1>

    <div class="profile-header">
        <img src="/src/assets/images/profile/<?= htmlspecialchars($userData['photo_profil'] ?? 'default.png') ?>" alt="Photo de profil" class="profile-image">
        <div class="profile-title">
            <h2><?= htmlspecialchars($userData['prenom'] . ' ' . $userData['nom']) ?></h2>
            <span class="role">Organisateur</span>
        </div>
    </div>

    <form id="profileForm" method="POST" action="/src/?page=dashboard&action=update_profile" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($userData['prenom']) ?>" required>
            </div>

            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($userData['nom']) ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>" required>
            </div>

            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($userData['telephone'] ?? '') ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="photo_profil">Photo de profil</label>
            <input type="file" id="photo_profil" name="photo_profil" accept="image/*">
        </div>

        <div class="button-group">
            <button type="submit">Enregistrer les modifications</button>
            <button type="reset" class="btn-secondary">Réinitialiser</button>
        </div>
    </form>

    <form id="passwordForm" method="POST" action="/src/?page=dashboard&action=update_password">
        <h3 class="section-title">Sécurité</h3>

        <div class="form-row">
            <div class="form-group">
                <label for="current_password">Mot de passe actuel</label>
                <div class="password-input-container">
                    <input type="password" id="current_password" name="current_password">
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('current_password')"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="new_password">Nouveau mot de passe</label>
                <div class="password-input-container">
                    <input type="password" id="new_password" name="new_password">
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('new_password')"></i>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirmer le mot de passe</label>
            <div class="password-input-container">
                <input type="password" id="confirm_password" name="confirm_password">
                <i class="fas fa-eye toggle-password" onclick="togglePassword('confirm_password')"></i>
            </div>
        </div>

        <div class="button-group">
            <button type="submit">Modifier le mot de passe</button>
            <button type="reset" class="btn-secondary">Réinitialiser</button>
        </div>
    </form>
</div>

<script>
function showPopup(type, message) {
    const popup = document.getElementById(type === 'success' ? 'successPopup' : 'errorPopup');
    const messageElement = document.getElementById(type === 'success' ? 'popupMessage' : 'errorMessage');
    messageElement.textContent = message;
    popup.style.display = 'flex';
}

function closePopup() {
    document.getElementById('successPopup').style.display = 'none';
    document.getElementById('errorPopup').style.display = 'none';
    if (document.getElementById('successPopup').style.display === 'flex') {
        location.reload();
    }
}

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling;
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Status:', response.status);
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Réponse non-JSON:', text);
                throw new Error('Réponse invalide du serveur');
            }
        });
    })
    .then(data => {
        console.log('Réponse du serveur:', data);
        if (data.success) {
            showPopup('success', 'Profil mis à jour avec succès');
            setTimeout(() => {
                closePopup();
                location.reload();
            }, 2000);
        } else {
            const message = data.message || 'Erreur lors de la mise à jour du profil';
            showPopup('error', message);
        }
    })
    .catch(error => {
        console.error('Erreur complète:', error);
        showPopup('error', 'Une erreur est survenue lors de la mise à jour du profil: ' + error.message);
    });
});

document.getElementById('passwordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showPopup('success', 'Mot de passe mis à jour avec succès');
            this.reset();
        } else {
            const message = data.message || 'Erreur lors de la mise à jour du mot de passe';
            showPopup('error', message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showPopup('error', 'Une erreur est survenue lors de la mise à jour du mot de passe');
    });
});
</script> 