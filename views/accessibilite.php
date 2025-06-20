<?php
if (!defined('BASE_PATH')) require '../config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Accessibilité | Light Control</title>
  <link rel="stylesheet" href="assets/CSS/accessibilite.css" />
  <link rel="icon" href="assets/images/favicon.png">
  <style>
    body {
      align-items: center;
      justify-content: center;
    }
  </style>
</head>
<body>
  <?php include 'views/common/header.php'; ?>

  <aside class="accessibility-settings">
    <h2>Paramètres d'accessibilité</h2>
    

    <div class="setting-line">
      <div class="setting color">
        <label for="color">Thème</label>
        <input type="color" id="color" value="#4a6fa5">
      </div>
      <div class="setting slider">
        <label for="saturation">Saturation</label>
        <input type="range" id="saturation" min="0" max="1" step="0.1" value="1">
      </div>
    </div>
    

    
    <div class="setting-line">
      <div class="setting select">
        <label for="font-family">Police</label>
        <select id="font-family">
          <option value="Arial, sans-serif">Arial</option>
          <option value="Verdana, sans-serif">Verdana</option>
          <option value="Calibri, sans-serif">Calibri</option>
          <option value="Trebuchet MS, sans-serif">Trebuchet</option>
          <option value="Helvetica, sans-serif">Helvetica</option>
          <option value="Tahoma, sans-serif">Tahoma</option>
          <option value="Achemine, sans-serif">Achemine</option>
        </select>
      </div>

      <div class="setting slider">
        <label for="font-size">Taille de la police</label>
        <input type="range" id="font-size" min="0.8" max="1.5" step="0.05" value="1">
      </div>

      <div class="setting select">
        <label for="line-spacing">Espacement du texte</label>
        <select id="line-spacing">
          <option value="1.2">Compact</option>
          <option value="1.6">Normal</option>
          <option value="2">Spacieux</option>
        </select>
      </div>
    </div>
    

    <div class="setting checkbox-setting">
      <label><input type="checkbox" id="reduce-motion"> Réduire les animations</label>
    </div>

    <div class="setting checkbox-setting">
      <label><input type="checkbox" id="underline-links" value=""> Souligner les liens</label>
    </div>

    <button id="reset-preferences" style="margin-top: auto; padding: 0.75rem; font-weight: bold;">
      Réinitialiser les paramètres
    </button>
    
  </aside>
  <script src="assets/JS/accessibilite.js"></script>
</body>
</html>
