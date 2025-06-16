<?php
session_start();

// Inclure les bibliothèques nécessaires
require 'config.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialiser le feedback pour éviter les erreurs
$feedback = '';

// Vérification du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Valider les champs
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $nom = htmlspecialchars(trim($_POST['nom']));
    $email = filter_var(trim($_POST['mail']), FILTER_VALIDATE_EMAIL);
    $sujet = htmlspecialchars(trim($_POST['subject']));
    $contenuMessage = htmlspecialchars(trim($_POST['message']));

    if ($prenom && $nom && $email && $sujet && $contenuMessage) {
        $mail = new PHPMailer(true);

        try {
            // Configuration de l'envoi SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'contact.planit1@gmail.com';
            $mail->Password = 'tafo lpbl apix hosh';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Destinataires
            $mail->setFrom('contact.planit1@gmail.com', 'Plan-It');
            $mail->addAddress('contact.planit1@gmail.com');
            $mail->addReplyTo($email, "$prenom $nom");

            // Contenu de l'e-mail
            $mail->isHTML(false);
            $mail->Subject = $sujet;
            $mail->Body = "Prénom: $prenom\nNom: $nom\nEmail: $email\nMessage: $contenuMessage";

            // Envoi
            $mail->send();
            $feedback = "<p style='color: green;'>Votre message a été envoyé avec succès !</p>";
        } catch (Exception $e) {
            $feedback = "<p style='color: red;'>Erreur lors de l'envoi : {$mail->ErrorInfo}</p>";
        }
    } else {
        $feedback = "<p style='color: red;'>Veuillez remplir tous les champs correctement.</p>";
    }
}

// Inclure la vue
require 'views/contact.php';
