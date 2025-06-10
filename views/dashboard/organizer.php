<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Organisateur</title>
    <link rel="stylesheet" href="/src/assets/CSS/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php require_once 'views/common/header.php'; ?>

<div class="dashboard-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="profile-section">
            <img src="/src/assets/images/<?= htmlspecialchars($userData['photo_profil'] ?? 'default_profile.png') ?>" alt="Photo de profil">
            <h3><?= htmlspecialchars($userData['prenom'] . ' ' . $userData['nom']) ?></h3>
            <span class="role">Organisateur</span>
        </div>
        <nav>
            <ul>
                <li class="active"><a href="#overview"><i class="fas fa-home"></i> Vue d'ensemble</a></li>
                <li><a href="#events"><i class="fas fa-calendar-alt"></i> Mes Événements</a></li>
                <li><a href="#requests"><i class="fas fa-user-clock"></i> Demandes en attente</a></li>
                <li><a href="#profile"><i class="fas fa-user-edit"></i> Modifier le profil</a></li>
                <li><a href="/src/?page=create_event"><i class="fas fa-plus-circle"></i> Créer un événement</a></li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Vue d'ensemble -->
        <section id="overview" class="dashboard-section">
            <h2>Vue d'ensemble</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-calendar"></i>
                    <div class="stat-info">
                        <h3><?= $stats['total_events'] ?></h3>
                        <p>Événements créés</p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <div class="stat-info">
                        <h3><?= $stats['total_participants'] ?></h3>
                        <p>Participants total</p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-clock"></i>
                    <div class="stat-info">
                        <h3><?= $stats['pending_requests'] ?></h3>
                        <p>Demandes en attente</p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-calendar-check"></i>
                    <div class="stat-info">
                        <h3><?= $stats['upcoming_events'] ?></h3>
                        <p>Événements à venir</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mes Événements -->
        <section id="events" class="dashboard-section">
            <h2>Mes Événements</h2>
            <div class="events-grid">
                <?php foreach ($events as $event): ?>
                    <div class="event-card">
                        <div class="event-header">
                            <h3><?= htmlspecialchars($event['nom_event']) ?></h3>
                            <span class="status <?= $event['statut'] ?>"><?= ucfirst($event['statut']) ?></span>
                        </div>
                        <div class="event-info">
                            <p><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($event['date_planifiee'])) ?></p>
                            <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['adresse_event']) ?></p>
                            <p><i class="fas fa-users"></i> <?= $event['participants_approuves'] ?> / <?= $event['capacite_max'] ?> participants</p>
                            <?php if ($event['participants_en_attente'] > 0): ?>
                                <p class="pending"><i class="fas fa-user-clock"></i> <?= $event['participants_en_attente'] ?> en attente</p>
                            <?php endif; ?>
                        </div>
                        <div class="event-actions">
                            <select class="status-select" onchange="updateEventStatus(<?= $event['id_evenement'] ?>, this.value)">
                                <option value="ouvert" <?= $event['statut'] === 'ouvert' ? 'selected' : '' ?>>Ouvert</option>
                                <option value="sous_reserve" <?= $event['statut'] === 'sous_reserve' ? 'selected' : '' ?>>Sous réserve</option>
                                <option value="ferme" <?= $event['statut'] === 'ferme' ? 'selected' : '' ?>>Fermé</option>
                            </select>
                            <button onclick="window.location.href='/src/?page=event_details&id=<?= $event['id_evenement'] ?>'">
                                <i class="fas fa-eye"></i> Voir détails
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Demandes en attente -->
        <section id="requests" class="dashboard-section">
            <h2>Demandes en attente</h2>
            <div class="requests-list">
                <?php foreach ($pendingRequests as $request): ?>
                    <div class="request-card">
                        <div class="request-info">
                            <img src="/src/assets/images/<?= htmlspecialchars($request['photo_profil'] ?? 'default_profile.png') ?>" alt="Photo de profil">
                            <div>
                                <h4><?= htmlspecialchars($request['prenom'] . ' ' . $request['nom_participant']) ?></h4>
                                <p>Pour : <?= htmlspecialchars($request['nom_event']) ?></p>
                                <p class="date">Demande envoyée le <?= date('d/m/Y', strtotime($request['date_inscription'])) ?></p>
                            </div>
                        </div>
                        <div class="request-actions">
                            <button class="approve" onclick="handleRequest(<?= $request['id_evenement'] ?>, <?= $request['id_participant'] ?>, 'approve')">
                                <i class="fas fa-check"></i> Approuver
                            </button>
                            <button class="reject" onclick="handleRequest(<?= $request['id_evenement'] ?>, <?= $request['id_participant'] ?>, 'reject')">
                                <i class="fas fa-times"></i> Refuser
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($pendingRequests)): ?>
                    <p class="no-requests">Aucune demande en attente</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Profil Organisateur -->
        <section id="profile" class="dashboard-section">
            <h2>Mon Profil</h2>
            <div class="profile-content">
                <div class="profile-info-card">
                    <div class="profile-header">
                        <img src="/src/assets/images/profile/<?= htmlspecialchars($userData['photo_profil'] ?? 'default.png') ?>" alt="Photo de profil">
                        <div class="profile-title">
                            <h3><?= htmlspecialchars($userData['prenom'] . ' ' . $userData['nom']) ?></h3>
                            <span class="role">Organisateur</span>
                        </div>
                    </div>
                    <form id="profileForm" class="profile-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Prénom</label>
                                <input type="text" name="prenom" value="<?= htmlspecialchars($userData['prenom']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Nom</label>
                                <input type="text" name="nom" value="<?= htmlspecialchars($userData['nom']) ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Téléphone</label>
                                <input type="tel" name="telephone" value="<?= htmlspecialchars($userData['telephone'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Photo de profil</label>
                            <input type="file" name="photo_profil" accept="image/*">
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-save">
                                <i class="fas fa-save"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>

                <div class="profile-info-card">
                    <h3>Sécurité</h3>
                    <form id="passwordForm" class="profile-form">
                        <div class="form-group">
                            <label>Mot de passe actuel</label>
                            <input type="password" name="current_password" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nouveau mot de passe</label>
                                <input type="password" name="new_password" required>
                            </div>
                            <div class="form-group">
                                <label>Confirmer le mot de passe</label>
                                <input type="password" name="confirm_password" required>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-save">
                                <i class="fas fa-key"></i> Changer le mot de passe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
