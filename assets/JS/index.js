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
            document.querySelector('.stats-container .stat:nth-child(1) .count').textContent = data.capteurs;
            document.querySelector('.stats-container .stat:nth-child(2) .count').textContent = data.utilisateurs;
            document.querySelector('.stats-container .stat:nth-child(3) .count').textContent = data.support;


            // Optionnel : Ajouter une animation de comptage
            animateCounters();
        })
        .catch(error => console.error('Erreur lors de la récupération des statistiques :', error));
});

// Fonction pour animer les compteurs
function animateCounters() {
    const counters = document.querySelectorAll('.count');
    counters.forEach(counter => {
        const target = +counter.textContent;
        const speed = 200;

        const updateCount = () => {
            const current = +counter.textContent;
            const increment = target / speed;

            if (current < target) {
                counter.textContent = Math.ceil(current + increment);
                setTimeout(updateCount, 10);
            } else {
                counter.textContent = target;
            }
        };

        updateCount();
    });
}

// Nouveau carrousel moderne avec défilement infini
class InfiniteCarousel {
    constructor(element) {
        this.carousel = element;
        this.items = Array.from(this.carousel.children);
        this.currentPosition = 0;
        this.isAnimating = false;
        
        if (this.items.length === 0) return;
        
        this.setupCarousel();
        this.createControls();
        this.setupStyles();
        this.bindEvents();
        this.startAutoplay();
        this.updateCarousel();
    }

    setupCarousel() {
        // Créer le wrapper principal
        this.wrapper = document.createElement('div');
        this.wrapper.className = 'carousel-container';
        this.carousel.parentNode.insertBefore(this.wrapper, this.carousel);
        this.wrapper.appendChild(this.carousel);

        // Cloner les éléments pour l'effet infini
        const itemsToClone = 2;
        for (let i = 0; i < itemsToClone; i++) {
            this.items.forEach(item => {
                const clone = item.cloneNode(true);
                clone.setAttribute('aria-hidden', 'true');
                this.carousel.appendChild(clone);
            });
        }
    }

    setupStyles() {
        const style = document.createElement('style');
        style.textContent = `
            .carousel-container {
                position: relative;
                overflow: hidden;
                padding: 40px;
                margin: 0 auto;
                max-width: 1400px;
                background: rgba(255, 255, 255, 0.05);
                border-radius: 20px;
                backdrop-filter: blur(10px);
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            }

            .carousel {
                display: flex;
                gap: 30px;
                transition: transform 0.8s cubic-bezier(0.45, 0, 0.25, 1);
                will-change: transform;
            }

            .carousel-item {
                flex: 0 0 calc(33.333% - 20px);
                min-width: calc(33.333% - 20px);
                position: relative;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
                transform: scale(0.9);
                transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
                background: white;
            }

            .carousel-item.active {
                transform: scale(1);
                box-shadow: 0 15px 40px rgba(66, 151, 93, 0.2);
            }

            .carousel-item img {
                width: 100%;
                height: 200px;
                object-fit: cover;
                border-radius: 20px 20px 0 0;
                transition: transform 0.6s ease;
            }

            .carousel-item:hover img {
                transform: scale(1.05);
            }

            .carousel-item-content {
                padding: 20px;
                background: white;
            }

            .carousel-item h3 {
                margin: 0 0 10px 0;
                font-size: 1.2rem;
                color: #333;
            }

            .carousel-item p {
                margin: 0;
                font-size: 0.9rem;
                color: #666;
                line-height: 1.5;
            }

            .carousel-controls {
                position: absolute;
                top: 50%;
                left: 0;
                right: 0;
                transform: translateY(-50%);
                display: flex;
                justify-content: space-between;
                padding: 0 20px;
                pointer-events: none;
            }

            .carousel-button {
                width: 50px;
                height: 50px;
                border: none;
                border-radius: 50%;
                background: #42975D;
                color: white;
                font-size: 1.5rem;
                cursor: pointer;
                pointer-events: auto;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
                box-shadow: 0 4px 15px rgba(66, 151, 93, 0.3);
            }

            .carousel-button:hover {
                background: #356949;
                transform: scale(1.1);
                box-shadow: 0 6px 20px rgba(66, 151, 93, 0.4);
            }

            .carousel-dots {
                display: flex;
                justify-content: center;
                gap: 10px;
                margin-top: 20px;
            }

            .carousel-dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background: #ddd;
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
                border: 2px solid transparent;
            }

            .carousel-dot.active {
                background: #42975D;
                transform: scale(1.3);
                border-color: white;
            }

            .btn-details {
                display: inline-block;
                margin-top: 15px;
                padding: 8px 20px;
                background: #42975D;
                color: white;
                text-decoration: none;
                border-radius: 25px;
                font-size: 0.9rem;
                transition: all 0.3s ease;
                border: 2px solid transparent;
            }

            .btn-details:hover {
                background: white;
                color: #42975D;
                border-color: #42975D;
            }

            @media (max-width: 768px) {
                .carousel-item {
                    flex: 0 0 calc(100% - 40px);
                    min-width: calc(100% - 40px);
                }
                
                .carousel-container {
                    padding: 20px;
                }
            }
        `;
        document.head.appendChild(style);
    }

