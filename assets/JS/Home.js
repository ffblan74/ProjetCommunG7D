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


document.addEventListener('DOMContentLoaded', function () {
  function updateFeatureIcons() {
    // Récupération des statistiques depuis l'objet global
    const stats = window.sensorValues || {};

    // Fonction pour changer l’icône en fonction de la statistique
    const changeIcon = (elementId, newIconClass) => {
      const iconElement = document.querySelector(`#${elementId} .feature-icon i`);
      if (iconElement) {

        if (elementId === "state-light") {
          // Pour l'état de la lumière, on garde lightbulb et on change fa-solid/fa-regular
            iconElement.className = `${newIconClass} fa-lightbulb`;
        } else {
          iconElement.className = `fa-solid ${newIconClass}`;
        }
      }
    };

    //  RÈGLES DE CHANGEMENT
    if (stats.temperature < 5) {
      changeIcon("temp", "fa-temperature-empty");
    } else if (stats.temperature < 13) {
      changeIcon("temp", "fa-temperature-quarter");
    } else if (stats.temperature < 21) {
      changeIcon("temp", "fa-temperature-half");
    } else if (stats.temperature < 29) {
      changeIcon("temp", "fa-temperature-three-quarters");
    } else {
      changeIcon("temp", "fa-temperature-half");
    }

    if (stats.humidite < 25) {
      changeIcon("humid", "fa-droplet-slash");
    } else if (stats.humidite > 70) {
      changeIcon("humid", "fa-water");
    } else {
      changeIcon("humid", "fa-droplet");
    }

    if (stats.luminosite < 300) {
      changeIcon("brightn", "fa-cloud-moon");
    } else if (stats.luminosite > 1000) {
      changeIcon("brightn", "fa-sun-bright");
    } else {
      changeIcon("brightn", "fa-sun");
    }

    if (stats.etatLumiere === 0) {
      changeIcon("state-light", "fa-regular");
    } else {
      changeIcon("state-light", "fa-solid");
    }

    if (stats.etatVolets === 0) {
      changeIcon("state-blinds", "fa-door-closed");
    } else {
      changeIcon("state-blinds", "fa-door-open");
    }

    if (stats.weatherTemp > 28) {
      changeIcon("weather", "fa-sun");
    } else if (stats.weatherTemp < 10) {
      changeIcon("weather", "fa-snowflake");
    } else {
      changeIcon("weather", "fa-cloud-sun");
    }
  }

  // Appel immédiat
  updateFeatureIcons();
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
            document.getElementById('compteur-capteurs').textContent = data.capteurs;
            document.getElementById('compteur-utilisateurs').textContent = data.utilisateurs;


            
        })
        .catch(error => console.error('Erreur lors de la récupération des statistiques :', error));
});