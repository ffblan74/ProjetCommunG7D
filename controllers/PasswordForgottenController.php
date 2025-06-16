<?php
require_once __DIR__ . '/../utils/SessionManager.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../config.php';

SessionManager::startSession();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    
    if (!empty($username)) {
        // Rechercher l'utilisateur dans la base de données
        $user = findUserByUsername($username);
        
        if ($user) {
            // Générer un token unique
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Sauvegarder le token dans la base de données
            saveResetToken($user['id_utilisateur'], $token, $expiry);
            
            // Envoyer l'email avec le lien de réinitialisation
            $resetLink = "http://localhost/?page=reset_password&token=" . $token;
            $to = $user['email'];
            $subject = "Réinitialisation de votre mot de passe";
            $message = "Bonjour,\n\nPour réinitialiser votre mot de passe, cliquez sur le lien suivant :\n" . $resetLink;
            $headers = "From: noreply@planit.com";
            
            if(mail($to, $subject, $message, $headers)) {
                SessionManager::set('success_message', 'Un email de réinitialisation a été envoyé à votre adresse email.');
            } else {
                SessionManager::set('error_message', 'Erreur lors de l\'envoi de l\'email. Veuillez réessayer.');
            }
        } else {
            SessionManager::set('error_message', 'Aucun utilisateur trouvé avec ce nom d\'utilisateur.');
        }
    } else {
        SessionManager::set('error_message', 'Veuillez entrer un nom d\'utilisateur.');
    }
}

// Afficher la vue
require_once __DIR__ . '/../views/password_forgotten.php';
?>
