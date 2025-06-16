// Ouvrir et fermer le pop-up
const openSettingsButton = document.querySelector('#openSettings');
const popupSettings = document.getElementById('popupSettings');
const closePopupButton = document.querySelector('.close-popup');

const passwordInput = document.getElementById('password');
const confirmPasswordInput = document.getElementById('confirm-password');
const passwordFeedback = document.getElementById('password-feedback');

// Fonction de validation des mots de passe
function validatePasswords() {
  const passwordValue = passwordInput.value;
  const confirmPasswordValue = confirmPasswordInput.value;

  if (confirmPasswordValue.length >= 8) {
    if (passwordValue === confirmPasswordValue) {
      passwordFeedback.textContent = `Mots de passe identiques (${confirmPasswordValue.length} caractères).`;
      passwordFeedback.classList.add('valid');
      passwordFeedback.classList.remove('invalid');
    } else {
      passwordFeedback.textContent = `Les mots de passe ne correspondent pas (${confirmPasswordValue.length} caractères).`;
      passwordFeedback.classList.add('invalid');
      passwordFeedback.classList.remove('valid');
    }
  } else {
    passwordFeedback.textContent = '';
    passwordFeedback.classList.remove('valid', 'invalid');
  }
}

passwordInput.addEventListener('input', validatePasswords);
confirmPasswordInput.addEventListener('input', validatePasswords);

// Ouvrir le pop-up
openSettingsButton.addEventListener('click', (e) => {
  e.preventDefault();
  popupSettings.style.display = 'flex';
});

// Fermer le pop-up
closePopupButton.addEventListener('click', () => {
  popupSettings.style.display = 'none';
});

// Fermer le pop-up en cliquant en dehors
window.addEventListener('click', (e) => {
  if (e.target === popupSettings) {
    popupSettings.style.display = 'none';
  }
});



// Fonction pour basculer la visibilité de la sidebar
function toggleSidebar() {
  const sidebar = document.querySelector('.sidebar');
  sidebar.classList.toggle('open'); // Ajouter ou retirer la classe 'open'
}



// POP UP EVENEMENT CREATION


