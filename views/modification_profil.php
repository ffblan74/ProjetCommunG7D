<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modifier le Profil</title>
  <link rel="stylesheet" href="../assets/CSS/client_profil.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
  <!-- Sidebar Section -->
  <?php include 'sidebar.php'; ?>

  <!-- Main Content -->
  <div class="main-content">
    <!-- Header -->
    <div class="profile-header">
      <h2>Modifier le Profil</h2>
    </div>

    <!-- Formulaire de Modification -->
    <div class="profile-edit-container">
      <form method="post" enctype="multipart/form-data">
        <div class="form-group">
          <label for="username">Nouveau Pseudo :</label>
          <input type="text" id="username" name="username" placeholder="Saisissez votre nouveau pseudo" required>
        </div>

        <div class="form-group">
          <label for="profile-photo">Nouvelle Photo de Profil :</label>
          <input type="file" id="profile-photo" name="profile_photo" accept="image/*">
        </div>

        <div class="form-group">
          <label for="bio">Bio :</label>
          <textarea id="bio" name="bio" rows="4" placeholder="Écrivez quelque chose sur vous..."></textarea>
        </div>

        <!-- Boutons alignés -->
        <div class="form-buttons">
          <button type="submit" class="btn save">Enregistrer</button>
          <a href="index.php?page=client_profile" class="btn cancel">Annuler</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
