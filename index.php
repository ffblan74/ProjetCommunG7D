<?php
session_start();

// Inclure les fichiers nécessaires
require_once 'config.php';
require_once 'models/Database.php';

// Récupérer la page demandée
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Routage des pages
switch ($page) {
    case 'home':
        require 'controllers/HomeController.php';
        break;

    case 'login':
        require 'controllers/LoginController.php';
        break;

    case 'admin':
        require 'controllers/AdminController.php';
        $adminController = new AdminController();
        $adminController->handleRequest();
        break;

    case 'signin':
        require 'controllers/SignupController.php';
        break;

    case 'password_forgotten':
        require 'controllers/PasswordForgottenController.php';
        break;

    case 'explorer':
        require 'controllers/ExploreController.php';
        $exploreController = new ExploreController();
        $exploreController->handleRequest();
        break;

    case 'faq':
        require 'controllers/FaqController.php';
        break;

    case 'tags_signin':
        require 'controllers/TagsSigninController.php';
        break;
    
    case 'conditions_utilisation':
        require 'controllers/ConditionsUtilisationController.php';
        break;

    case 'contact':
        require 'controllers/ContactController.php';
        break;

    case 'about':
        require 'controllers/AboutController.php';
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
        
    case 'create_event':  
        require 'controllers/CreateEventController.php';  
        break;

    case 'client_profil':
        require 'controllers/ClientProfileController.php';
        break;

    case 'dashboard':
        require 'controllers/DashboardController.php';
        $dashboardController = new DashboardController();
        $dashboardController->handleRequest();
        break;

    case 'edit_profile':
        require 'controllers/EditProfileController.php';
        break;

    case 'update_settings':
        require 'controllers/UpdateSettingsController.php';
        break;

    case 'event_registration':
        require 'controllers/EventRegistrationController.php';
        $registrationController = new EventRegistrationController();
        $registrationController->handleRequest();
        break;

    default:
        require 'views/common/404.php';
        break;
}
?>
