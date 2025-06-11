<!-- Demandes en attente -->
<section class="dashboard-section">
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
                    <button class="approve" onclick="handleRequest(<?= $request['id_event'] ?>, <?= $request['id_participant'] ?>, 'approve')">
                        <i class="fas fa-check"></i> Approuver
                    </button>
                    <button class="reject" onclick="handleRequest(<?= $request['id_event'] ?>, <?= $request['id_participant'] ?>, 'reject')">
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

<script>
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
            location.reload();
        } else {
            alert('Erreur lors du traitement de la demande');
        }
    });
}
</script> 