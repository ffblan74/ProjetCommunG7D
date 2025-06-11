<?php
// Vue pour l'édition d'un événement
?>
<?php require_once 'views/dashboard/organizer_layout.php'; ?>

<div class="event-form">
    <a href="/src/?page=dashboard&view=events" class="back-link">
        <i class="fas fa-arrow-left"></i> Retour aux événements
    </a>
    
    <h1>Modifier l'événement</h1>
    
    <form id="editEventForm" method="POST">
        <input type="hidden" name="event_id" value="<?= htmlspecialchars($event['id_event']) ?>">
        
        <div class="form-row">
            <div class="form-group">
                <label for="nom_event">Nom de l'événement</label>
                <input type="text" id="nom_event" name="nom_event" value="<?= htmlspecialchars($event['nom_event']) ?>" required>
            </div>

            <div class="form-group">
                <label for="date_planifiee">Date et heure</label>
                <input type="datetime-local" id="date_planifiee" name="date_planifiee" 
                       value="<?= date('Y-m-d\TH:i', strtotime($event['date_planifiee'])) ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" required><?= htmlspecialchars($event['description']) ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="duree">Durée (en minutes)</label>
                <input type="number" id="duree" name="duree" value="<?= htmlspecialchars($event['duree']) ?>" required min="1">
            </div>

            <div class="form-group">
                <label for="nb_participants">Nombre maximum de participants</label>
                <input type="number" id="nb_participants" name="nb_participants" value="<?= htmlspecialchars($event['capacite_max']) ?>" required min="1">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="adresse_event">Lieu de l'événement</label>
                <input type="text" id="adresse_event" name="adresse_event" value="<?= htmlspecialchars($event['adresse_event']) ?>" required>
            </div>

            <div class="form-group">
                <label for="statut">Statut de l'événement</label>
                <select id="statut" name="statut" required>
                    <option value="ouvert" <?= $event['statut'] === 'ouvert' ? 'selected' : '' ?>>Ouvert</option>
                    <option value="sous_reserve" <?= $event['statut'] === 'sous_reserve' ? 'selected' : '' ?>>Sous réserve</option>
                    <option value="ferme" <?= $event['statut'] === 'ferme' ? 'selected' : '' ?>>Fermé</option>
                </select>
            </div>
        </div>

        <div class="button-group">
            <button type="submit">Enregistrer les modifications</button>
            <button type="button" onclick="window.location.href='/src/?page=dashboard&view=events'">Annuler</button>
        </div>
    </form>
</div>

<script>
document.getElementById('editEventForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);

    fetch('/src/?page=dashboard&action=update_event', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Événement mis à jour avec succès');
            window.location.href = '/src/?page=dashboard&view=events';
        } else {
            alert(data.message || 'Erreur lors de la mise à jour de l\'événement');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue lors de la mise à jour de l\'événement');
    });
});
</script> 