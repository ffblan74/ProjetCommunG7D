<?php
// controllers/VerifyCodeController.php

require_once __DIR__ . '/../utils/SessionManager.php';
require_once __DIR__ . '/../models/UserModel.php';

SessionManager::startSession();

$userModel = new UserModel();
$error_message = '';
$emailForReset = $_SESSION['reset_email'] ?? null;

// Si l'utilisateur n'est pas dans le processus de reset, on le redirige.
if (!$emailForReset) {
    header('Location: /?page=login');
    exit();
}

// -- PARTIE 1 : Vérification du code --
if (isset($_POST['verify_code'])) {
    $submittedCode = $_POST['code'] ?? '';
    
    // Vérifier si le code en session existe et n'a pas expiré
    if (isset($_SESSION['reset_code']) && time() < $_SESSION['reset_expiry']) {
        if ($submittedCode == $_SESSION['reset_code']) {
            $_SESSION['reset_verified'] = true;
            unset($_SESSION['reset_code']); // On supprime le code pour qu'il ne soit pas réutilisé
        } else {
            $error_message = 'Code incorrect. Veuillez réessayer.';
        }
    } else {
        $error_message = 'Le code de réinitialisation a expiré. Veuillez refaire une demande.';
        unset($_SESSION['reset_code'], $_SESSION['reset_email'], $_SESSION['reset_expiry']);
    }
}

// -- PARTIE 2 : Mise à jour du mot de passe (après vérification du code) --
if (isset($_POST['update_password'])) {
    // On vérifie que l'utilisateur a bien été vérifié juste avant
    if (!isset($_SESSION['reset_verified']) || !$_SESSION['reset_verified']) {
        die("Accès non autorisé.");
    }

    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($password) || $password !== $password_confirm) {
        $error_message = 'Les mots de passe sont vides ou ne correspondent pas.';
    } else {
        $user = $userModel->findUserByEmail($emailForReset);
        if ($user && $userModel->updatePasswordById($user['id'], $password)) { // Assure-toi que la fonction updatePasswordById existe dans ton UserModel
            // Tout est bon, on nettoie la session et on redirige vers le login
            unset($_SESSION['reset_verified'], $_SESSION['reset_email'], $_SESSION['reset_expiry']);
            SessionManager::set('success_login_message', 'Mot de passe réinitialisé avec succès !');
            header('Location: ?page=login');
            exit();
        } else {
            $error_message = 'Une erreur est survenue lors de la mise à jour.';
        }
    }
}


// À la fin, le contrôleur inclut la vue pour afficher le résultat.
require_once __DIR__ . '/../views/verify_code.php';
?>