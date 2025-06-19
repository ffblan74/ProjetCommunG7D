<?php
require_once 'models/Database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM accessibilite WHERE id = 1");
    $stmt->execute();
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$settings) {
        $settings = [
            'high_contrast' => 0,
            'text_size' => 'medium',
            'text_spacing' => 0,
            'disable_animations' => 0
        ];
    }
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    $settings = [
        'high_contrast' => 0,
        'text_size' => 'medium',
        'disable_animations' => 0
    ];
}

$bodyClass = '';
if ($settings['high_contrast']) {
    $bodyClass .= 'high-contrast ';
}
if ($settings['text_size']) {
    $bodyClass .= 'text-size-' . $settings['text_size'] . ' ';
}
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
<body class="<?php echo trim($bodyClass); ?>">
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
                    <input type="checkbox" id="highContrast" <?php echo $settings['high_contrast'] ? 'checked' : ''; ?>>
                    <span class="slider"></span>
                </label>
            </div>
            
            <div class="accessibilite-option">
                <div>
                    <div class="option-title">Taille du texte</div>
                    <div class="option-description">Ajustez la taille du texte à votre préférence</div>
                </div>
                <div class="text-size-controls">
                    <button class="text-size-btn <?php echo $settings['text_size'] === 'small' ? 'active' : ''; ?>" data-size="small">Petit</button>
                    <button class="text-size-btn <?php echo $settings['text_size'] === 'medium' ? 'active' : ''; ?>" data-size="medium">Moyen</button>
                    <button class="text-size-btn <?php echo $settings['text_size'] === 'large' ? 'active' : ''; ?>" data-size="large">Grand</button>
                </div>
            </div>
            
            <div class="accessibilite-option">
                <div>
                    <div class="option-title">Désactiver les animations</div>
                    <div class="option-description">Supprime toutes les animations et transitions</div>
                </div>
                <label class="switch">
                    <input type="checkbox" id="disableAnimations" <?php echo $settings['disable_animations'] ? 'checked' : ''; ?>>
                    <span class="slider"></span>
                </label>
            </div>
        </div>
    </main>

    <script>
    function saveSetting(setting, value) {
        fetch('controllers/save_accessibility.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `setting=${setting}&value=${value}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status !== 'success') {
                console.error('Failed to save setting:', data.message);
            } else {
                applyAccessibilitySettings();
            }
        });
    }

    function applyAccessibilitySettings() {
        if (document.getElementById('highContrast').checked) {
            document.body.classList.add('high-contrast');
        } else {
            document.body.classList.remove('high-contrast');
        }
    }

    document.getElementById('highContrast').addEventListener('change', function() {
        saveSetting('high_contrast', this.checked ? 1 : 0);
    });

    document.querySelectorAll('.text-size-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const size = this.getAttribute('data-size');
            saveSetting('text_size', size);
            document.querySelectorAll('.text-size-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    document.getElementById('disableAnimations').addEventListener('change', function() {
        saveSetting('disable_animations', this.checked ? 1 : 0);
    });

    applyAccessibilitySettings();
    </script>

    <?php include 'views/common/footer.php'; ?>
</body>
</html>