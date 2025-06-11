<?php
session_start(); // Démarre la session
session_unset(); // Supprime toutes les variables de session
session_destroy(); // Détruit la session

// Rediriger vers la page d'accueil (index.php) via le système de routage
header("Location: ?page=home");  // Redirection vers la page d'accueil
exit;
?>
