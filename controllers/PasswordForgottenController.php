<?php
// controllers/PasswordForgottenController.php

// Inclusions des fichiers nécessaires
require_once __DIR__ . '/../utils/SessionManager.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../config.php';

// Inclusions pour PHPMailer (chemins corrigés pour être dans un sous-dossier)
require_once __DIR__ . '/../phpmailer/Exception.php';
require_once __DIR__ . '/../phpmailer/PHPMailer.php';
require_once __DIR__ . '/../phpmailer/SMTP.php';

// Importation des classes PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

SessionManager::startSession();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_formulaire = $_POST['mail'] ?? '';
    
    if (!empty($email_formulaire)) {
        $userModel = new UserModel();
        $user = $userModel->findUserByEmail($email_formulaire);

        if ($user) {
            if (isset($_SESSION['reset_code'])) {
                unset($_SESSION['reset_verified']);
                unset($_SESSION['reset_code']);
                unset($_SESSION['reset_expiry']);
            }
            // 1. Générer le code à 5 chiffres et le stocker en session
            $resetCode = random_int(10000, 99999);
            $_SESSION['reset_code'] = $resetCode;
            $_SESSION['reset_email'] = $user['email'];
            $_SESSION['reset_expiry'] = time() + 600; // Le code expire dans 10 minutes

            // 2. On initialise PHPMailer
            $mail = new PHPMailer(true);

            try {
                // ** ON UTILISE LA CONFIGURATION EXACTE DE TON ContactController **
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'contact.planit1@gmail.com'; // Ton e-mail qui fonctionne
                $mail->Password = 'tafo lpbl apix hosh';     // Ton mot de passe d'application qui fonctionne
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Destinataires et contenu
                $mail->setFrom('contact.planit1@gmail.com', 'Light Control');
                $mail->addAddress($user['email'], $user['nom']); // On envoie à l'utilisateur
                
                $mail->isHTML(false);
                $mail->Subject = 'Votre code de réinitialisation de mot de passe';
                $mail->Body = "Bonjour,\n\nVotre code de vérification est : " . $resetCode . "\n\nCe code expirera dans 10 minutes.";

                // 3. On envoie l'e-mail
                $mail->send();

                // 4. On redirige vers la page de vérification
                header('Location: ?page=verify_code');

            } catch (Exception $e) {
                // En cas d'erreur d'envoi, on met le message en session et on redirige
                SessionManager::set('error_message', "Erreur lors de l'envoi de l'e-mail. Veuillez réessayer. Erreur: {$mail->ErrorInfo}");
                require_once __DIR__ . '/../views/password_forgotten.php';
            }
        } else {
            SessionManager::set('error_message', 'Aucun utilisateur trouvé avec cette adresse email.');
        }
    } else {
        SessionManager::set('error_message', 'Veuillez entrer une adresse email.');
    }
    // S'il y a une erreur, on redirige vers la page de départ pour l'afficher
    require_once __DIR__ . '/../views/password_forgotten.php';
}

// Si la méthode n'est pas POST, on affiche simplement la vue
require_once __DIR__ . '/../views/password_forgotten.php';
?>