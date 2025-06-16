let slideIndex = 0;
const images = <?php echo json_encode(array_column($images, 'image_path')); ?>; // Récupérer les chemins des images depuis PHP
const dots = document.querySelectorAll('.slider-dot');
const slideImage = document.getElementById('slide-image');

function showSlide(n) {
    if (n >= images.length) {
        slideIndex = 0;
    } else if (n < 0) {
        slideIndex = images.length - 1;
    } else {
        slideIndex = n;
    }
    slideImage.src = "../uploads/" + images[slideIndex]; // Mettre à jour l'image affichée
    updateDots();
}

function changeSlide(n) {
    showSlide(slideIndex + n);
}

function currentSlide(n){
    showSlide(n);
}

function updateDots() {
    dots.forEach((dot, index) => {
        if (index === slideIndex) {
            dot.classList.add('active');
        } else {
            dot.classList.remove('active');
        }
    });
}

// Initialisation : Afficher la première image et le premier point actif
showSlide(0);
