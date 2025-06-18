function setCookie(name, value, seconds) {
  let expires = "";
  if (seconds === -1) {
    expires = "; expires=Thu, 01 Jan 1970 00:00:00 UTC";
  } else if (seconds) {
    const date = new Date();
    date.setTime(date.getTime() + (seconds * 1000));
    expires = "; expires=" + date.toUTCString();
  }
  document.cookie = name + "=" + encodeURIComponent(value) + expires + "; path=/";
}

function getCookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return decodeURIComponent(parts.pop().split(';').shift());
  return null;
}

function requestGeolocation() {
  if (!navigator.geolocation) {
    console.warn("Géolocalisation non supportée.");
    return;
  }

  navigator.geolocation.getCurrentPosition(
    (position) => {
      const lat = position.coords.latitude.toFixed(6);
      const lon = position.coords.longitude.toFixed(6);
      console.log("Localisation acceptée :", lat, lon);

      setCookie("geoConsent", "true", 365 * 24 * 60 * 60); // 1 an
      setCookie("lat", lat, 7 * 24 * 60 * 60);
      setCookie("lon", lon, 7 * 24 * 60 * 60);
      clearInterval(window.geoRetryInterval);
      window.location.reload();
    },
    (error) => {
      console.warn("Localisation refusée :", error.message);
      setCookie("geoConsent", "false", 60); // Refus valable 1 minute
    }
  );
}

window.onload = function () {
  const consent = getCookie("geoConsent");
  const lat = getCookie("lat");
  const lon = getCookie("lon");

  if (consent === "true" && lat && lon) {
    console.log("Consent déjà donné. Coordonnées présentes.");
    return;
  }

  if (consent === "false") {
    // Relancer toutes les minutes
    window.geoRetryInterval = setInterval(() => {
      if (getCookie("geoConsent") !== "true") {
        console.log("Nouvelle tentative de géolocalisation navigateur...");
        requestGeolocation();
      }
    }, 60 * 1000);
    return;
  }

  // Première visite : demander
  requestGeolocation();
};