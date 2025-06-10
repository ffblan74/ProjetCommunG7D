<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure le modèle et la configuration de la base de données
require_once 'models/UserModel.php';
require_once 'models/Database.php';
require_once __DIR__ . '/../config.php';

$error = '';
$success = '';
$showPopup = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $userModel = new UserModel();

        // Récupérer et valider les données du formulaire
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $role = $_POST['role'] ?? 'participant'; // Par défaut, le rôle est participant

        // Validation des champs
        if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
            throw new Exception("Tous les champs sont obligatoires.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("L'adresse email n'est pas valide.");
        }

        if ($password !== $confirmPassword) {
            throw new Exception("Les mots de passe ne correspondent pas.");
        }

        if (strlen($password) < 8) {
            throw new Exception("Le mot de passe doit contenir au moins 8 caractères.");
        }

        // Vérifier si l'email existe déjà
        if ($userModel->emailExists($email)) {
            throw new Exception("Cette adresse email est déjà utilisée.");
        }

        // Créer l'utilisateur
        $userId = $userModel->createUser([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role
        ]);

        if ($userId) {
            $success = "Compte créé avec succès !";
            $showPopup = true;
        } else {
            throw new Exception("Erreur lors de la création du compte.");
        }

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Charger la vue
require_once BASE_PATH . '/views/signin.php';
?>
