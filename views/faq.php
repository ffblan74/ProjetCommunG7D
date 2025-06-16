<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foire aux questions</title>
    <link rel="stylesheet" href="assets/CSS/faq.css">
    <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/CSS/footer.css">
    <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/CSS/header.css">
    <link rel="icon" type="image/x-icon" href="<?= BASE_PATH ?>/assets/images/favicon.jpg">
</head>
<body>

<?php include BASE_PATH . '/views/common/header.php'; ?> 

<main>
    <div class="container">
        <section class="faq-content">
            <h1>Questions souvent posées</h1>
            <?php foreach ($questionsReponses as $index => $qr): ?>
                <?php $questionId = 'q' . ($index + 1); ?>
                <div class="faq-item">
                    <h2 class="question" onclick="toggleReponse(this, '<?= $questionId ?>')"><?= $qr['question'] ?></h2>
                    <div class="reponse" id="<?= $questionId ?>"><?= $qr['reponse'] ?></div>
                </div>
            <?php endforeach; ?>
        </section>
    </div>
</main>

<script>
    function toggleReponse(questionElement, id) {
        const reponse = document.getElementById(id);
        const allQuestions = document.querySelectorAll('.question');
        const allReponses = document.querySelectorAll('.reponse');

        // Ferme toutes les autres réponses
        allQuestions.forEach(q => {
            if (q !== questionElement) {
                q.classList.remove('active');
            }
        });
        allReponses.forEach(r => {
            if (r.id !== id) {
                r.classList.remove('active');
            }
        });

        // Bascule l'état de la réponse cliquée
        questionElement.classList.toggle('active');
        reponse.classList.toggle('active');
    }

    // Ouvre la première question par défaut
    document.addEventListener('DOMContentLoaded', function() {
        const firstQuestion = document.querySelector('.question');
        const firstReponse = document.querySelector('.reponse');
        if (firstQuestion && firstReponse) {
            firstQuestion.classList.add('active');
            firstReponse.classList.add('active');
        }
    });
</script>

<?php include BASE_PATH . '/views/common/footer.php'; ?> 

</body>
</html>