function updateEventStatus(eventId, status) {
    fetch('/src/?page=dashboard&action=update_event_status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `event_id=${eventId}&status=${status}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualiser la page ou mettre à jour l'UI
            location.reload();
        } else {
            alert('Erreur lors de la mise à jour du statut');
        }
    });
}

function handleRequest(eventId, participantId, action) {
    fetch('/src/?page=dashboard&action=manage_participants', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `event_id=${eventId}&participant_id=${participantId}&action=${action}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualiser la page ou mettre à jour l'UI
            location.reload();
        } else {
            alert('Erreur lors du traitement de la demande');
        }
    });
}

// Navigation dans la sidebar
document.querySelectorAll('.sidebar nav a').forEach(link => {
    link.addEventListener('click', function(e) {
        document.querySelectorAll('.sidebar nav li').forEach(li => li.classList.remove('active'));
        this.parentElement.classList.add('active');
    });
});

// Gestion du formulaire de profil
document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'update_profile');

    fetch('/src/?page=dashboard&action=update_profile', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Profil mis à jour avec succès');
            location.reload();
        } else {
            alert('Erreur lors de la mise à jour du profil');
        }
    });
});

// Gestion du formulaire de mot de passe
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'update_password');

    fetch('/src/?page=dashboard&action=update_password', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Mot de passe mis à jour avec succès');
            this.reset();
        } else {
            alert('Erreur lors de la mise à jour du mot de passe');
        }
    });
});
</script>

</body>
</html> 