function acceptCookies() {
    localStorage.setItem("geoConsent", "true");
    document.getElementById("cookie-banner").style.display = "none";
    getIPLocation(); // Lance localisation
  }
  
  function refuseCookies() {
    localStorage.setItem("geoConsent", "false");
    document.getElementById("cookie-banner").style.display = "none";
  }
  
  window.onload = function() {
    const consent = localStorage.getItem("geoConsent");
    if (consent === "true") {
      getIPLocation();
      document.getElementById("cookie-banner").style.display = "none";
    } else if (consent === "false") {
      document.getElementById("cookie-banner").style.display = "none";
    }
  };


  function getIPLocation() {
    fetch("https://ipinfo.io/json?token=445463b8c641a9999ed84e3fb21e5047")
        .then(response => response.json())
        .then(data => {
            const loc = data.loc.split(",");
            const lat = loc[0];
            const lon = loc[1];

            // Sauvegarde dans localStorage
            localStorage.setItem("lat", lat);
            localStorage.setItem("lon", lon);
        })
        .catch(error => {
            console.error("Erreur géolocalisation IP:", error);
        });
  }