<?php
// Démarrer la session
session_start();

// Inclure le modèle si nécessaire (s'il y en avait un)
require_once __DIR__ . '/../config.php';

// Afficher la vue Mentions légales
require_once __DIR__ . '/../views/MentionsLegales.php';
?>
