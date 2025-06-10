<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Préférences d'Événements</title>
  <link rel="stylesheet" href="../assets/CSS/tags_signin.css">
  <link rel="icon" type="image/x-icon" href="assets/images/favicon.jpg">
</head>
<body>
  <div class="signin-container">
    <div class="signin-box">
      <h1>Intéressé par des événements en particulier ?</h1>
      <p class="info-text">
        <strong>Note :</strong> Vous pouvez cocher un maximum de 3 cases. Si vous en cochez plus de 3, vous devrez en décocher une pour pouvoir en sélectionner d'autres.
      </p>
      <form method="post">
        <div class="checkbox-group">
          <label><input type="checkbox" name="preferences[]" value="concert"> Concert</label>
          <label><input type="checkbox" name="preferences[]" value="brocante"> Brocante</label>
          <label><input type="checkbox" name="preferences[]" value="festival"> Festival</label>
          <label><input type="checkbox" name="preferences[]" value="theatre"> Spectacle de théâtre</label>
          <label><input type="checkbox" name="preferences[]" value="film"> Projection de film</label>
          <label><input type="checkbox" name="preferences[]" value="art"> Exposition d'art</label>
          <label><input type="checkbox" name="preferences[]" value="atelier"> Atelier culinaire</label>
          <label><input type="checkbox" name="preferences[]" value="sport"> Compétition sportive</label>
          <label><input type="checkbox" name="preferences[]" value="marche"> Marché artisanal</label>
          <label><input type="checkbox" name="preferences[]" value="jeux"> Soirée jeux</label>
        </div>
        <button type="submit" class="btn">Continuer</button>
      </form>
    </div>
  </div>

  <script src="../assets/JS/tags_signin.js"></script>
</body>
</html>
