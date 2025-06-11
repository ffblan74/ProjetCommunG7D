<?php
// Démarrage de la session si nécessaire
session_start();

// Inclure la configuration du site si nécessaire
require_once __DIR__ . '/../config.php';

// Inclure la vue correspondante
require_once BASE_PATH . '/views/about.php';
?>
