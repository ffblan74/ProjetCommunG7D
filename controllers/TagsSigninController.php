<?php
session_start(); // Démarrer la session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les préférences sélectionnées
    $preferences = isset($_POST['preferences']) ? $_POST['preferences'] : [];

    // Enregistrer les préférences dans la session ou dans la base de données
    $_SESSION['preferences'] = $preferences;

    // Redirection vers l'index
    header('Location: index.php');
    exit;
}

// Inclure la vue
require __DIR__ . '/../views/tags_signin.php';
?>
