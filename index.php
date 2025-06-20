<?php
session_start();

// Inclure les fichiers nécessaires
require_once 'config.php';
require_once 'models/Database.php';

// Créer une instance de la connexion à la base de données et récupérer l'objet PDO
$pdo = Database::getInstance()->getConnection();


// Récupérer la page demandée
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Routage des pages
switch ($page) {
    case 'Home':
        require 'controllers/HomeController.php';
        $homeController = new HomeController();
        $homeController->handleRequest($pdo);
        break;

    case 'login':
        require 'controllers/LoginController.php';
        break;


    case 'signin':
        require 'controllers/SignupController.php';
        break;

    case 'password_forgotten':
        require 'controllers/PasswordForgottenController.php';
        break;

    case 'statistiques':
        require 'controllers/StatisticsController.php';
        $statisticsController = new StatisticsController($pdo);
        $statisticsController->handleRequest();
        break;
    case 'controlRoom':
        require 'controllers/ControlRoomController.php';
        $controlRoomController = new ControlRoomController();
        $controlRoomController->handleRequest();
        break;

    case 'accessibilite':
        require 'controllers/AccessibiliteController.php';
        break;

  
    
    case 'conditions_utilisation':
        require 'controllers/ConditionsUtilisationController.php';
        break;



    case 'event_details':
        require 'controllers/EventDetailsController.php';
        $eventDetailsController = new EventDetailsController();
        $eventDetailsController->handleRequest();
        break;

    case 'mentions_legales': 
        require 'controllers/MentionsLegalesController.php';
        break;

    case 'logout':  
        require 'controllers/LogoutController.php';  
        break;
        
  

 
    case 'verify_code':
        require 'controllers/VerifyCodeController.php';
        break;


  


    case 'statistiques':
        require 'controllers/StatistiquesController.php';
        break;

    case 'dashboard':
        require 'controllers/DashboardController.php';
        break;

    case 'eco':
        require 'controllers/EcoController.php';
        break;   

    default:
        require 'views/common/404.php';
        break;
}
?>
