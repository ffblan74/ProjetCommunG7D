<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Vérification du code</title>
  <link rel="stylesheet" href="assets/CSS/password_forgotten.css">
  <link rel="icon" type="image/x-icon" href="assets/images/favicon.png">

</head>
<body>

  <main class="main-content">
    <div class="password-forgotten-container">
      
      <?php if (!empty($error_message)): ?>
        <div class="alert alert-error"><?php echo $error_message; ?></div>
      <?php endif; ?>

      <?php 
      // Si l'utilisateur n'a pas encore validé son code, on affiche le formulaire de code
      if (!isset($_SESSION['reset_verified']) || !$_SESSION['reset_verified']): 
      ?>
        <h1>Vérifiez votre boîte mail</h1>
        <p>Nous avons envoyé un code à 5 chiffres à <strong><?php echo htmlspecialchars($_SESSION['reset_email']); ?></strong>. Il expire dans 10 minutes.</p>

        <form action="?page=verify_code" method="POST" class="password-forgotten-form">
          <div class="form-group">
            <label for="code">Code de vérification</label>
            <input type="text" id="code" name="code" required pattern="\d{5}" title="Entrez les 5 chiffres.">
          </div>
          <button type="submit" name="verify_code" class="btn-reset">Vérifier le code</button>
        </form>
      
      <?php else: ?>
        <h1>Choisir un nouveau mot de passe</h1>
        <form action="?page=verify_code" method="POST" class="password-forgotten-form">
          <div class="form-group">
            <label for="password">Nouveau mot de passe</label>
            <input type="password" id="password" name="password" required>
          </div>
          <div class="form-group">
            <label for="password_confirm">Confirmer le mot de passe</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
          </div>
          <button type="submit" name="update_password" class="btn-reset">Enregistrer</button>
        </form>
      <?php endif; ?>

    </div>
  </main>

</body>
</html>