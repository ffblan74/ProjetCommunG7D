<?php
// Statuts des événements
define('EVENT_STATUS_OPEN', 'ouvert');
define('EVENT_STATUS_PENDING', 'sous_reserve');
define('EVENT_STATUS_CLOSED', 'ferme');

// Statuts des participations
define('PARTICIPATION_STATUS_PENDING', 'en_attente');
define('PARTICIPATION_STATUS_APPROVED', 'approuvé');
define('PARTICIPATION_STATUS_REJECTED', 'refusé');

// Rôles utilisateurs
define('ROLE_ADMIN', 'admin');
define('ROLE_ORGANIZER', 'organisateur');
define('ROLE_PARTICIPANT', 'participant');

// Configuration des chemins
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('PROFILE_PICS_DIR', UPLOAD_DIR . 'profile_pics/');
define('EVENT_PICS_DIR', UPLOAD_DIR . 'event_pics/');

// Limites et contraintes
define('MAX_EVENT_CAPACITY', 1000);
define('MIN_EVENT_CAPACITY', 1);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB 