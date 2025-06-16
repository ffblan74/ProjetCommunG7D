<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="assets/CSS/contact.css">
    <link rel="stylesheet" href="assets/CSS/footer.css">
    <link rel="stylesheet" href="assets/CSS/header.css">
    <link rel="icon" type="image/jpg" href="../assets/images/favicon.jpg">
</head>
<body>
    <?php include 'views/common/header.php'; ?>

    <div class="page">
        <div class="contact-container">
            <h1>Contactez-nous</h1>
            <!-- Afficher les retours du formulaire -->
            <?= $feedback ?? ''; ?>
            <form class="contact-form" method="POST">
                <label for="name">Prénom</label>
                <input type="text" name="prenom" id="prenom" required>
                <label for="surname">Nom</label>
                <input type="text" name="nom" id="nom" required>
                <label for="email">Adresse mail</label>
                <input type="email" name="mail" id="mail" required>
                <br>
                <label for="subject">Sujet</label>
                <select name="subject" id="subject">
                    <option value="participant">Question en tant que participant</option>
                    <option value="organisateur">Question en tant qu'organisateur</option>
                    <option value="general">Question générale sur le site</option>
                    <option value="problem">Problème/Bug</option>
                    <option value="other">Autre</option>
                </select>
                <br>
                <br>
                <label for="message">Message</label>
                <textarea name="message" id="message" rows="5" placeholder="Votre message" required></textarea>
                <br>
                <div class="checkbox-container">
                    <input type="checkbox" name="conditions" id="conditions" required>
                    <label for="conditions">J'accepte les <a href="<?= $basePath ?>?page=conditions_utilisation">conditions d'utilisation</a>.</label>
                </div>
                <br>
                <input type="submit" class="submit" value="Envoyer">
            </form>
        </div>
    </div>
    <?php
    if (isset($_GET['success']) && $_GET['success'] === 'true'): ?>
    <div id="popup" class="popup">
        <div class="popup-content">
            <span class="close-btn" onclick="closePopup()">×</span>
            <h2>Votre événement a été créé avec succès !</h2>
            <button class="btn" onclick="closePopup()">OK</button>
        </div>
    </div>
   <?php endif; ?>

    <?php include 'views/common/footer.php'; ?>
</body>
</html>
