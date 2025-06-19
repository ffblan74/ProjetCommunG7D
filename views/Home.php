<?php
// Inclure la configuration si nécessaire
if (!defined('BASE_PATH')) {
    require '../config.php';
}

$statsFile = __DIR__ . '/../controllers/getstats.php';
if (file_exists($statsFile)) {
    $statsJSON = file_get_contents($statsFile);
    $stats = json_decode($statsJSON, true);
    
    // Vérification du décodage JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        $stats = ['capteurs' => 0, 'utilisateurs' => 0, 'support' => 0];
        error_log("Erreur de décodage JSON: " . json_last_error_msg());
    }
} else {
    $stats = ['capteurs' => 0, 'utilisateurs' => 0, 'support' => 0];
    error_log("Fichier getstats.php introuvable: " . $statsFile);
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Light Control - Votre maison intelligente</title>
  <link rel="stylesheet" href="assets/CSS/home.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="icon" type="image/x-icon" href="assets/images/favicon.png">
  <script src="assets/JS/IPaddress.js"></script>
</head>
<body>
  <!-- Header -->
  <?php include 'views/common/header.php'; ?>

  <main>
    <!-- Section héroïque améliorée -->
    <section class="hero-section">
      <div class="hero-content">
        <h1>Transformez votre maison en un espace intelligent 🏠</h1>
        <p class="hero-subtitle">Contrôle, confort et économie d'énergie à portée de main</p>
        <div class="cta-buttons">
          <a href="<?= $basePath ?>?page=statistiques" class="cta-button <?= $page === 'statistiques' ? 'active' : '' ?>">
            <i class="fas fa-chart-line"></i> Voir les statistiques
          </a>
          <a href="<?= $basePath ?>?page=dashboard" class="cta-button primary <?= $page === 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-home"></i> Gérer la maison
          </a>
        </div>
      </div>
    </section>

    <!-- Section fonctionnalités améliorée -->
    <section class="features-section">
      <div class="container">
        <div class="section-header">
          <h2>Vos données en temps réel</h2>
          <p class="section-subtitle">Surveillez et contrôlez tous les aspects de votre habitat</p>
        </div>
        
        <div class="features-grid">
          <?php if (!($isLoggedIn)): ?>
            <div class="feature-card auth-card">
              <div class="feature-icon">
                <i class="fas fa-user-plus"></i>
              </div>
              <h3>Rejoignez notre communauté</h3>
              <p>Inscrivez-vous pour débloquer toutes les fonctionnalités avancées de Light Control.</p>
              <a href="<?= $basePath ?>?page=login" class="feature-button">
                <i class="fas fa-sign-in-alt"></i> Se connecter
              </a>
            </div>
          <?php else: ?>
            <div class="feature-card">
              <div class="feature-icon temperature">
                <i class="fa-solid fa-temperature-half"></i>
              </div>
              <h3>Température</h3>
              <p>22.5°C • Confort optimal</p>
              <div class="sensor-status">
              </div>
            </div>
            
            <div class="feature-card">
              <div class="feature-icon humidity">
                <i class="fa-solid fa-droplet"></i>
              </div>
              <h3>Humidité</h3>
              <p>45% • Niveau idéal</p>
              <div class="sensor-status">
              </div>
            </div>
            
            <div class="feature-card">
              <div class="feature-icon light">
                <i class="fa-solid fa-sun"></i>
              </div>
              <h3>Luminosité</h3>
              <p>650 lux • Lumière naturelle</p>
              <div class="sensor-status">
              </div>
            </div>
            
            <div class="feature-card">
              <div class="feature-icon bulb">
                <i class="fa-solid fa-lightbulb"></i>
              </div>
              <h3>Éclairage</h3>
              <p>3/8 lumières allumées</p>
              <div class="sensor-status">
              </div>
            </div>
            
            <div class="feature-card">
              <div class="feature-icon blinds">
                <i class="fa-solid fa-blinds"></i>
              </div>
              <h3>Volets</h3>
              <p>70% ouverts</p>
              <div class="sensor-status">
              </div>
            </div>
            
            <div class="feature-card">
              <div class="feature-icon weather">
                <i class="fa-solid fa-cloud-sun"></i>
              </div>
              <h3>Météo locale</h3>
              <p>Ensoleillé • 24°C</p>
              <div class="sensor-status">
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

      <!-- Section statistiques améliorée -->
    <section class="stats-section">
      <div class="container">
        <div class="section-header">
          <h2>Light Control en quelques chiffres</h2>
          <p class="section-subtitle">Avec Light Control, devenez maître de votre logement. Light Control vous permet de contrôler vos appareils domotiques à distance, de manière simple et sécurisée pour augmenter votre confort à domicile.</p>
        </div>
  
        <div class="stats-container">
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-microchip"></i>
            </div>
            <div class="stat" data-target="<?= $stats['capteurs'] ?>">
              

            </div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-users"></i>
            </div>
            <h3 data-target=>+<span class="count">14</span></h3>
            <p>Utilisateurs actifs</p>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-headset"></i>
            </div>
            <h3><span class=>24</span>/7</h3>
            <p>Support disponible</p>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <?php include 'views/common/footer.php'; ?>

  <script src="assets/JS/home.js"></script>
</body>
</html>