<!DOCTYPE html>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À propos</title>
    <link rel="stylesheet" href="/src/assets/CSS/about.css">
    <link rel="stylesheet" href="/src/assets/CSS/footer.css">
    <link rel="stylesheet" href="/src/assets/CSS/header.css">
    <script src="/src/assets/JS/about.js" defer></script>
    <link rel="icon" type="image/x-icon" href="/src/assets/favicon.jpg">
</head>
<body>

<!-- Header -->
<?php include 'common/header.php'; ?> 

<!-- Section Héroïque -->
<section class="hero-section">
    <h1>À propos de nous</h1>
    <p>Découvrez l'histoire et les valeurs qui animent notre projet.</p>
</section>

<!-- Section À propos -->
<section class="about-section">
    <h2>Notre Mission</h2>
    <p>Chez Plan It, nous croyons que chaque moment partagé a le potentiel de devenir un souvenir inoubliable. Notre mission est simple mais ambitieuse : connecter les gens grâce à des événements uniques, adaptés à tous les goûts et à toutes les passions.</p>
    <p>Plan It simplifie la recherche, l'organisation et la participation à des événements, avec un objectif : créer une plateforme conviviale et inclusive pour tous.</p>
</section>

<!-- Section Comment ça fonctionne -->
<section class="how-it-works">
    <h2>Comment ça fonctionne ?</h2>
    <div class="steps">
        <div class="step">
            <h3>1. Créez votre compte</h3>
            <p>Inscrivez-vous gratuitement en quelques clics et accédez à un calendrier d'événements passionnants.</p>
        </div>
        <div class="step">
            <h3>2. Trouvez un événement</h3>
            <p>Utilisez nos filtres pour découvrir des activités qui correspondent à vos goûts et à votre localisation.</p>
        </div>
        <div class="step">
            <h3>3. Participez et partagez</h3>
            <p>Rejoignez des événements, faites de nouvelles rencontres et partagez vos expériences avec la communauté.</p>
        </div>
    </div>
</section>

<!-- Section Valeurs -->
<section class="values-section">
    <h2>Nos Valeurs</h2>
    <ul class="values-list">
        <li><strong>Convivialité :</strong> Créer des liens humains grâce à des événements accessibles à tous.</li>
        <li><strong>Accessibilité :</strong> Une plateforme intuitive et adaptée aux besoins de chacun.</li>
        <li><strong>Responsabilité :</strong> Favoriser des événements respectueux et durables.</li>
    </ul>
</section>

<!-- Section Équipe -->
<section class="team-section">
    <h2>Rencontrez notre équipe</h2>
    <div class="team-members">
        <!-- Chefs de projet en haut -->
        <div class="member">
            <h3>Sofiane Addarazi</h3>
            <p>Chef de Projet-<br>Développeur Fullstack</p>
        </div>
        <div class="member">
            <h3>Pablo Rubio-Aguayo</h3>
            <p>Chef de projet-<br>Développeur Frontend</p>
        </div>
        <!-- Autres membres en dessous -->
        <div class="member">
            <h3>Alexandre Poulain</h3>
            <p>Développeur Frontend</p>
        </div>
        <div class="member">
            <h3>Matthew Le Bouffos</h3>
            <p>Développeur Frontend</p>
        </div>
        <div class="member">
            <h3>Fabrice Lin</h3>
            <p>Développeur Frontend</p>
        </div>
        <div class="member">
            <h3>Taoufik Amrani</h3>
            <p>Développeur Frontend</p>
        </div>
    </div>
</section>

<!-- Section Avis Client -->
<section class="testimonial-section">
    <h2>Ce que disent nos utilisateurs</h2>
    <div class="testimonial-slider">
        <button class="slider-button prev-button">&#8249;</button>
        <div class="testimonial">
            <p>"J'ai organisé mon anniversaire surprise grâce à Plan It. L'interface est super intuitive et j'ai pu gérer facilement la liste des invités. Mes amis ont adoré l'expérience !"</p>
            <p class="client-name">— Thomas Martin, Paris</p>
        </div>
        <div class="testimonial">
            <p>"En tant qu'organisateur régulier d'événements sportifs, Plan It me fait gagner un temps précieux. La gestion des inscriptions et des paiements est vraiment simplifiée."</p>
            <p class="client-name">— Sarah Dubois, Lyon</p>
        </div>
        <div class="testimonial">
            <p>"J'ai découvert des activités passionnantes près de chez moi que je n'aurais jamais trouvées autrement. La communauté est super active et bienveillante !"</p>
            <p class="client-name">— Lucas Bernard, Marseille</p>
        </div>
        <div class="testimonial">
            <p>"Grâce à Plan It, j'ai pu organiser un workshop photo qui a réuni des passionnés de tous niveaux. La plateforme a rendu l'organisation très fluide."</p>
            <p class="client-name">— Emma Laurent, Bordeaux</p>
        </div>
        <div class="testimonial">
            <p>"Je suis nouveau dans ma ville et Plan It m'a permis de rencontrer des gens qui partagent mes centres d'intérêt. J'ai déjà participé à plusieurs soirées jeux de société !"</p>
            <p class="client-name">— Antoine Moreau, Nantes</p>
        </div>
        <button class="slider-button next-button">&#8250;</button>
    </div>
</section>

<!-- Section Appel à l'action (visible uniquement si non connecté) -->
<?php if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) : ?>
    <section class="cta-section">
        <h2>Rejoignez-nous dès aujourd'hui !</h2>
        <p>Inscrivez-vous gratuitement et commencez à explorer des événements passionnants autour de vous.</p>
        <a href="index.php?page=signin" class="cta-button">S'inscrire maintenant</a>
    </section>
<?php endif; ?>

<!-- Footer -->
<?php include 'common/footer.php'; ?>

</body>
</html>
