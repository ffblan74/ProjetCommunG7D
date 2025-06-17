<?php
$apiKey = '445463b8c641a9999ed84e3fb21e5047'; // Remplace par ta vraie clé OpenWeatherMap
$ville = 'Paris';
$unites = 'metric'; // 'metric' pour °C, 'imperial' pour °F
$langue = 'fr';

// Construction de l'URL de requête
$url = "https://api.openweathermap.org/data/2.5/weather?q={$ville}&appid={$apiKey}&units={$unites}&lang={$langue}";

// Appel à l’API
$response = file_get_contents($url);

if ($response === FALSE) {
    echo "❌ Erreur lors de l'appel à l'API météo.";
    exit;
}

$data = json_decode($response, true);

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
