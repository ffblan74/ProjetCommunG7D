// = Cookies
function setCookie(name, val, days = 365) {
  document.cookie = `${name}=${encodeURIComponent(val)};path=/;max-age=${days*86400}`;
}
function getCookie(name) {
  const c = document.cookie.split('; ').find(r=>r.startsWith(name+'='));
  return c ? decodeURIComponent(c.split('=')[1]) : null;
}
function deleteCookie(name) {
  document.cookie = `${name}=;path=/;max-age=0`;
}
// = Appliquer les préférences
function applyPrefs() {
  const root = document.documentElement;

  // Thème couleurs principales
  const couleur = getCookie('color');
  if (couleur) root.style.setProperty('--color', couleur);
  const colorInput = document.getElementById('color');
  if (colorInput) {
    colorInput.value = couleur || '#4a6fa5'; // Valeur par défaut
    root.style.setProperty('--color', colorInput.value);
  }
  // Contraste (clair / sombre)
  const contrast = getCookie('contrast');
  if (contrast === 'dark') {
    root.style.setProperty('--background-color', '#000');
    root.style.setProperty('--text-color', '#fff');
    root.style.setProperty('--color', '#2222aa');
  } else {
    root.style.setProperty('--background-color', '#fff');
    root.style.setProperty('--text-color', '#000');
  }
  const contrastSelect = document.getElementById('contrast-mode');
  if (contrastSelect) {
    contrastSelect.value = contrast || 'light'; // Valeur par défaut
  }
  // Saturation
  const saturation = getCookie('saturation');
  const saturationSlider = document.getElementById('saturation');
  if (saturationSlider) {
    saturationSlider.value = saturation || '1'; // Valeur par défaut
  }
  document.body.style.filter = saturation ? `saturate(${saturation})` : 'none';

  // Police
  const font = getCookie('fontFamily');
  if (font) document.documentElement.style.setProperty('--font-family', font);
  const fontSelect = document.getElementById('font-family');
  if (fontSelect) {
    fontSelect.value = font || 'Arial, sans-serif'; // Valeur par défaut
  }

  // Taille de police (multiplicateur 0.8 - 1.5)
  const multiplier = parseFloat(getCookie('fontSizeMultiplier') || '1');
  // Met à jour la value du slider dans l'UI
  const fontSizeSlider = document.getElementById('font-size');
  if (fontSizeSlider) {
    fontSizeSlider.value = multiplier;
  }
  document.querySelectorAll('h1,h2,h3,h4,h5,h6,p,span,li,a,label').forEach(el => {
    const baseSize = parseFloat(el.getAttribute('data-base-font-size'));
    el.style.fontSize = `${baseSize * multiplier}px`;
  });

  // Espacement texte
  const lineSpacing = getCookie('lineSpacing');
  if (lineSpacing) document.body.style.lineHeight = lineSpacing;
  const lineSpacingSelect = document.getElementById('line-spacing');
  if (lineSpacingSelect) {
    lineSpacingSelect.value = lineSpacing || '1.6'; // Valeur par défaut
  }
  // Réduction des animations
  const reduce = getCookie('reduceMotion') === 'true';
  document.body.classList.toggle('reduce-motion', reduce);
  const reduceMotionCheckbox = document.getElementById('reduce-motion');
  if (reduceMotionCheckbox) {
    reduceMotionCheckbox.checked = reduce;
  }

  // Soulignement des liens
  const underline = getCookie('underlineLinks') === 'true';
  document.querySelectorAll('a').forEach(a => {
    a.style.textDecoration = underline ? 'underline' : 'none';
  });
  const underlineCheckbox = document.getElementById('underline-links');
  if (underlineCheckbox) {
    underlineCheckbox.checked = underline;
  }

  // Curseur visible
  const bigCursor = getCookie('bigCursor') === 'true';
  document.body.style.cursor = bigCursor ? 'url("/img/cursor-large.png"), auto' : 'auto';
  const bigCursorCheckbox = document.getElementById('big-cursor');
  if (bigCursorCheckbox) {
    bigCursorCheckbox.checked = bigCursor;
  }
}

// Applique les prefs au plus tôt possible (avant chargement complet)
applyPrefs();
// Initialisation
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('h1,h2,h3,h4,h5,h6,p,span,li,a,label').forEach(el => {
  if (!el.hasAttribute('data-base-font-size')) {
    const baseSize = window.getComputedStyle(el).getPropertyValue('font-size');
    el.setAttribute('data-base-font-size', baseSize);
  }
  });

  applyPrefs();

  // On attache les contrôles ici
  document.getElementById('color')?.addEventListener('input', e => {
    setCookie('color', e.target.value);
    applyPrefs();
  });

  document.getElementById('font-family')?.addEventListener('change', e => {
    setCookie('fontFamily', e.target.value);
    applyPrefs();
  });

  document.getElementById('font-size')?.addEventListener('input', e => {
    setCookie('fontSizeMultiplier', e.target.value);
    applyPrefs();
  });

  document.getElementById('line-spacing')?.addEventListener('change', e => {
    setCookie('lineSpacing', e.target.value);
    applyPrefs();
  });


  document.getElementById('saturation')?.addEventListener('input', e => {
    setCookie('saturation', e.target.value);
    applyPrefs();
  });

  document.getElementById('contrast-mode')?.addEventListener('change', e => {
    setCookie('contrast', e.target.value);
    applyPrefs();
  });

  document.getElementById('underline-links')?.addEventListener('change', e => {
    setCookie('underlineLinks', e.target.checked);
    applyPrefs();
  });

  document.getElementById('reduce-motion')?.addEventListener('change', e => {
    setCookie('reduceMotion', e.target.checked);
    applyPrefs();
  });

  document.getElementById('big-cursor')?.addEventListener('change', e => {
    setCookie('bigCursor', e.target.checked);
    applyPrefs();
  });

  // Bouton réinitialiser
  document.getElementById('reset-preferences')?.addEventListener('click', () => {
    const keys = [
      'color',
      'contrast',
      'saturation',
      'fontFamily',
      'fontSizeMultiplier',
      'lineSpacing',
      'reduceMotion',
      'underlineLinks',
      'bigCursor'
    ];
  
    keys.forEach(deleteCookie);
    location.reload(); // recharge la page avec les valeurs par défaut
    applyPrefs();
  });
});
