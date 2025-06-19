<?php
// Plus de base de données ici, on laisse JS gérer
$bodyClass = '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres d'accessibilité</title>
    <link rel="stylesheet" href="assets/CSS/accessibilite.css">
    <link rel="stylesheet" href="assets/CSS/footer.css">
    <link rel="stylesheet" href="assets/CSS/header.css">
    <link rel="icon" type="image/jpg" href="assets/images/favicon.jpg">
</head>
<body>
<?php include 'views/common/header.php'; ?>

<main class="page">
    <div class="accessibilite-container">
        <h2>⚙️ Paramètres <span class="texte-orange">d'accessibilité</span></h2>

        <div class="accessibilite-option">
            <div>
                <div class="option-title">Mode contraste élevé</div>
                <div class="option-description">Augmente le contraste des couleurs pour une meilleure lisibilité</div>
            </div>
            <label class="switch">
                <input type="checkbox" id="highContrast">
                <span class="slider"></span>
            </label>
        </div>

        <div class="accessibilite-option">
            <div>
                <div class="option-title">Taille du texte</div>
                <div class="option-description">Ajustez la taille du texte à votre préférence</div>
            </div>
            <div class="text-size-controls">
                <button class="text-size-btn" data-size="small">Petit</button>
                <button class="text-size-btn" data-size="medium">Moyen</button>
                <button class="text-size-btn" data-size="large">Grand</button>
            </div>
        </div>

        <div class="accessibilite-option">
            <div>
                <div class="option-title">Désactiver les animations</div>
                <div class="option-description">Supprime toutes les animations et transitions</div>
            </div>
            <label class="switch">
                <input type="checkbox" id="disableAnimations">
                <span class="slider"></span>
            </label>
        </div>
    </div>
</main>

<script>
    // Cookie helpers
    function setCookie(name, value, days = 365) {
        const expires = new Date(Date.now() + days * 864e5).toUTCString();
        document.cookie = `${name}=${value}; expires=${expires}; path=/`;
    }

    function getCookie(name) {
        return document.cookie.split('; ').find(row => row.startsWith(name + '='))?.split('=')[1];
    }

    function applyAccessibilitySettings() {
        const highContrast = getCookie('high_contrast') === '1';
        const textSize = getCookie('text_size') || 'medium';
        const disableAnimations = getCookie('disable_animations') === '1';

        document.body.classList.toggle('high-contrast', highContrast);
        document.body.classList.remove('text-size-small', 'text-size-medium', 'text-size-large');
        document.body.classList.add('text-size-' + textSize);

        if (disableAnimations) {
            document.body.classList.add('disable-animations');
        } else {
            document.body.classList.remove('disable-animations');
        }

        // Appliquer aux éléments d’interface
        document.getElementById('highContrast').checked = highContrast;
        document.getElementById('disableAnimations').checked = disableAnimations;

        document.querySelectorAll('.text-size-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.size === textSize);
        });
    }

    // Events
    document.getElementById('highContrast').addEventListener('change', function() {
        setCookie('high_contrast', this.checked ? '1' : '0');
        applyAccessibilitySettings();
    });

    document.getElementById('disableAnimations').addEventListener('change', function() {
        setCookie('disable_animations', this.checked ? '1' : '0');
        applyAccessibilitySettings();
    });

    document.querySelectorAll('.text-size-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const size = this.dataset.size;
            setCookie('text_size', size);
            applyAccessibilitySettings();
        });
    });

    // Initialisation
    applyAccessibilitySettings();
</script>

<?php include 'views/common/footer.php'; ?>
</body>
</html>
