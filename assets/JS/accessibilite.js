// = Cookies
function setCookie(name, val, days = 365) {
  document.cookie = `${name}=${encodeURIComponent(val)};path=/;max-age=${days*86400}`;
}
function getCookie(name) {
  const c = document.cookie.split('; ').find(r=>r.startsWith(name+'='));
  return c ? decodeURIComponent(c.split('=')[1]) : null;
}

// = Appliquer les préférences
function applyPrefs() {
  const root = document.documentElement;

  // Thème couleurs principales
  const primary = getCookie('primaryColor');
  const secondary = getCookie('secondaryColor');
  if (primary) root.style.setProperty('--primary-color', primary);
  if (secondary) root.style.setProperty('--secondary-color', secondary);

  // Contraste (clair / sombre)
  const contrast = getCookie('contrast');
  if (contrast === 'dark') {
    root.style.setProperty('--background-color', '#000');
    root.style.setProperty('--text-color', '#fff');
    root.style.setProperty('--primary-color', '#2222aa');
    root.style.setProperty('--secondary-color', '#8800ff');
  } else {
    root.style.setProperty('--background-color', '#fff');
    root.style.setProperty('--text-color', '#000');
  }

  // Saturation
  const saturation = getCookie('saturation');
  const saturationSlider = document.getElementById('saturation');
  if (saturationSlider) {
    saturationSlider.value = saturation;
  }
  document.body.style.filter = saturation ? `saturate(${saturation})` : 'none';

  // Police
  const font = getCookie('fontFamily');
  if (font) document.body.style.fontFamily = font;

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

  // Espacement objets
  const objectSpacing = getCookie('objectSpacing');
  document.body.classList.remove('spacing-compact', 'spacing-normal', 'spacing-spacious');
  if (objectSpacing) document.body.classList.add(`spacing-${objectSpacing}`);

  // Réduction des animations
  const reduce = getCookie('reduceMotion') === 'true';
  document.body.classList.toggle('reduce-motion', reduce);

  // Soulignement des liens
  const underline = getCookie('underlineLinks') === 'true';
  document.querySelectorAll('a').forEach(a => {
    a.style.textDecoration = underline ? 'underline' : 'none';
  });

  // Curseur visible
  const bigCursor = getCookie('bigCursor') === 'true';
  document.body.style.cursor = bigCursor ? 'url("/img/cursor-large.png"), auto' : 'auto';
}

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
  document.getElementById('primary-color')?.addEventListener('input', e => {
    setCookie('primaryColor', e.target.value);
    applyPrefs();
  });
  document.getElementById('secondary-color')?.addEventListener('input', e => {
    setCookie('secondaryColor', e.target.value);
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

  document.getElementById('object-spacing')?.addEventListener('change', e => {
    setCookie('objectSpacing', e.target.value);
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
});
