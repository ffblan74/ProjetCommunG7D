
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Light Control</title>

  <link rel="stylesheet" href="assets/CSS/index.css">
  <link rel="icon" type="image/x-icon" href="assets/images/favicon.png">

  <?php
  // Inclure la configuration si nécessaire
  if (!defined('BASE_PATH')) {
      require '../config.php';
  }

  $statsJSON = file_get_contents('/../controllers/getstats.php');
  $stats = json_decode($statsJSON, true);


  ?>
  <script src="assets/JS/IPaddress.js"></script>
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

 

  <!-- Section fonctionnalités -->
  <div class="features">
    <h2>Les chiffres clés</h2>
    <p>Un aperçu des données importantes de vos capteurs ou des mesures de votre région.</p>
    <div class="features-grid">
      <div class="feature-card">
        <i class="fa-solid fa-cloud-sun-rain"></i>
        <h3>Météo du jour</h3>
        <p>Organisez facilement vos événements et gérez les inscriptions en quelques clics.</p>
      </div>
      <?php if (!($isLoggedIn)): ?>
        <div class="feature-card">
          <i class="fas fa-sign-in-alt"></i>
          <h3>Inscription</h3>
          <p>Inscrivez-vous pour accéder à toutes les fonctionnalités et statistiques.</p>
        </div>
        <div>
          <p>Vous souhaitez voir toutes les statistiques ? Inscrivez-vous et synchronisez vos appareils Light Control.</p>
          <a href="<?= $basePath ?>?page=login" class="connect-button">Se connecter</a>
        </div>
      <?php else: ?>
        <div class="feature-card">
          <i class="fa-solid fa-temperature-half"></i>
          <h3>Température</h3>
          <p>Trouvez des événements près de chez vous grâce à notre système de géolocalisation.</p>
        </div>
        <div class="feature-card">
          <i class="fa-solid fa-droplet"></i>
          <h3>Humidité</h3>
          <p>Trouvez des événements près de chez vous grâce à notre système de géolocalisation.</p>
        </div>
        <div class="feature-card">
          <i class="fa-solid fa-sun"></i>
          <h3>Luminosité</h3>
          <p>Trouvez des événements près de chez vous grâce à notre système de géolocalisation.</p>
        </div>
        <div class="feature-card">
          <i class="fa-solid fa-lightbulb"></i>
          <h3>État de la lumière</h3>
          <p>Suivez facilement les inscriptions et communiquez avec vos participants.</p>
        </div>
        <div class="feature-card">
          <i class="fa-solid fa-table-columns"></i>
          <h3>État des volets</h3>
          <p>Restez informé des nouveaux événements qui correspondent à vos centres d'intérêt.</p>
        </div>
      <?php endif; ?>
    </div>
    
  </div>

  <!-- Affichage des statistiques -->
  <section class="stats-section">
    <h2>Pourquoi choisir Light Control ?</h2>
    <p>Avec Light Control, devenez maître de votre logement. Light Control vous permet de contrôler vos appareils domotiques à distance, de manière simple et sécurisée, pour augmenter votre confort à domicile.</p>
    <div class="stats-container">
      <div class="stat" data-target="<?= $stats['capteurs'] ?>">
        <h3>+<span class="count"><?= $stats['capteurs'] ?></span></h3>
        <p>Appareils recensés</p>
      </div>
      <div class="stat" data-target="<?= $stats['utilisateurs'] ?>">
        <h3>+<span class="count"><?= $stats['utilisateurs'] ?></span></h3>
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