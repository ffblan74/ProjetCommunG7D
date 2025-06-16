
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
<main>

  <!-- Section héroïque -->
  <div class="hero-section">

    <h1>Découvrez les plaisirs d'une maison autogérée 🏠</h1>
    <p>Ajustez les paramètres comme bon vous semble !</p>
    <div class="search-container">
      <a href="<?= $basePath ?>?page=statistiques" class="<?= $page === 'statistiques' ? 'active' : '' ?> search-button">Voir les statistiques</a>
      <a href="<?= $basePath ?>?page=dashboard" class="<?= $page === 'dashboard' ? 'active' : '' ?> search-button">Gérer la maison</a>
      
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
    <h2>Les chiffres clé</h2>
    <p>Un aperçu des données importantes de vos capteurs ou des mesures de votre région.</p>
    <div class="features-grid-wrapper">
      <div class="features-grid <?php if (!$isLoggedIn) echo 'blur-active'; ?>">
        <div class="feature-card">
          <i class="fa-solid fa-cloud-sun-rain" style="color:#3C75A6"></i>
          <h3>Météo du jour</h3>
          <p>Organisez facilement vos événements et gérez les inscriptions en quelques clics.</p>
        </div>
        <?php if (!(isset($_SESSION['user_id']))): ?>
          <div class="feature-card">
            <i class="fas fa-sign-in-alt"></i>
            <h3>Inscription</h3>
            <p>Inscrivez-vous pour accéder à toutes les fonctionnalités et statistiques.</p>
          </div>
          <div>
            <p>Vous souhaitez voir toutes les statistiques ? Inscrivez-vous et synchronisez vos appareils Light Control.</p>
            <a href="<?= $basePath ?>?page=login" class="connect-button">Se connecter</a>
          </div>
          <div class="overlay-text">👉 Voici du texte lisible au-dessus du flou</div>
        <?php endif; ?>
        <div class="feature-card">
          <i class="fas fa-map-marker-alt"></i>
          <h3>Luminosité</h3>
          <p>Trouvez des événements près de chez vous grâce à notre système de géolocalisation.</p>
        </div>
        <div class="feature-card">
          <i class="fas fa-users"></i>
          <h3>État de la lumière</h3>
          <p>Suivez facilement les inscriptions et communiquez avec vos participants.</p>
        </div>
        <div class="feature-card">
          <i class="fa-solid fa-table-columns"></i>
          <h3>État des volets</h3>
          <p>Restez informé des nouveaux événements qui correspondent à vos centres d'intérêt.</p>
        </div>
        
      </div>
    </div>
    
  </div>

  <!-- Affichage des statistiques -->
  <section class="stats-section">
    <h2>Pourquoi choisir Light Control ?</h2>
    <p>Avec Light Control, devenez maître de votre logement. Light Control vous permet de contrôler vos appareils domotiques à distance, de manière simple et sécurisée, pour augmenter votre confort à domicile.</p>
    <div class="stats-container">
      <div class="stat" data-target="<?= $stats['events'] ?>">
        <h3>+<span class="count"><?= $stats['events'] ?></span></h3>
        <p>Appareils recensés</p>
      </div>
      <div class="stat" data-target="<?= $stats['participants'] ?>">
        <h3>+<span class="count"><?= $stats['participants'] ?></span></h3>
        <p>Utilisateurs actifs</p>
      </div>
      <div class="stat" data-target="<?= $stats['support'] ?>">
        <h3><span class="count"><?= $stats['support'] ?></span>/7</h3>
        <p>Support client</p>
      </div>
    </div>
  </section>

      </main>
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