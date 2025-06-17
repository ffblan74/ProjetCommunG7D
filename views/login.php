<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion</title>
  <link rel="stylesheet" href="assets/CSS/login.css">
  <link rel="icon" type="image/x-icon" href="assets/images/favicon.png">

</head>
<body>
  <div class="signin-container">
    <div class="signin-box">
      <h1>Connexion</h1>
      
      <!-- Affichage des messages d'erreur -->
      <?php if (!empty($error_message)): ?>
          <p style="color: red; font-weight: bold;"><?php echo htmlspecialchars($error_message); ?></p>
      <?php endif; ?>

      <!-- Formulaire -->
      <form action="?page=login" method="post">
        <!-- Adresse email -->
        <div class="form-group">
          <label for="username">Adresse email</label>
          <input type="text" id="username" name="username" placeholder="Votre adresse email" required>
        </div>

        <!-- Mot de passe -->
        <div class="form-group" style="position: relative;">
          <label for="password">Mot de passe</label>
          <input type="password" id="password" name="password" placeholder="Votre mot de passe" required
                 style="width: 100%; height: 40px; padding-right: 40px; box-sizing: border-box;">
          <img src="assets/images/eye-icon.png" alt="Afficher le mot de passe" id="toggle-password"
               style="position: absolute; right: 12px; top: 70%; transform: translateY(-50%); cursor: pointer; width: 35px; height: 35px; opacity: 0.8;">
        </div>

        <!-- Bouton de connexion -->
        <button type="submit" class="btn">Se connecter</button>
      </form>

      <!-- Liens vers inscription et mot de passe oublié -->
      <div class="links">
          <p><a href="?page=signin">Pas encore de compte ? Inscrivez-vous</a></p>
          <p><a href="?page=password_forgotten">Mot de passe oublié ?</a></p>
      </div>
    </div>
  </div>
  <script src="assets/JS/login.js"></script>
</body>
</html>
