<!-- Mes Événements -->
<section class="dashboard-section">
    <h2>Mes Événements</h2>
    <div class="events-grid">
        <?php foreach ($events as $event): ?>
            <div class="event-card">
                <div class="event-header">
                    <h3><?= htmlspecialchars($event['nom_event']) ?></h3>
                    <div class="status-container">
                        <span class="status <?= $event['statut'] ?>"><?= ucfirst($event['statut']) ?></span>
                        <?php if ($event['etat'] === 'En attente'): ?>
                            <span class="status pending">En attente d'approbation</span>
                        <?php endif; ?>
                    </div>
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
                    <select class="status-select" onchange="updateEventStatus(<?= $event['id_event'] ?>, this.value)">
                        <option value="ouvert" <?= $event['statut'] === 'ouvert' ? 'selected' : '' ?>>Ouvert</option>
                        <option value="sous_reserve" <?= $event['statut'] === 'sous_reserve' ? 'selected' : '' ?>>Sous réserve</option>
                        <option value="ferme" <?= $event['statut'] === 'ferme' ? 'selected' : '' ?>>Fermé</option>
                    </select>
                    <button onclick="window.location.href='/src/?page=dashboard&view=edit_event&id=<?= $event['id_event'] ?>'" class="btn-edit">
                        <i class="fas fa-edit"></i> Modifier
                    </button>
                    <button onclick="window.location.href='/src/?page=event_details&id=<?= $event['id_event'] ?>'" class="btn-view">
                        <i class="fas fa-eye"></i> Voir détails
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<style>
.status-container {
    display: flex;
    gap: 10px;
    align-items: center;
}

.status.pending {
    background-color: #ffc107;
    color: #000;
}
</style>

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
            location.reload();
        } else {
            alert('Erreur lors de la mise à jour du statut');
        }
    });
}
</script> 