    createControls() {
        // Boutons de navigation
        const controls = document.createElement('div');
        controls.className = 'carousel-controls';
        controls.innerHTML = `
            <button class="carousel-button prev" aria-label="Précédent">❮</button>
            <button class="carousel-button next" aria-label="Suivant">❯</button>
        `;
        this.wrapper.appendChild(controls);

        // Points de navigation
        const dots = document.createElement('div');
        dots.className = 'carousel-dots';
        this.items.forEach((_, i) => {
            const dot = document.createElement('button');
            dot.className = 'carousel-dot';
            dot.setAttribute('aria-label', `Aller à la diapositive ${i + 1}`);
            dot.addEventListener('click', () => this.goToSlide(i));
            dots.appendChild(dot);
        });
        this.wrapper.appendChild(dots);
    }

    bindEvents() {
        // Navigation
        this.wrapper.querySelector('.prev').addEventListener('click', () => this.prev());
        this.wrapper.querySelector('.next').addEventListener('click', () => this.next());

        // Pause au survol
        this.wrapper.addEventListener('mouseenter', () => this.stopAutoplay());
        this.wrapper.addEventListener('mouseleave', () => this.startAutoplay());

        // Gestion tactile
        let touchStartX = 0;
        let touchEndX = 0;

        this.carousel.addEventListener('touchstart', e => {
            touchStartX = e.touches[0].clientX;
            this.stopAutoplay();
        });

        this.carousel.addEventListener('touchmove', e => {
            touchEndX = e.touches[0].clientX;
            const diff = touchStartX - touchEndX;
            if (Math.abs(diff) > 5) {
                e.preventDefault();
            }
        });

        this.carousel.addEventListener('touchend', () => {
            const diff = touchStartX - touchEndX;
            if (Math.abs(diff) > 50) {
                if (diff > 0) this.next();
                else this.prev();
            }
            this.startAutoplay();
        });
    }

    updateCarousel() {
        const slideWidth = this.carousel.querySelector('.carousel-item').offsetWidth;
        const gap = 30; // Correspond au gap défini dans le CSS
        const offset = -(this.currentPosition * (slideWidth + gap));
        
        this.carousel.style.transform = `translateX(${offset}px)`;

        // Mise à jour des points
        const dots = this.wrapper.querySelectorAll('.carousel-dot');
        const activeIndex = this.currentPosition % this.items.length;
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === activeIndex);
        });

        // Mise à jour des éléments actifs
        const allItems = this.carousel.querySelectorAll('.carousel-item');
        allItems.forEach((item, i) => {
            const isActive = i === this.currentPosition + 1;
            item.classList.toggle('active', isActive);
        });
    }

    next() {
        if (this.isAnimating) return;
        this.isAnimating = true;
        this.currentPosition++;
        this.updateCarousel();

        setTimeout(() => {
            if (this.currentPosition >= this.items.length * 2) {
                this.carousel.style.transition = 'none';
                this.currentPosition = 0;
                this.updateCarousel();
                setTimeout(() => {
                    this.carousel.style.transition = '';
                }, 50);
            }
            this.isAnimating = false;
        }, 800);
    }

    prev() {
        if (this.isAnimating) return;
        this.isAnimating = true;
        this.currentPosition--;
        this.updateCarousel();

        setTimeout(() => {
            if (this.currentPosition < 0) {
                this.carousel.style.transition = 'none';
                this.currentPosition = this.items.length - 1;
                this.updateCarousel();
                setTimeout(() => {
                    this.carousel.style.transition = '';
                }, 50);
            }
            this.isAnimating = false;
        }, 800);
    }

    goToSlide(index) {
        if (this.isAnimating) return;
        this.currentPosition = index;
        this.updateCarousel();
    }

    startAutoplay() {
        if (this.autoplayInterval) return;
        this.autoplayInterval = setInterval(() => this.next(), 5000);
    }

    stopAutoplay() {
        if (this.autoplayInterval) {
            clearInterval(this.autoplayInterval);
            this.autoplayInterval = null;
        }
    }
}

// Initialisation du carrousel
document.addEventListener('DOMContentLoaded', function() {
    const carouselElement = document.querySelector('.carousel');
    if (carouselElement && carouselElement.children.length > 0) {
        new InfiniteCarousel(carouselElement);
    }
});
