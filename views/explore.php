<?php
// La variable $events est déjà fournie par ExploreController

function formatDate($dateString) {
    $date = new DateTime($dateString);
    return $date->format('d/m/y \à H\h');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explorer les événements - Plan It</title>
    <link rel="stylesheet" href="/src/assets/CSS/explore.css">
</head>
<body>
    <?php include 'views/common/header.php'; ?>

    <main>
        <div class="search-section">
            <div class="search-bar">
                <form method="GET" action="/src/">
                    <input type="hidden" name="page" value="explorer">
                    <input type="text" name="query" placeholder="Rechercher un événement" value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>">
                    <button type="submit">Rechercher</button>
                </form>
            </div>
        </div>

        <div class="filters-section">
            <h3>Filtres</h3>
            <form action="/src/" method="GET" class="filters-form">
                <input type="hidden" name="page" value="explorer">
                
                <div class="filter-group">
                    <label for="localisation">Localisation</label>
                    <input type="text" id="localisation" name="localisation" list="localisations" value="<?= isset($_GET['localisation']) ? htmlspecialchars($_GET['localisation']) : '' ?>" placeholder="Entrez une adresse">
                    <datalist id="localisations">
                        <?php foreach ($localisations as $loc): ?>
                            <option value="<?= htmlspecialchars($loc) ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>

                <div class="filter-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" value="<?= isset($_GET['date']) ? htmlspecialchars($_GET['date']) : '' ?>">
                </div>

                <div class="filter-group">
                    <label for="participants">Nombre de participants maximum</label>
                    <select id="participants" name="participants">
                        <option value="">Tous</option>
                        <option value="10" <?= isset($_GET['participants']) && $_GET['participants'] == 10 ? 'selected' : '' ?>>10 participants ou moins</option>
                        <option value="20" <?= isset($_GET['participants']) && $_GET['participants'] == 20 ? 'selected' : '' ?>>20 participants ou moins</option>
                        <option value="50" <?= isset($_GET['participants']) && $_GET['participants'] == 50 ? 'selected' : '' ?>>50 participants ou moins</option>
                        <option value="100" <?= isset($_GET['participants']) && $_GET['participants'] == 100 ? 'selected' : '' ?>>100 participants ou moins</option>
                    </select>
                </div>

                <button type="submit" class="filter-button">Appliquer les filtres</button>
            </form>
        </div>

        <section class="event-list">
            <h2><?php echo isset($_GET['query']) && !empty($_GET['query']) ? "Résultats de recherche" : "Événements à venir"; ?></h2>
            <div class="event-cards">
                <?php if (!empty($events)) { ?>
                    <?php foreach ($events as $event) { ?>
                        <a href="?page=event_details&id=<?= $event['id_event'] ?>" class="event-card">
                            <h3>
                                <?php echo htmlspecialchars($event['nom_event']); ?>
                                <?php if ($event['statut'] === 'sous_reserve'): ?>
                                    <span class="badge badge-warning">Sous réserve</span>
                                <?php endif; ?>
                            </h3>
                            <div class="event-info">
                                <p class="event-location"><?php echo htmlspecialchars($event['adresse_event']); ?></p>
                                <p class="event-date"><?php echo formatDate($event['date_planifiee']); ?></p>
                                <p class="event-description"><?php echo htmlspecialchars(substr($event['description'], 0, 150)) . (strlen($event['description']) > 150 ? '...' : ''); ?></p>
                                <p class="event-organizer">Organisé par <?php echo htmlspecialchars($event['nom_organisateur'] . ' ' . $event['prenom']); ?></p>
                            </div>
                        </a>
                    <?php } ?>
                <?php } else { ?>
                    <p class="no-events">Aucun événement trouvé.</p>
                <?php } ?>
            </div>
        </section>
    </main>

    <?php include 'views/common/footer.php'; ?>
</body>
</html>
