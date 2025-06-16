<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Light Control - Événements</title>
  <link rel="stylesheet" href="assets/CSS/index.css">
  
  <?php
  // Inclure la configuration si nécessaire
  if (!defined('BASE_PATH')) {
      require '../config.php';
  }
  ?>

  <link rel="icon" type="image/x-icon" href="/src/assets/images/favicon.jpg">
</head>
<body>
  <!-- Header -->
  <?php include 'views/common/header.php'; ?>

  <!-- Section héroïque -->
  <div class="hero-section">
    <h1>Découvrez les événements qui vous passionnent</h1>
    <p>Explorez des expériences uniques autour de vous</p>
    <div class="search-container">
      <form action="/src/?page=explorer" method="GET">
        <input type="hidden" name="page" value="explorer">
        <input type="text" name="query" class="search-bar" placeholder="Rechercher un événement">
        <button type="submit" class="search-button">Rechercher</button>
      </form>
    </div>
  </div>

  <!-- Carrousel d'événements -->
  <section class="events-carousel">
    <h2>Événements à venir</h2>
    <div class="carousel">
      <?php if (count($events) > 0): ?>
          <?php foreach ($events as $event): ?>
            <div class="carousel-item">
              <img src="<?= !empty($event['image_path']) ? $event['image_path'] : '/src/assets/images/default-event.jpg' ?>" 
                   alt="<?= htmlspecialchars($event['nom_event']); ?>">
              <h3>
                <?php
                $date = new DateTime($event['date_planifiee']);
                echo $date->format('d M Y') . ' - ' . htmlspecialchars($event['nom_event']);
                ?>
              </h3>
              <p><?= htmlspecialchars($event['description']); ?></p>
              <a href="/src/?page=event_details&id=<?= $event['id_event'] ?>" class="btn-details">Voir les détails</a>
            </div>
          <?php endforeach; ?>
      <?php else: ?>
          <p>Aucun événement à venir pour le moment.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- Section fonctionnalités -->
  <div class="features">
    <h2>Nos fonctionnalités clés</h2>
    <p>Découvrez tout ce que Light Control peut vous apporter pour vos événements.</p>
    <div class="features-grid">
      <div class="feature-card">
        <i class="fas fa-calendar-plus"></i>
        <h3>Créez vos événements</h3>
        <p>Organisez facilement vos événements et gérez les inscriptions en quelques clics.</p>
      </div>
      <div class="feature-card">
        <i class="fas fa-map-marker-alt"></i>
        <h3>Événements locaux</h3>
        <p>Trouvez des événements près de chez vous grâce à notre système de géolocalisation.</p>
      </div>
      <div class="feature-card">
        <i class="fas fa-users"></i>
        <h3>Gestion des participants</h3>
        <p>Suivez facilement les inscriptions et communiquez avec vos participants.</p>
      </div>
      <div class="feature-card">
        <i class="fas fa-bell"></i>
        <h3>Notifications</h3>
        <p>Restez informé des nouveaux événements qui correspondent à vos centres d'intérêt.</p>
      </div>
    </div>
  </div>

  <!-- Affichage des statistiques -->
  <section class="stats-section">
    <h2>Pourquoi choisir Light Control ?</h2>
    <p>Avec Light Control, découvrez des événements qui enrichissent votre quotidien. Que vous soyez amateur de concerts, d'art ou de moments en communauté, nous avons tout ce qu'il vous faut.</p>
    <div class="stats-container">
      <div class="stat" data-target="<?= $stats['events'] ?>">
        <h3>+<span class="count"><?= $stats['events'] ?></span></h3>
        <p>Événements répertoriés</p>
      </div>
      <div class="stat" data-target="<?= $stats['participants'] ?>">
        <h3>+<span class="count"><?= $stats['participants'] ?></span></h3>
        <p>Participants actifs</p>
      </div>
      <div class="stat" data-target="<?= $stats['support'] ?>">
        <h3><span class="count"><?= $stats['support'] ?></span>/7</h3>
        <p>Support client</p>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <?php include 'views/common/footer.php'; ?>
  <script src="assets/JS/index.js"></script>
  <!-- Font Awesome pour les icônes -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  
  <!-- Script pour la barre de recherche -->
  <script>
    document.querySelector('.search-container form').addEventListener('submit', function(e) {
      const searchInput = this.querySelector('input[name="query"]');
      if (!searchInput.value.trim()) {
        e.preventDefault();
        searchInput.focus();
      }
    });
  </script>
</body>
</html>
