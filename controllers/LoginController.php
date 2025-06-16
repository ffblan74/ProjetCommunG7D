<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure le modèle et la configuration de la base de données
require 'models/UserModel.php';
require_once 'models/Database.php';

// Initialiser un message d'erreur vide
$error_message = '';

// Gestion du formulaire après soumission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (!empty($username) && !empty($password)) {
        try {
            // Connexion à la base de données
            $pdo = Database::getInstance()->getConnection();
            
            // Debug: Vérifions la connexion à la base de données
            error_log("Tentative de connexion pour l'email: " . $username);
            
            // Rechercher l'utilisateur
            $userModel = new UserModel();
            $user = $userModel->findUserByEmail($username);
            
            // Debug: Vérifions le résultat de la recherche
            error_log("Résultat de la recherche: " . print_r($user, true));

            if ($user) {
                error_log("Utilisateur trouvé, vérification du mot de passe");
                if (password_verify($password, $user['mot_de_passe'])) {
                    // Debug avant de définir les variables de session
                    error_log("ID de l'utilisateur avant stockage: " . $user['user_id']);

                    $_SESSION['logged_in'] = true;
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['nom'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['email'] = $username;
                    
                    // Debug après avoir défini les variables de session
                    error_log("Variables de session après connexion:");
                    error_log("logged_in: " . $_SESSION['logged_in']);
                    error_log("username: " . $_SESSION['username']);
                    error_log("user_id: " . $_SESSION['user_id']);
                    error_log("role: " . $_SESSION['role']);
                    
                    // Redirection vers la page d'accueil après connexion
                    header('Location: ?page=home');
                    exit;
                } else {
                    $error_message = "Le mot de passe est incorrect.";
                    error_log("Échec de la vérification du mot de passe");
                }
            } else {
                $error_message = "L'email n'est pas enregistré.";
                error_log("Aucun utilisateur trouvé avec cet email");
            }
        } catch (PDOException $e) {
            $error_message = "Erreur lors de la connexion à la base de données";
            error_log("Erreur PDO: " . $e->getMessage());
        }
    } else {
        $error_message = "Veuillez remplir tous les champs.";
    }
}

// Charger la vue
require 'views/login.php';
?>
