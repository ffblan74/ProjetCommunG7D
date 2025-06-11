<?php
// Inclure la configuration
require_once __DIR__ . '/../config.php';

// Définir les questions et réponses
$questionsReponses = [
    [
        "question" => "Puis-je organiser un petit évènement sans profit ?",
        "reponse" => "Oui, biensûr il suffit juste de décocher la case rémuneration."
    ],
    [
        "question" => "Puis-je annuler ma participation à un évènement ?",
        "reponse" => "Oui, vous pouvez annuler votre participation directement depuis l'onglet \"Mes réservations\" avant la date de l'évènement."
    ],
    [
        "question" => "Puis-je contacter un organisateur d'évènement ?",
        "reponse" => "Vous pouvez contacter un organisateur d'évènement directement via la messagerie interne de la plateforme."
    ],
    [
        "question" => "Il est donc plus facile d'écrire des avis maintenant, n'est-ce pas ?",
        "reponse" => "Comment le savez-vous ? Il y a maintenant plus de pages avec des appels à l'action pour que vous puissiez poster vos propres critiques d'hôtels, de restaurants et d'attractions."
    ]
];

// Inclure la vue
require_once __DIR__ . '/../views/faq.php';
?>
