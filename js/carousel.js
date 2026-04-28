let slideIndex = 0;
const slides = document.querySelectorAll('.slide');
const totalSlides = slides.length;
const nextBtn = document.querySelector('.next');
const prevBtn = document.querySelector('.prev');

let autoSlideInterval = 7000; // Intervalle du carrousel automatique
let autoSlide = null; // Stocke l'intervalle
let pauseTimeout = null; // Stocke le timeout de pause

function showSlide(newIndex, direction = 1) {
    const currentSlide = slides[slideIndex];
    const nextSlide = slides[newIndex];

    slides.forEach(slide => slide.classList.remove('active', 'prev-slide'));

    if(direction === 1) { // Suivant
        currentSlide.classList.add('prev-slide');
        nextSlide.style.left = '100%';
    } else { // Précédent
        currentSlide.classList.add('prev-slide');
        nextSlide.style.left = '-100%';
    }

    void nextSlide.offsetWidth; // Force le navigateur à appliquer le style

    nextSlide.classList.add('active');
    nextSlide.style.left = '0';

    slideIndex = newIndex;
}

// Fonction pour démarrer le carrousel automatique
function startAutoSlide() {
    autoSlide = setInterval(() => {
        const newIndex = (slideIndex + 1) % totalSlides;
        showSlide(newIndex, 1);
    }, autoSlideInterval);
}

// Fonction pour arrêter temporairement le carrousel
function pauseAutoSlide() {
    clearInterval(autoSlide); // Stop le slide automatique
    clearTimeout(pauseTimeout); // Nettoie tout timeout précédent

    // Relance le carrousel après 10 secondes
    pauseTimeout = setTimeout(() => {
        startAutoSlide();
    }, 10000);
}

// Navigation manuelle
nextBtn.addEventListener('click', () => {
    const newIndex = (slideIndex + 1) % totalSlides;
    showSlide(newIndex, 1);
    pauseAutoSlide(); // Stop et pause 10s
});

prevBtn.addEventListener('click', () => {
    const newIndex = (slideIndex - 1 + totalSlides) % totalSlides;
    showSlide(newIndex, -1);
    pauseAutoSlide(); // Stop et pause 10s
});

// Démarrage initial du carrousel
startAutoSlide();

