document.addEventListener('DOMContentLoaded', function() {
  const animateSingleCounter = (counter) => {
    const target = +counter.parentElement.getAttribute('data-target');
    const speed = 200;

    const updateCount = () => {
      const count = +counter.innerText;
      const increment = target / speed;

      if (count < target) {
        counter.innerText = Math.ceil(count + increment);
        setTimeout(updateCount, 5);
      } else {
        counter.innerText = target;
      }
    };
    updateCount();
  };

  const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const counterElement = entry.target.querySelector('.count');
        if (counterElement) {
          animateSingleCounter(counterElement);
        }
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.5 });

  document.querySelectorAll('.stat-card').forEach(card => {
    if (card.querySelector('.count')) {
      observer.observe(card);
    }
  });
});

// Gestion des statistiques
document.addEventListener('DOMContentLoaded', () => {
    // Récupérer les statistiques via une requête AJAX
    fetch('controllers/getstats.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Erreur :', data.error);
                return;
            }

            // Mettre à jour les valeurs des compteurs
            document.querySelector('#stat-capteurs .count').textContent = data.capteurs;
            document.querySelector('#stat-utilisateurs .count').textContent = data.utilisateurs;
            document.querySelector('#stat-support .count').textContent = data.support;


            // Optionnel : Ajouter une animation de comptage
            animateCounters();
        })
        .catch(error => console.error('Erreur lors de la récupération des statistiques :', error));
});