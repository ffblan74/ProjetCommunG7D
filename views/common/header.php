<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Définir la base du chemin dynamiquement
$basePath = (basename($_SERVER['PHP_SELF']) === 'index.php') ? './' : '../';
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$isOrganizer = isset($_SESSION['role']) && $_SESSION['role'] === 'organisateur';
?>

<header>
  <link rel="stylesheet" href="<?= $basePath ?>assets/CSS/header.css">
  <nav class="navbar">
    <div class="navbar-left">
      <a href="<?= $basePath ?>?page=home">
        <img src="<?= $basePath ?>assets/images/logo.png" alt="Logo Light Control" class="logo">
      </a>
    </div>

    <div class="menu-toggle">
      <span></span>
      <span></span>
      <span></span>
    </div>

    <ul class="navbar-menu">
      <li><a href="<?= $basePath ?>?page=home" class="<?= $page === 'home' ? 'active' : '' ?>">Accueil</a></li>
      <li><a href="<?= $basePath ?>?page=statistiques" class="<?= $page === 'statistiques' ? 'active' : '' ?>">Statistiques</a></li>
      <li><a href="<?= $basePath ?>?page=dashboard" class="<?= $page === 'dashboard' ? 'active' : '' ?>">Dashboard</a></li>
    </ul>
    <div class="navbar-right">
      <?php if ($isLoggedIn): ?>
        <?php if ($_SESSION['role'] === 'administrateur'): ?>
          <a href="<?= $basePath ?>?page=admin" class="profile-button">Tableau de bord</a>
          <a href="<?= $basePath ?>?page=create_event" class="profile-button">Créer un événement</a>
        <?php elseif ($_SESSION['role'] === 'participant'): ?>
          <a href="<?= $basePath ?>?page=client_profil" class="profile-button">Mon profil</a>
        <?php endif; ?>
        <a href="<?= $basePath ?>?page=logout" class="connect-button">Se déconnecter</a>
      <?php else: ?>
        <a href="<?= $basePath ?>?page=login" class="connect-button">Se connecter</a>
      <?php endif; ?>
    </div>
  </nav>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const menuToggle = document.querySelector('.menu-toggle');
      const navbarMenu = document.querySelector('.navbar-menu');
      const navbarRight = document.querySelector('.navbar-right');
      const dropdowns = document.querySelectorAll('.dropdown');

      // Toggle menu on burger click
      menuToggle.addEventListener('click', function() {
        this.classList.toggle('active');
        navbarMenu.classList.toggle('active');
        navbarRight.classList.toggle('active');
      });

      // Handle dropdowns on mobile
      dropdowns.forEach(dropdown => {
        const dropdownButton = dropdown.querySelector('.dropdown-button');
        dropdownButton.addEventListener('click', function(e) {
          if (window.innerWidth <= 768) {
            e.preventDefault();
            dropdown.classList.toggle('active');
          }
        });
      });

      // Close menu when clicking outside
      document.addEventListener('click', function(e) {
        if (!e.target.closest('.navbar')) {
          menuToggle.classList.remove('active');
          navbarMenu.classList.remove('active');
          navbarRight.classList.remove('active');
          dropdowns.forEach(dropdown => dropdown.classList.remove('active'));
        }
      });

      // Handle window resize
      window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
          menuToggle.classList.remove('active');
          navbarMenu.classList.remove('active');
          navbarRight.classList.remove('active');
          dropdowns.forEach(dropdown => dropdown.classList.remove('active'));
        }
      });
    });
  </script>
</header>
