<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Light Control</title>
    <link rel="stylesheet" href="assets/CSS/admin.css">
    <link rel="stylesheet" href="../../assets/CSS/style.css">
</head>
<body>
    <?php 
    // Récupérer la section active
    $section = $_GET['section'] ?? 'dashboard';
    ?>

    <?php if (isset($error)): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php include(dirname(__FILE__) . '/../common/header.php'); ?>

    <div class="admin-wrapper">
        <!-- Sidebar gauche -->
        <aside class="admin-sidebar">
            <div class="admin-profile">
                <div class="profile-icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="profile-info">
                    <h3>Administrateur</h3>
                    <p>Super Admin</p>
                </div>
            </div>

            <nav class="admin-nav">
                <a href="?page=admin&section=dashboard" class="nav-item <?php echo $section === 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    Tableau de bord
                </a>
                <a href="?page=admin&section=events" class="nav-item <?php echo $section === 'events' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar"></i>
                    Événements
                </a>
                <a href="?page=admin&section=users" class="nav-item <?php echo $section === 'users' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    Utilisateurs
                </a>
            </nav>
        </aside>

        <!-- Contenu principal -->
        <main class="admin-content">
            <div class="content-header">
                <h1>
                    <?php
                    switch($section) {
                        case 'events':
                            echo 'Événements';
                            break;
                        case 'users':
                            echo 'Utilisateurs';
                            break;
                        default:
                            echo 'Tableau de bord';
                    }
                    ?>
                </h1>
            </div>

            <div class="content-body">
                <!-- Section Dashboard -->
                <?php if ($section === 'dashboard'): ?>
                    <div class="section">
                        <div class="events-section">
                            <h2>Événements en attente</h2>
                            <?php if (isset($pendingEvents) && !empty($pendingEvents)): ?>
                                <div class="events-list">
                                    <?php foreach ($pendingEvents as $event): ?>
                                        <div class="event-card">
                                            <div class="event-info">
                                                <h3><?= htmlspecialchars($event['nom_event']) ?></h3>
                                                <p>Date: <?= date('d/m/Y H:i', strtotime($event['date_planifiee'])) ?></p>
                                                <p>Organisateur: <?= htmlspecialchars($event['prenom'] . ' ' . $event['nom_organisateur']) ?></p>
                                                <p>Participants max: <?= htmlspecialchars($event['nb_participants']) ?></p>
                                                <p>Adresse: <?= htmlspecialchars($event['adresse_event']) ?></p>
                                                <p class="event-description">Description: <?= htmlspecialchars($event['description']) ?></p>
                                            </div>
                                            <div class="event-actions">
                                                <a href="?page=admin&action=approve&id=<?= $event['id_event'] ?>" 
                                                   class="btn-approve"
                                                   onclick="return confirm('Voulez-vous vraiment approuver cet événement ?')">
                                                    Approuver
                                                </a>
                                                <a href="?page=admin&action=reject&id=<?= $event['id_event'] ?>" 
                                                   class="btn-reject"
                                                   onclick="return confirm('Voulez-vous vraiment refuser cet événement ?')">
                                                    Refuser
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="no-events">Aucun événement en attente</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Section Événements -->
                <?php if ($section === 'events'): ?>
                    <div class="section">
                        <div class="filters-bar">
                            <div class="search-bar">
                                <input type="text" id="searchEvents" placeholder="Rechercher un événement (ID, nom, etc...)" onkeyup="searchTable('searchEvents', 'eventsTable')">
                            </div>
                            <div class="filter-options">
                                <select id="filterEtat" onchange="filterEvents()">
                                    <option value="">Tous les états</option>
                                    <option value="Planifié">Planifié</option>
                                    <option value="En cours">En cours</option>
                                    <option value="Terminé">Terminé</option>
                                    <option value="Annulé">Annulé</option>
                                </select>
                                <input type="date" id="filterDate" onchange="filterEvents()" placeholder="Filtrer par date">
                            </div>
                        </div>
                        <div class="events-list">
                            <table class="admin-table" id="eventsTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nom</th>
                                        <th>Participants Max</th>
                                        <th>Inscrits</th>
                                        <th>Adresse</th>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Durée (h)</th>
                                        <th>État</th>
                                        <th>Organisateur</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($events) && !empty($events)): ?>
                                        <?php foreach ($events as $event): ?>
                                            <tr id="event-row-<?= $event['id_event'] ?>">
                                                <td><?= htmlspecialchars($event['id_event']) ?></td>
                                                <td>
                                                    <span class="display-value"><?= htmlspecialchars($event['nom_event']) ?></span>
                                                    <input type="text" class="edit-input" value="<?= htmlspecialchars($event['nom_event']) ?>" style="display: none;">
                                                </td>
                                                <td>
                                                    <span class="display-value"><?= htmlspecialchars($event['nb_participants']) ?></span>
                                                    <input type="number" class="edit-input" value="<?= htmlspecialchars($event['nb_participants']) ?>" style="display: none;">
                                                </td>
                                                <td><?= htmlspecialchars($event['nombre_inscrits']) ?></td>
                                                <td>
                                                    <span class="display-value"><?= htmlspecialchars($event['adresse_event']) ?></span>
                                                    <input type="text" class="edit-input" value="<?= htmlspecialchars($event['adresse_event']) ?>" style="display: none;">
                                                </td>
                                                <td>
                                                    <span class="display-value"><?= date('d/m/Y H:i', strtotime($event['date_planifiee'])) ?></span>
                                                    <input type="datetime-local" class="edit-input" value="<?= date('Y-m-d\TH:i', strtotime($event['date_planifiee'])) ?>" style="display: none;">
                                                </td>
                                                <td>
                                                    <span class="display-value"><?= htmlspecialchars($event['description']) ?></span>
                                                    <textarea class="edit-input" style="display: none;"><?= htmlspecialchars($event['description']) ?></textarea>
                                                </td>
                                                <td>
                                                    <span class="display-value"><?= htmlspecialchars($event['duree']) ?></span>
                                                    <input type="number" class="edit-input" value="<?= htmlspecialchars($event['duree']) ?>" style="display: none;">
                                                </td>
                                                <td>
                                                    <span class="display-value" data-state="<?= htmlspecialchars($event['etat']) ?>"><?= htmlspecialchars($event['etat']) ?></span>
                                                    <select class="edit-input" style="display: none;">
                                                        <option value="Planifié" <?= $event['etat'] === 'Planifié' ? 'selected' : '' ?>>Planifié</option>
                                                        <option value="En cours" <?= $event['etat'] === 'En cours' ? 'selected' : '' ?>>En cours</option>
                                                        <option value="Terminé" <?= $event['etat'] === 'Terminé' ? 'selected' : '' ?>>Terminé</option>
                                                        <option value="Annulé" <?= $event['etat'] === 'Annulé' ? 'selected' : '' ?>>Annulé</option>
                                                    </select>
                                                </td>
                                                <td><?= htmlspecialchars($event['prenom'] . ' ' . $event['nom_organisateur']) ?></td>
                                                <td>
                                                    <button class="btn-edit" onclick="openEditDialog(<?= $event['id_event'] ?>)">Modifier</button>
                                                    <button class="btn-save" onclick="saveEventChanges(<?= $event['id_event'] ?>)" style="display: none;">Enregistrer</button>
                                                    <a href="?page=admin&section=events&action=delete&id=<?= $event['id_event'] ?>"
                                                       onclick="return confirm('Voulez-vous vraiment supprimer cet événement ?')"
                                                       class="btn-delete">Supprimer</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="11">Aucun événement trouvé</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Dialog d'édition (HTML natif) -->
                    <dialog id="editDialog" class="edit-dialog">
                        <form method="POST" action="?page=admin&section=events&action=update" class="edit-form">
                            <div class="dialog-header">
                                <h2>Modifier l'événement</h2>
                                <button type="button" onclick="document.getElementById('editDialog').close()" class="close-button">&times;</button>
                            </div>
                            
                            <div class="dialog-body">
                                <input type="hidden" name="id_event" id="edit_id_event">
                                
                                <div class="form-group">
                                    <label for="edit_nom_event">Nom de l'événement</label>
                                    <input type="text" id="edit_nom_event" name="nom_event" required class="form-input">
                                </div>

                                <div class="form-group">
                                    <label for="edit_nb_participants">Nombre maximum de participants</label>
                                    <input type="number" id="edit_nb_participants" name="nb_participants" required class="form-input">
                                </div>

                                <div class="form-group">
                                    <label for="edit_adresse">Adresse</label>
                                    <input type="text" id="edit_adresse" name="adresse_event" required class="form-input">
                                </div>

                                <div class="form-group">
                                    <label for="edit_date">Date et heure</label>
                                    <input type="datetime-local" id="edit_date" name="date_planifiee" required class="form-input">
                                </div>

                                <div class="form-group">
                                    <label for="edit_description">Description</label>
                                    <textarea id="edit_description" name="description" rows="4" class="form-input"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="edit_duree">Durée (heures)</label>
                                    <input type="number" id="edit_duree" name="duree" required class="form-input">
                                </div>

                                <div class="form-group">
                                    <label for="edit_etat">État</label>
                                    <select id="edit_etat" name="etat" required class="form-input">
                                        <option value="Planifié">Planifié</option>
                                        <option value="En cours">En cours</option>
                                        <option value="Terminé">Terminé</option>
                                        <option value="Annulé">Annulé</option>
                                    </select>
                                </div>
                            </div>

                            <div class="dialog-footer">
                                <button type="button" onclick="document.getElementById('editDialog').close()" class="btn-cancel">Annuler</button>
                                <button type="submit" class="btn-save">Enregistrer</button>
                            </div>
                        </form>
                    </dialog>

                    <script>
                    // Remplacer la fonction openEditDialog par cette version corrigée
                    function openEditDialog(id) {
                        const row = document.getElementById(`event-row-${id}`);
                        const dialog = document.getElementById('editDialog');
                        
                        // Remplir le formulaire
                        document.getElementById('edit_id_event').value = id;
                        document.getElementById('edit_nom_event').value = row.cells[1].querySelector('.display-value').textContent.trim();
                        document.getElementById('edit_nb_participants').value = row.cells[2].querySelector('.display-value').textContent.trim();
                        document.getElementById('edit_adresse').value = row.cells[4].querySelector('.display-value').textContent.trim();
                        document.getElementById('edit_description').value = row.cells[6].querySelector('.display-value').textContent.trim();
                        document.getElementById('edit_duree').value = row.cells[7].querySelector('.display-value').textContent.trim();
                        
                        // Correction pour l'état
                        const etatValue = row.cells[8].querySelector('.display-value').textContent.trim();
                        const etatSelect = document.getElementById('edit_etat');
                        
                        // S'assurer que l'option existe et la sélectionner
                        for(let option of etatSelect.options) {
                            if(option.value === etatValue) {
                                option.selected = true;
                                break;
                            }
                        }

                        // Convertir la date
                        const dateFr = row.cells[5].querySelector('.display-value').textContent;
                        const [date, time] = dateFr.split(' ');
                        const [day, month, year] = date.split('/');
                        document.getElementById('edit_date').value = `${year}-${month}-${day}T${time}`;

                        dialog.showModal();
                    }

                    // Ajouter une fonction pour gérer la soumission du formulaire
                    document.getElementById('editDialog').querySelector('form').addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        const formData = new FormData(this);
                        const id = formData.get('id_event');
                        
                        fetch(`?page=admin&section=events&action=update&id=${id}`, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.text())
                        .then(() => {
                            // Mettre à jour l'affichage dans le tableau
                            const row = document.getElementById(`event-row-${id}`);
                            
                            // Mettre à jour les valeurs affichées
                            row.cells[1].querySelector('.display-value').textContent = formData.get('nom_event');
                            row.cells[2].querySelector('.display-value').textContent = formData.get('nb_participants');
                            row.cells[4].querySelector('.display-value').textContent = formData.get('adresse_event');
                            
                            // Formater la date pour l'affichage
                            const date = new Date(formData.get('date_planifiee'));
                            const options = { 
                                day: '2-digit', 
                                month: '2-digit', 
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            };
                            row.cells[5].querySelector('.display-value').textContent = date.toLocaleString('fr-FR', options);
                            
                            row.cells[6].querySelector('.display-value').textContent = formData.get('description');
                            row.cells[7].querySelector('.display-value').textContent = formData.get('duree');
                            
                            // Mise à jour de l'état avec le data-state
                            const etatCell = row.cells[8].querySelector('.display-value');
                            etatCell.textContent = formData.get('etat');
                            etatCell.setAttribute('data-state', formData.get('etat'));
                            
                            // Fermer le dialog
                            document.getElementById('editDialog').close();
                            
                            // Rafraîchir les filtres si nécessaire
                            filterEvents();
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            alert('Une erreur est survenue lors de la mise à jour');
                        });
                    });

                    // Remplacer la fonction searchTable existante par celle-ci
                    function searchTable(inputId, tableId) {
                        const input = document.getElementById(inputId);
                        const filter = input.value.toLowerCase();
                        const table = document.getElementById(tableId);
                        const rows = table.getElementsByTagName('tr');

                        for (let i = 1; i < rows.length; i++) {
                            let found = false;
                            const cells = rows[i].getElementsByTagName('td');
                            
                            // Vérifier d'abord l'ID (première colonne)
                            const idCell = cells[0];
                            if (idCell && idCell.textContent.toLowerCase().includes(filter)) {
                                found = true;
                            }

                            // Si l'ID ne correspond pas, vérifier les autres colonnes
                            if (!found) {
                                for (let j = 1; j < cells.length - 1; j++) {
                                    const cell = cells[j];
                                    if (cell) {
                                        const displayValue = cell.querySelector('.display-value');
                                        const text = (displayValue ? displayValue.textContent : cell.textContent).toLowerCase();
                                        if (text.includes(filter)) {
                                            found = true;
                                            break;
                                        }
                                    }
                                }
                            }

                            rows[i].style.display = found ? '' : 'none';
                        }
                    }

                    // Remplacer la fonction filterEvents existante par celle-ci
                    function filterEvents() {
                        const etatFilter = document.getElementById('filterEtat').value;
                        const dateFilter = document.getElementById('filterDate').value;
                        const rows = document.getElementById('eventsTable').getElementsByTagName('tr');

                        // Convertir la date du filtre au format français pour la comparaison
                        let filterDateFr = '';
                        if (dateFilter) {
                            const dateParts = dateFilter.split('-');
                            filterDateFr = `${dateParts[2]}/${dateParts[1]}/${dateParts[0]}`;
                        }

                        for (let i = 1; i < rows.length; i++) {
                            const etatCell = rows[i].cells[8].querySelector('.display-value');
                            const dateCell = rows[i].cells[5].querySelector('.display-value');
                            
                            if (!etatCell || !dateCell) continue;

                            const etatValue = etatCell.textContent.trim();
                            const dateValue = dateCell.textContent.split(' ')[0]; // Prend juste la partie date (DD/MM/YYYY)

                            const matchEtat = !etatFilter || etatValue === etatFilter;
                            const matchDate = !filterDateFr || dateValue === filterDateFr;

                            rows[i].style.display = (matchEtat && matchDate) ? '' : 'none';
                        }
                    }

                    // Ajouter des écouteurs d'événements pour les filtres
                    document.addEventListener('DOMContentLoaded', function() {
                        const etatSelect = document.getElementById('filterEtat');
                        const dateInput = document.getElementById('filterDate');
                        
                        if (etatSelect && dateInput) {
                            etatSelect.addEventListener('change', filterEvents);
                            dateInput.addEventListener('change', filterEvents);
                        }

                        const searchInputs = {
                            'searchEvents': 'Rechercher un événement (ID, nom, etc...)',
                            'searchParticipants': 'Rechercher un participant (ID, nom, etc...)',
                            'searchOrganisateurs': 'Rechercher un organisateur (ID, nom, etc...)'
                        };

                        for (let [id, placeholder] of Object.entries(searchInputs)) {
                            const input = document.getElementById(id);
                            if (input) {
                                input.placeholder = placeholder;
                            }
                        }
                    });
                    </script>
                <?php endif; ?>

                <!-- Section Utilisateurs -->
                <?php if ($section === 'users'): ?>
                    <div class="section">
                        <!-- Participants -->
                        <div class="users-list">
                            <h2>Participants</h2>
                            <div class="search-bar">
                                <input type="text" id="searchParticipants" placeholder="Rechercher un participant (ID, nom, etc...)" onkeyup="searchTable('searchParticipants', 'participantsTable')">
                            </div>
                            <table class="admin-table" id="participantsTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nom</th>
                                        <th>Prénom</th>
                                        <th>Email</th>
                                        <th>Rôle</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($users) && !empty($users)): ?>
                                        <?php foreach ($users as $user): ?>
                                            <tr id="user-row-<?= $user['id_participant'] ?>">
                                                <td><?= htmlspecialchars($user['id_participant']) ?></td>
                                                <td>
                                                    <span class="display-value"><?= htmlspecialchars($user['nom_participant']) ?></span>
                                                    <input type="text" class="edit-input" value="<?= htmlspecialchars($user['nom_participant']) ?>" style="display: none;">
                                                </td>
                                                <td>
                                                    <span class="display-value"><?= htmlspecialchars($user['prenom']) ?></span>
                                                    <input type="text" class="edit-input" value="<?= htmlspecialchars($user['prenom']) ?>" style="display: none;">
                                                </td>
                                                <td>
                                                    <span class="display-value"><?= htmlspecialchars($user['email']) ?></span>
                                                    <input type="email" class="edit-input" value="<?= htmlspecialchars($user['email']) ?>" style="display: none;">
                                                </td>
                                                <td>
                                                    <span class="display-value role-badge <?= htmlspecialchars($user['role']) ?>"><?= htmlspecialchars($user['role'] ?? 'utilisateur') ?></span>
                                                    <select class="edit-input" style="display: none;">
                                                        <option value="utilisateur" <?= ($user['role'] ?? 'utilisateur') === 'utilisateur' ? 'selected' : '' ?>>Utilisateur</option>
                                                        <option value="moderateur" <?= ($user['role'] ?? '') === 'moderateur' ? 'selected' : '' ?>>Modérateur</option>
                                                        <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button class="btn-edit" onclick="toggleEdit(this, <?= $user['id_participant'] ?>, 'user')">Modifier</button>
                                                    <button class="btn-save" onclick="saveChanges(<?= $user['id_participant'] ?>, 'user')" style="display: none;">Enregistrer</button>
                                                    <a href="?page=admin&section=users&action=delete&id=<?= $user['id_participant'] ?>"
                                                       onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?')"
                                                       class="btn-delete">Supprimer</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5">Aucun participant trouvé</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Organisateurs -->
                        <div class="users-list">
                            <h2>Organisateurs</h2>
                            <div class="search-bar">
                                <input type="text" id="searchOrganisateurs" placeholder="Rechercher un organisateur (ID, nom, etc...)" onkeyup="searchTable('searchOrganisateurs', 'organisateursTable')">
                            </div>
                            <table class="admin-table" id="organisateursTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nom</th>
                                        <th>Prénom</th>
                                        <th>Email</th>
                                        <th>Rôle</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($organisateurs) && !empty($organisateurs)): ?>
                                        <?php foreach ($organisateurs as $org): ?>
                                            <tr id="org-row-<?= $org['id_organisateur'] ?>">
                                                <td><?= htmlspecialchars($org['id_organisateur']) ?></td>
                                                <td>
                                                    <span class="display-value"><?= htmlspecialchars($org['nom_organisateur']) ?></span>
                                                    <input type="text" class="edit-input" value="<?= htmlspecialchars($org['nom_organisateur']) ?>" style="display: none;">
                                                </td>
                                                <td>
                                                    <span class="display-value"><?= htmlspecialchars($org['prenom']) ?></span>
                                                    <input type="text" class="edit-input" value="<?= htmlspecialchars($org['prenom']) ?>" style="display: none;">
                                                </td>
                                                <td>
                                                    <span class="display-value"><?= htmlspecialchars($org['email']) ?></span>
                                                    <input type="email" class="edit-input" value="<?= htmlspecialchars($org['email']) ?>" style="display: none;">
                                                </td>
                                                <td>
                                                    <span class="display-value role-badge <?= htmlspecialchars($org['role']) ?>"><?= htmlspecialchars($org['role'] ?? 'organisateur') ?></span>
                                                    <select class="edit-input" style="display: none;">
                                                        <option value="organisateur" <?= ($org['role'] ?? 'organisateur') === 'organisateur' ? 'selected' : '' ?>>Organisateur</option>
                                                        <option value="moderateur" <?= ($org['role'] ?? '') === 'moderateur' ? 'selected' : '' ?>>Modérateur</option>
                                                        <option value="admin" <?= ($org['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button class="btn-edit" onclick="toggleEdit(this, <?= $org['id_organisateur'] ?>, 'org')">Modifier</button>
                                                    <button class="btn-save" onclick="saveChanges(<?= $org['id_organisateur'] ?>, 'org')" style="display: none;">Enregistrer</button>
                                                    <a href="?page=admin&section=users&action=delete&id=<?= $org['id_organisateur'] ?>"
                                                       onclick="return confirm('Voulez-vous vraiment supprimer cet organisateur ?')"
                                                       class="btn-delete">Supprimer</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5">Aucun organisateur trouvé</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <script>
                    function toggleEdit(button, id, type) {
                        const row = document.getElementById(`${type}-row-${id}`);
                        const displayValues = row.querySelectorAll('.display-value');
                        const editInputs = row.querySelectorAll('.edit-input');
                        const editButton = button;
                        const saveButton = row.querySelector('.btn-save');

                        displayValues.forEach(span => span.style.display = 'none');
                        editInputs.forEach(input => input.style.display = 'inline-block');
                        editButton.style.display = 'none';
                        saveButton.style.display = 'inline-block';
                    }

                    function saveChanges(id, type) {
                        const row = document.getElementById(`${type}-row-${id}`);
                        const inputs = row.querySelectorAll('.edit-input');
                        const formData = new FormData();
                        
                        formData.append('nom', inputs[0].value);
                        formData.append('prenom', inputs[1].value);
                        formData.append('email', inputs[2].value);

                        fetch(`?page=admin&section=${type === 'org' ? 'organisateurs' : 'users'}&action=update&id=${id}`, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.text())
                        .then(() => {
                            const displayValues = row.querySelectorAll('.display-value');
                            displayValues[0].textContent = inputs[0].value;
                            displayValues[1].textContent = inputs[1].value;
                            displayValues[2].textContent = inputs[2].value;

                            inputs.forEach(input => input.style.display = 'none');
                            displayValues.forEach(span => span.style.display = 'inline');
                            row.querySelector('.btn-save').style.display = 'none';
                            row.querySelector('.btn-edit').style.display = 'inline-block';
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            alert('Une erreur est survenue lors de la mise à jour');
                        });
                    }

                    function searchTable(inputId, tableId) {
                        const input = document.getElementById(inputId);
                        const filter = input.value.toLowerCase();
                        const table = document.getElementById(tableId);
                        const rows = table.getElementsByTagName('tr');

                        for (let i = 1; i < rows.length; i++) {
                            let found = false;
                            const cells = rows[i].getElementsByTagName('td');
                            
                            // Vérifier d'abord l'ID (première colonne)
                            const idCell = cells[0];
                            if (idCell && idCell.textContent.toLowerCase().includes(filter)) {
                                found = true;
                            }

                            // Si l'ID ne correspond pas, vérifier les autres colonnes
                            if (!found) {
                                for (let j = 1; j < cells.length - 1; j++) {
                                    const cell = cells[j];
                                    if (cell) {
                                        const displayValue = cell.querySelector('.display-value');
                                        const text = (displayValue ? displayValue.textContent : cell.textContent).toLowerCase();
                                        if (text.includes(filter)) {
                                            found = true;
                                            break;
                                        }
                                    }
                                }
                            }

                            rows[i].style.display = found ? '' : 'none';
                        }
                    }
                    </script>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <?php include(dirname(__FILE__) . '/../../views/common/footer.php'); ?>
</body>
</html> 