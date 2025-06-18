<?php
$lat = $_COOKIE['lat'] ?? null;
$lon = $_COOKIE['lon'] ?? null;



// Clé API et appel à OpenWeatherMap
$apiKey = '445463b8c641a9999ed84e3fb21e5047';
$ville = 'Paris'; // Ville par défaut si lat/lon non définis
$unites = 'metric';
$langue = 'fr';
// Url par défaut si lat et lon non définis
$url = "https://api.openweathermap.org/data/2.5/weather?q={$ville}&appid={$apiKey}&units={$unites}&lang={$langue}";
// Sinon on utilise les coordonnées fournies
if ($lat && $lon) {
    $url = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$apiKey}&units={$unites}&lang={$langue}";
}

$response = file_get_contents($url);
if ($response === FALSE) {
    echo "❌ Erreur lors de l'appel à l'API météo.";
}

$data = json_decode($response, true);
if (!$data || !isset($data['main'])) {
    echo "❌ Réponse météo invalide.";
}

// Extraction des données utiles
$temp = $data['main']['temp'];
$desc = ucfirst($data['weather'][0]['description']);
$ville = $data['name'];
$icone = $data['weather'][0]['icon'];
$tempMin = $data['main']['temp_min'];
$tempMax = $data['main']['temp_max'];
$humidite = $data['main']['humidity'];
$nuages = $data['clouds']['all'];

$leverSoleil = date("H:i", $data['sys']['sunrise']);
$coucherSoleil = date("H:i", $data['sys']['sunset']);

echo "<h2>Météo à {$ville}</h2>";
echo "<img src='https://openweathermap.org/img/wn/{$icone}@2x.png' alt='Icône météo'>";
echo "<p>{$desc}, {$temp}°C</p>";
echo "<br>🔻 Min : {$tempMin}°C / 🔺 Max : {$tempMax}°C<br>";
echo "💧 Humidité : {$humidite}%<br>";
echo "☁️ Couverture nuageuse : {$nuages}%<br>";
echo "🌅 Lever du soleil : {$leverSoleil} <br>🌇 Coucher : {$coucherSoleil}<br>";
?>
