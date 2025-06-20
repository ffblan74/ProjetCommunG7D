

// Convertisseur HEX -> HSL
function hexToHSL(hex) {
    let r = parseInt(hex.slice(1, 3), 16) / 255;
    let g = parseInt(hex.slice(3, 5), 16) / 255;
    let b = parseInt(hex.slice(5, 7), 16) / 255;

    const max = Math.max(r, g, b), min = Math.min(r, g, b);
    let h, s, l = (max + min) / 2;

    if (max === min) {
      h = s = 0;
    } else {
      const d = max - min;
      s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
      switch (max) {
        case r: h = (g - b) / d + (g < b ? 6 : 0); break;
        case g: h = (b - r) / d + 2; break;
        case b: h = (r - g) / d + 4; break;
      }
      h *= 60;
    }

    return {
      h: Math.round(h),
      s: Math.round(s * 100),
      l: Math.round(l * 100)
    };
  }

  // Convertisseur HSL -> HEX
  function hslToHex(h, s, l) {
    s /= 100;
    l /= 100;

    const k = n => (n + h / 30) % 12;
    const a = s * Math.min(l, 1 - l);
    const f = n => Math.round(255 * (l - a * Math.max(-1, Math.min(Math.min(k(n) - 3, 9 - k(n)), 1))));

    return "#" + [f(0), f(8), f(4)].map(x => x.toString(16).padStart(2, '0')).join('');
  }

  // Calcul de l’écart entre deux couleurs HSL
  function getHSLDelta(baseHex, targetHex) {
    const base = hexToHSL(baseHex);
    const target = hexToHSL(targetHex);
    return {
      h: target.h - base.h,
      s: target.s - base.s,
      l: target.l - base.l
    };
  }

  // Appliquer un décalage HSL à une couleur
  function applyHSLDelta(hex, delta) {
    const hsl = hexToHSL(hex);
    const newH = (hsl.h + delta.h + 360) % 360;
    const newS = Math.min(100, Math.max(0, hsl.s + delta.s));
    const newL = Math.min(100, Math.max(0, hsl.l + delta.l));
    return hslToHex(newH, newS, newL);
  }


// On récupèd les préférences de l'utilisateur depuis les cookies
function getCookie(name) {
    const c = document.cookie.split('; ').find(r=>r.startsWith(name+'='));
    return c ? decodeURIComponent(c.split('=')[1]) : null;
  }
  // -------------------------------------------------------------- SETUP -----------------------------------------------------------------
  const baseBlue = "#4a6fa5";      // Bleu de référence
  let userColor = getCookie('color');   // Couleur cible définie par l'utilisateur

  if (!userColor || userColor[0] !== '#') userColor = baseBlue;

  const bckgBlue = "#f8f9fa";   // Nuance de fond du site
  let delta = getHSLDelta(baseBlue, bckgBlue); // Calcul de l'écart HSL entre le bleu de référence et la couleur de fond
  let bckgColor = applyHSLDelta(userColor, delta); // Couleur de fond transformée

  const heroBckgBlue = "#4a6fa51a"; // Nuance gradient de fond du hero
  delta = getHSLDelta(baseBlue, heroBckgBlue);
  let heroBckgColor = applyHSLDelta(userColor, delta);
    //   HEADER
  const headBckgBlue = "#D9E3E8"; // Bleu de fond du header
  delta = getHSLDelta(baseBlue, headBckgBlue);
  let headBckgColor = applyHSLDelta(userColor, delta); // Couleur de fond du header transformée

  const linkBlue = "#2B3A4B"; // Bleu du titre du header
  delta = getHSLDelta(baseBlue, linkBlue);
  let linkColor = applyHSLDelta(userColor, delta); // Couleur du lien transformée

  const linkHoverBlue = "#3C75A6"; // Bleu du titre du header au survol
  delta = getHSLDelta(baseBlue, linkHoverBlue);
  let linkHoverColor = applyHSLDelta(userColor, delta); // Couleur du lien au survol transformée

  const connectionBlue = "#5A9BD5"; // Bleu du bouton de connexion
  delta = getHSLDelta(baseBlue, connectionBlue);
  let connectionColor = applyHSLDelta(userColor, delta); // Couleur du bouton de connexion transformée
  
  const connectedBlue = "#A8B0B8"; // Bleu du bouton de connexion quand connecté
  delta = getHSLDelta(baseBlue, connectedBlue);
  let connectedColor = applyHSLDelta(userColor, delta); // Couleur du bouton de connexion quand connecté transformée



  const root = document.documentElement;
  
  root.style.setProperty('--color', userColor || baseBlue); // Couleur principale
  root.style.setProperty('--bckg-color', bckgColor || bckgBlue); // Couleur de fond du site
  root.style.setProperty('--hero-bckg-color', heroBckgColor || heroBckgBlue); // Couleur de fond du hero
  root.style.setProperty('--head-bckg-color', headBckgColor || headBckgBlue); // Couleur de fond du header
  root.style.setProperty('--link-color', linkColor || linkBlue); // Couleur du lien du header
  root.style.setProperty('--link-hover-color', linkHoverColor || linkHoverBlue); // Couleur du lien du header au survol
  root.style.setProperty('--connection-color', connectionColor || connectionBlue); // Couleur du bouton de connexion
  root.style.setProperty('--connected-color', connectedColor || connectedBlue); // Couleur du bouton de connexion quand connecté

  
  const originalBox = document.getElementById("original");
  const transformedBox = document.getElementById("transformed");

  function updateColors(hex) {
    originalBox.style.backgroundColor = hex;
    const transformed = applyHSLDelta(hex, delta);
    transformedBox.style.backgroundColor = transformed;
  }
