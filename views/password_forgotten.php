<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mot de passe oublié </title>
  <link rel="stylesheet" href="assets/CSS/password_forgotten.css">
  <link rel="icon" type="image/x-icon" href="../assets/favicon.jpg">
</head>
<body>

  <div class="password-forgotten-container">
    <h1>Mot de passe oublié</h1>
    
    <?php if (SessionManager::get('success_message')): ?>
      <div class="alert alert-success">
        <?php 
        echo SessionManager::get('success_message');
        SessionManager::remove('success_message');
        ?>
      </div>
    <?php endif; ?>

    <?php if (SessionManager::get('error_message')): ?>
      <div class="alert alert-error">
        <?php 
        echo SessionManager::get('error_message');
        SessionManager::remove('error_message');
        ?>
      </div>
    <?php endif; ?>

    <form action="?page=password_forgotten" method="POST" class="password-forgotten-form">
      <div class="form-group">
        <label for="username">Adresse email</label>
        <input type="text" id="mail" name="mail" required>
      </div>

      <button type="submit" class="btn-reset">Réinitialiser le mot de passe</button>
    </form>

    <div class="links">
      <a href="?page=login">Retour à la connexion</a>
    </div>
  </div>
</body>
</html>
