<?php
if (!isset($userData) || !is_array($userData)) {
    echo "Erreur : Impossible de charger les données utilisateur.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once 'common/header.php'; ?>
    <link rel="stylesheet" href="/src/assets/CSS/client_profil.css">
</head>
<body>
    <!-- Popup de mise à jour -->
    <div id="updatePopup" class="popup-overlay" style="display: none;">
        <div class="popup">
            <div class="popup-content">
                <i class="fas fa-check-circle success-icon"></i>
                <h3>Mise à jour réussie</h3>
                <p>Vos informations ont été mises à jour avec succès.</p>
                <button onclick="closePopup('updatePopup')" class="btn btn-primary">Fermer</button>
            </div>
        </div>
    </div>

    <div class="dashboard-container">
        <!-- Sidebar à gauche -->
        <aside class="profile-sidebar">
            <div class="user-profile-header">
                <i class="fas fa-user-circle profile-icon"></i>
                <div class="user-details">
                    <h1><?= htmlspecialchars($userData['prenom']) ?> <?= htmlspecialchars($userData['nom']) ?></h1>
                    <p class="user-meta">
                        <i class="fas fa-calendar-alt"></i> Membre depuis <?php 
                            setlocale(LC_TIME, 'fr_FR.UTF8', 'fr.UTF8', 'fr_FR.UTF-8', 'fr.UTF-8');
                            echo strftime('%B %Y', strtotime($userData['date_inscription'])); 
                        ?>
                    </p>
                </div>
            </div>

            <div class="profile-tabs">
                <button class="tab-button active" onclick="showTab('events')">
                    <i class="fas fa-calendar-alt"></i>
                    Mes événements (<?= $eventCount ?>)
                </button>
                <button class="tab-button" onclick="showTab('account')">
                    <i class="fas fa-cog"></i>
                    Paramètres du compte
                </button>
            </div>
        </aside>

        <!-- Contenu principal -->
        <main class="main-content">
            <!-- Onglet Événements -->
            <div class="tab-content" id="events-tab">
                <div class="events-section">
                    <div class="events-header">
                        <h2><i class="fas fa-calendar"></i> Mes événements</h2>
                        <a href="/src/?page=explorer" class="btn btn-outline">Découvrir plus d'événements</a>
                    </div>
                    <?php if (empty($userEvents)): ?>
                        <div class="no-events">
                            <i class="fas fa-calendar-times"></i>
                            <p>Vous n'êtes inscrit à aucun événement pour le moment.</p>
                            <a href="/src/?page=explorer" class="btn btn-primary">Découvrir les événements</a>
                        </div>
                    <?php else: ?>
                        <div class="events-grid">
                            <?php foreach ($userEvents as $event): ?>
                                <div class="event-card">
                                    <div class="event-image">
                                        <?php if (isset($event['image']) && !empty($event['image'])): ?>
                                            <img src="/src/assets/images/events/<?= htmlspecialchars($event['image']) ?>" alt="<?= htmlspecialchars($event['nom_event']) ?>">
                                        <?php else: ?>
                                            <img src="/src/assets/images/default-event.jpg" alt="Image par défaut">
                                        <?php endif; ?>
                                        <div class="event-status <?= $event['inscription_statut'] === 'approuvé' ? 'approved' : 'pending' ?>">
                                            <?= $event['inscription_statut'] === 'approuvé' ? 'Inscrit' : 'En attente' ?>
                                        </div>
                                    </div>
                                    <div class="event-info">
                                        <h3><?= htmlspecialchars($event['nom_event']) ?></h3>
                                        <div class="event-meta">
                                            <span><i class="fas fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($event['date_planifiee'])) ?></span>
                                            <span><i class="fas fa-clock"></i> <?= date('H:i', strtotime($event['date_planifiee'])) ?></span>
                                            <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['adresse_event']) ?></span>
                                        </div>
                                        <a href="/src/?page=event_details&id=<?= $event['id_event'] ?>" class="btn btn-outline">Voir les détails</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Onglet Paramètres du compte -->
            <div class="tab-content" id="account-tab" style="display: none;">
                <div class="account-section">
                    <div class="account-header">
                        <h2><i class="fas fa-user-edit"></i> Paramètres du compte</h2>
                    </div>
                    <div class="account-content">
                        <form id="profileForm" onsubmit="updateProfile(event)">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Prénom</label>
                                    <input type="text" name="prenom" value="<?= htmlspecialchars($userData['prenom']) ?>" placeholder="Votre prénom" required>
                                </div>
                                <div class="form-group">
                                    <label>Nom</label>
                                    <input type="text" name="nom" value="<?= htmlspecialchars($userData['nom']) ?>" placeholder="Votre nom" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>" placeholder="votre@email.com" required>
                                </div>
                                <div class="form-group">
                                    <label>Date de naissance</label>
                                    <input type="date" name="date_naissance" value="<?= htmlspecialchars($userData['date_naissance'] ?? date('Y-m-d')) ?>" required>
                                </div>
                            </div>
                            <input type="hidden" name="action" value="update_profile">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer les modifications</button>
                        </form>

                        <div class="section-divider"></div>

                        <div class="security-section">
                            <h3><i class="fas fa-lock"></i> Sécurité</h3>
                            <form id="passwordForm" onsubmit="updatePassword(event)">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Mot de passe actuel</label>
                                        <div class="password-input-container">
                                            <input type="password" name="current_password" placeholder="Votre mot de passe actuel" required>
                                            <i class="fas fa-eye toggle-password" onclick="togglePasswordVisibility(this)"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Nouveau mot de passe</label>
                                        <div class="password-input-container">
                                            <input type="password" name="new_password" placeholder="Votre nouveau mot de passe" required>
                                            <i class="fas fa-eye toggle-password" onclick="togglePasswordVisibility(this)"></i>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Confirmer le nouveau mot de passe</label>
                                        <div class="password-input-container">
                                            <input type="password" name="confirm_password" placeholder="Confirmez votre nouveau mot de passe" required>
                                            <i class="fas fa-eye toggle-password" onclick="togglePasswordVisibility(this)"></i>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="action" value="update_password">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-key"></i> Changer le mot de passe</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    function showTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.style.display = 'none';
        });
        
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active');
        });
        
        document.getElementById(tabName + '-tab').style.display = 'block';
        document.querySelector(`.tab-button[onclick="showTab('${tabName}')"]`).classList.add('active');
    }

    function updateProfile(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);

        fetch('/src/?page=client_profil', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('updatePopup').style.display = 'flex';
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la mise à jour du profil');
        });
    }

    function updatePassword(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);

        fetch('/src/?page=client_profil', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('updatePopup').style.display = 'flex';
                form.reset();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la mise à jour du mot de passe');
        });
    }

    function closePopup(popupId) {
        document.getElementById(popupId).style.display = 'none';
    }

    function togglePasswordVisibility(icon) {
        const input = icon.previousElementSibling;
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }

    // Afficher l'onglet événements par défaut
    showTab('events');
    </script>

    <style>
    .password-input-container {
        position: relative;
        display: flex;
        align-items: center;
    }

    .password-input-container input {
        flex: 1;
        padding-right: 35px; /* Espace pour l'icône */
    }

    .toggle-password {
        position: absolute;
        right: 10px;
        cursor: pointer;
        color: #666;
        transition: color 0.3s ease;
    }

    .toggle-password:hover {
        color: #42875D;
    }
    </style>

    <?php require_once 'common/footer.php'; ?>
</body>
</html>
