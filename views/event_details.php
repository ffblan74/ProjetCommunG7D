<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once 'common/header.php'; ?>
    <link rel="stylesheet" href="/src/assets/CSS/event_details.css">
</head>
<body>
    <!-- Popup de confirmation d'inscription -->
    <div id="confirmationPopup" class="popup-overlay" style="display: none;">
        <div class="popup">
            <div class="popup-content">
                <i class="fas fa-check-circle success-icon"></i>
                <h3>Inscription confirmée !</h3>
                <p>Votre inscription a été enregistrée avec succès.</p>
                <button onclick="closePopup('confirmationPopup')" class="btn btn-primary">Fermer</button>
            </div>
        </div>
    </div>

    <!-- Popup de désinscription -->
    <div id="unregisterPopup" class="popup-overlay" style="display: none;">
        <div class="popup">
            <div class="popup-content">
                <i class="fas fa-exclamation-circle warning-icon"></i>
                <h3>Confirmer la désinscription</h3>
                <p>Êtes-vous sûr de vouloir vous désinscrire de cet événement ?</p>
                <div class="popup-buttons">
                    <button onclick="confirmUnregister()" class="btn btn-danger">Confirmer</button>
                    <button onclick="closePopup('unregisterPopup')" class="btn btn-outline-secondary">Annuler</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Popup de succès de désinscription -->
    <div id="unregisterSuccessPopup" class="popup-overlay" style="display: none;">
        <div class="popup">
            <div class="popup-content">
                <i class="fas fa-check-circle success-icon"></i>
                <h3>Désinscription effectuée</h3>
                <p>Vous avez été désinscrit de l'événement avec succès.</p>
                <button onclick="closePopup('unregisterSuccessPopup')" class="btn btn-primary">Fermer</button>
            </div>
        </div>
    </div>

    <div class="event-container">
        <div class="event-header">
            <div class="event-cover">
                <?php if (isset($event['image_path']) && !empty($event['image_path'])): ?>
                    <img src="<?= htmlspecialchars($event['image_path']) ?>" alt="<?= htmlspecialchars($event['titre']) ?>">
                <?php else: ?>
                    <img src="/src/assets/images/default-event.jpg" alt="Image par défaut">
                <?php endif; ?>
            </div>
            <div class="event-title-section">
                <h1><?= htmlspecialchars($event['titre']) ?></h1>
                <div class="event-status">
                    <span class="badge <?= $event['statut'] === 'ouvert' ? 'badge-success' : ($event['statut'] === 'sous_reserve' ? 'badge-warning' : 'badge-secondary') ?>">
                        <?= $event['statut'] === 'ouvert' ? 'Ouvert aux inscriptions' : ($event['statut'] === 'sous_reserve' ? 'Sur réserve' : 'Fermé') ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="event-content">
            <div class="event-main-info">
                <div class="info-card">
                    <div class="event-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <div>
                                <strong>Date</strong>
                                <span><?= date('d/m/Y', strtotime($event['date_event'])) ?></span>
                            </div>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <strong>Heure</strong>
                                <span><?= date('H:i', strtotime($event['date_event'])) ?></span>
                            </div>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <strong>Lieu</strong>
                                <span><?= htmlspecialchars($event['lieu']) ?></span>
                            </div>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-users"></i>
                            <div>
                                <strong>Participants</strong>
                                <span><?= $event['nombre_inscrits'] ?> / <?= $event['places_disponibles'] ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="event-description-card">
                    <h2>À propos de l'événement</h2>
                    <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                </div>

                <?php if (isset($event['exigences']) && !empty($event['exigences'])): ?>
                <div class="event-requirements-card">
                    <h2>Exigences</h2>
                    <ul>
                        <?php foreach (json_decode($event['exigences'], true) as $exigence): ?>
                            <li><i class="fas fa-check"></i> <?= htmlspecialchars($exigence) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>

            <div class="event-sidebar">
                <div class="registration-card">
                    <div class="spots-info">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" 
                                style="width: <?= ($event['nombre_inscrits'] / $event['places_disponibles']) * 100 ?>%">
                            </div>
                        </div>
                        <p>
                            <strong><?= $event['places_disponibles'] - $event['nombre_inscrits'] ?></strong> places restantes
                        </p>
                    </div>

                    <?php if ($isLoggedIn): ?>
                        <div class="registration-section">
                            <?php if ($isAdmin): ?>
                                <div class="organizer-badge admin">
                                    <i class="fas fa-crown"></i>
                                    <span>Vous êtes administrateur</span>
                                </div>
                            <?php elseif ($isOrganizer && $_SESSION['user_id'] == $event['id_organisateur']): ?>
                                <div class="organizer-badge">
                                    <i class="fas fa-star"></i>
                                    <span>Vous êtes l'organisateur de cet événement</span>
                                </div>
                            <?php elseif ($_SESSION['role'] === 'participant'): ?>
                                <?php if ($isRegistered): ?>
                                    <div class="registration-status">
                                        <?php if ($registrationStatus === 'approuvé'): ?>
                                            <div class="status-badge success">
                                                <i class="fas fa-check-circle"></i>
                                                <span>Vous êtes inscrit</span>
                                            </div>
                                        <?php elseif ($registrationStatus === 'en_attente'): ?>
                                            <div class="status-badge warning">
                                                <i class="fas fa-clock"></i>
                                                <span>En attente d'approbation</span>
                                            </div>
                                        <?php endif; ?>
                                        <button onclick="showUnregisterPopup()" class="btn btn-danger btn-block mt-3">Se désinscrire</button>
                                        <form id="unregisterForm" action="/src/?page=event_registration&action=cancel" method="POST" style="display: none;">
                                            <input type="hidden" name="event_id" value="<?= $event['id_event'] ?>">
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <?php if ($event['statut'] === 'ouvert'): ?>
                                        <?php if ($event['places_disponibles'] - $event['nombre_inscrits'] > 0): ?>
                                            <form id="registrationForm" action="/src/?page=event_registration&action=register" method="POST">
                                                <input type="hidden" name="event_id" value="<?= $event['id_event'] ?>">
                                                <button type="submit" class="btn btn-primary btn-lg btn-block">S'inscrire à l'événement</button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn btn-secondary btn-lg btn-block" disabled>Événement complet</button>
                                        <?php endif; ?>
                                    <?php elseif ($event['statut'] === 'sous_reserve'): ?>
                                        <form id="registrationForm" action="/src/?page=event_registration&action=register" method="POST">
                                            <input type="hidden" name="event_id" value="<?= $event['id_event'] ?>">
                                            <button type="submit" class="btn btn-warning btn-lg btn-block">Demander à participer</button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-lg btn-block" disabled>Inscriptions fermées</button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <a href="/src/?page=login" class="btn btn-outline-primary btn-lg btn-block">
                            Connectez-vous pour vous inscrire
                        </a>
                    <?php endif; ?>

                    <div class="organizer-info">
                        <h3>Organisateur</h3>
                        <div class="organizer-profile">
                            <div class="organizer-avatar">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div class="organizer-details">
                                <strong><?= htmlspecialchars($event['prenom']) ?> <?= htmlspecialchars($event['nom_organisateur']) ?></strong>
                                <span>Organisateur</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion de l'inscription
        const registrationForm = document.getElementById('registrationForm');
        if (registrationForm) {
            registrationForm.addEventListener('submit', function(e) {
                e.preventDefault();
                fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('confirmationPopup').style.display = 'flex';
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue l\'inscription');
                });
            });
        }
    });

    function showUnregisterPopup() {
        document.getElementById('unregisterPopup').style.display = 'flex';
    }

    function confirmUnregister() {
        const form = document.getElementById('unregisterForm');
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closePopup('unregisterPopup');
                document.getElementById('unregisterSuccessPopup').style.display = 'flex';
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                alert(data.message || 'Une erreur est survenue lors de la désinscription');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la désinscription');
        });
    }

    function closePopup(popupId) {
        document.getElementById(popupId).style.display = 'none';
    }
    </script>

    <?php require_once 'common/footer.php'; ?>
</body>
</html>
