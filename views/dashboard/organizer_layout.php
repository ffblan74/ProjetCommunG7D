<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Organisateur</title>
    <link rel="stylesheet" href="/src/assets/CSS/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php require_once 'views/common/header.php'; ?>

<div class="dashboard-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="profile-section">
            <img src="/src/assets/images/profile/<?= htmlspecialchars($userData['photo_profil'] ?? 'default.png') ?>" alt="Photo de profil">
            <h3><?= htmlspecialchars($userData['prenom'] . ' ' . $userData['nom']) ?></h3>
            <span class="role">Organisateur</span>
        </div>
        <nav>
            <ul>
                <li class="<?= $currentPage === 'overview' ? 'active' : '' ?>">
                    <a href="/src/?page=dashboard&view=overview"><i class="fas fa-home"></i> Vue d'ensemble</a>
                </li>
                <li class="<?= $currentPage === 'events' ? 'active' : '' ?>">
                    <a href="/src/?page=dashboard&view=events"><i class="fas fa-calendar-alt"></i> Mes Événements</a>
                </li>
                <li class="<?= $currentPage === 'requests' ? 'active' : '' ?>">
                    <a href="/src/?page=dashboard&view=requests"><i class="fas fa-user-clock"></i> Demandes en attente</a>
                </li>
                <li class="<?= $currentPage === 'profile' ? 'active' : '' ?>">
                    <a href="/src/?page=dashboard&view=profile"><i class="fas fa-user-edit"></i> Modifier le profil</a>
                </li>
                <li>
                    <a href="/src/?page=create_event"><i class="fas fa-plus-circle"></i> Créer un événement</a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <?php include $contentFile; ?>
    </div>
</div>

<script src="/src/assets/js/dashboard.js"></script>
</body>
</html> 