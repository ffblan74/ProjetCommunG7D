// Gestion des animations au scroll
const animateOnScroll = () => {
    const elements = document.querySelectorAll('.step, .member');
    const windowHeight = window.innerHeight;

    elements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        if (elementTop < windowHeight - 100) {
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }
    });
};

// Configuration initiale des animations
document.addEventListener('DOMContentLoaded', function() {
    // Animation au scroll pour les sections
    const elements = document.querySelectorAll('.step, .member');
    elements.forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
        element.style.transition = 'all 0.6s ease-out';
    });

    // Slider de témoignages
    const testimonialSlider = document.querySelector('.testimonial-slider');
    const testimonials = document.querySelectorAll('.testimonial');
    const prevButton = document.querySelector('.prev-button');
    const nextButton = document.querySelector('.next-button');
    let currentSlide = 0;
    let isAnimating = false;

    // Créer les points de navigation
    const dotsContainer = document.createElement('div');
    dotsContainer.className = 'slider-dots';
    testimonials.forEach((_, index) => {
        const dot = document.createElement('div');
        dot.className = 'slider-dot' + (index === 0 ? ' active' : '');
        dot.addEventListener('click', () => {
            if (!isAnimating && index !== currentSlide) {
                changeSlide(index > currentSlide ? 'next' : 'prev', index);
            }
        });
        dotsContainer.appendChild(dot);
    });
    testimonialSlider.parentNode.appendChild(dotsContainer);

    // Fonction pour mettre à jour les points
    function updateDots(newIndex) {
        document.querySelectorAll('.slider-dot').forEach((dot, index) => {
            dot.classList.toggle('active', index === newIndex);
        });
    }

    // Fonction pour masquer tous les témoignages
    function resetTestimonials() {
        testimonials.forEach(testimonial => {
            testimonial.classList.remove('active', 'prev');
            testimonial.style.transform = 'translateX(100%)';
        });
    }

    // Fonction pour changer de témoignage
    function changeSlide(direction, targetIndex = null) {
        if (isAnimating) return;
        isAnimating = true;

        const oldSlide = testimonials[currentSlide];
        let newIndex = targetIndex !== null ? targetIndex :
            direction === 'next' 
                ? (currentSlide + 1) % testimonials.length 
                : (currentSlide - 1 + testimonials.length) % testimonials.length;
        
        const newSlide = testimonials[newIndex];

        // Animation de sortie
        oldSlide.style.transform = `translateX(${direction === 'next' ? '-100%' : '100%'})`;
        oldSlide.style.opacity = '0';
        oldSlide.classList.remove('active');

        // Animation d'entrée
        newSlide.style.transform = `translateX(${direction === 'next' ? '100%' : '-100%'})`;
        newSlide.style.opacity = '0';
        newSlide.style.display = 'block';
        
        // Force reflow
        newSlide.offsetHeight;

        newSlide.style.transform = 'translateX(0)';
        newSlide.style.opacity = '1';
        newSlide.classList.add('active');

        currentSlide = newIndex;
        updateDots(currentSlide);

        // Réinitialiser l'état d'animation après la transition
        setTimeout(() => {
            isAnimating = false;
        }, 1000); // Synchronisé avec la durée de transition CSS
    }

    // Initialisation : afficher le premier témoignage
    resetTestimonials();
    testimonials[0].classList.add('active');
    testimonials[0].style.transform = 'translateX(0)';
    testimonials[0].style.opacity = '1';

    // Événements des boutons
    if (prevButton && nextButton) {
        nextButton.addEventListener('click', () => !isAnimating && changeSlide('next'));
        prevButton.addEventListener('click', () => !isAnimating && changeSlide('prev'));
    }
});

// Écouter l'événement de scroll pour les animations
window.addEventListener('scroll', animateOnScroll);
// Déclencher une première fois pour les éléments déjà visibles
animateOnScroll();

document.addEventListener('DOMContentLoaded', () => {
    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        const answer = item.querySelector('.faq-answer');

        question.addEventListener('click', () => {
            const isVisible = answer.style.display === 'block';
            answer.style.display = isVisible ? 'none' : 'block';
        });
    });
});



document.addEventListener('DOMContentLoaded', () => {
    const counters = document.querySelectorAll('.stat-number');

    counters.forEach(counter => {
        const target = +counter.dataset.target;
        const increment = target / 200; // Ajuste la vitesse ici

        const updateCounter = () => {
            const value = +counter.innerText;
            if (value < target) {
                counter.innerText = Math.ceil(value + increment);
                setTimeout(updateCounter, 20);
            } else {
                counter.innerText = target;
            }
        };

        updateCounter();
    });
});

