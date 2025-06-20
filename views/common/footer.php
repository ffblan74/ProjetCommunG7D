<?php
// Définir dynamiquement la base des chemins en fonction du fichier appelant
$basePath = (strpos($_SERVER['PHP_SELF'], 'index.php') !== false) ? './' : '../../';
?>
<footer>
  <div class="footer-container">
    <div class="footer-section about">
      <h4>Light Control</h4>
      <p>Votre plateforme de confiance pour découvrir le plaisir de votre maison autogérée.</p>
      <div class="contact-info">
        <p><i class="fas fa-envelope"></i> contact@lightcontrol.fr</p>
        <p><i class="fas fa-phone"></i> +33 1 23 45 67 89</p>
      </div>
    </div>

    <div class="footer-section links">
      <h4>Navigation</h4>
      <ul>
        <li><a href="<?= $basePath ?>index.php?page=home">Accueil</a></li>
        <li><a href="<?= $basePath ?>index.php?page=explorer">Explorer</a></li>
        <li><a href="<?= $basePath ?>index.php?page=about">À propos</a></li>
        <li><a href="<?= $basePath ?>index.php?page=contact">Contact</a></li>
      </ul>
    </div>

    <div class="footer-section resources">
      <h4>Ressources</h4>
      <ul>
        <li><a href="<?= $basePath ?>index.php?page=eco">Éco-conception</a></li>
        <li><a href="<?= $basePath ?>index.php?page=conditions_utilisation">Conditions d'utilisation</a></li>
        <li><a href="<?= $basePath ?>index.php?page=mentions_legales">Mentions légales</a></li>
        <li><a href="<?= $basePath ?>index.php?page=contact">Support</a></li>
      </ul>
    </div>

    <div class="footer-section social">
      <h4>Suivez-nous</h4>
      <div class="social-links">
        <a href="#" class="social-icon" title="Facebook"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="social-icon" title="Twitter"><i class="fab fa-twitter"></i></a>
        <a href="#" class="social-icon" title="Instagram"><i class="fab fa-instagram"></i></a>
        <a href="#" class="social-icon" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
      </div>
      <div class="newsletter">
        <h4>Newsletter</h4>
        <form class="newsletter-form">
          <input type="email" placeholder="Votre email">
          <button type="submit">S'abonner</button>
        </form>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <div class="footer-bottom-content">
      <p>&copy; <?= date('Y') ?> Light Control. Tous droits réservés.</p>
      <div class="footer-bottom-links">
        <a href="<?= $basePath ?>index.php?page=conditions_utilisation">Confidentialité</a>
        <span class="separator">|</span>
        <a href="<?= $basePath ?>index.php?page=mentions_legales">Mentions légales</a>
        <span class="separator">|</span>
        <a href="<?= $basePath ?>index.php?page=contact">Nous contacter</a>
      </div>
    </div>
  </div>
  
  <!-- Font Awesome pour les icônes -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="assets/CSS/footer.css">
</footer>



