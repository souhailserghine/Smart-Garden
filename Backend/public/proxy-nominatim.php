<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if (!isset($_GET['lat']) || !isset($_GET['lon'])) {
    echo json_encode(['display_name' => 'Position inconnue']);
    exit;
}

$lat = $_GET['lat'];
$lon = $_GET['lon'];
$url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lon}&zoom=18&addressdetails=1";

$context = stream_context_create([
    'http' => ['header' => "User-Agent: SmartGarden/1.0\r\n"]
]);

echo @file_get_contents($url, false, $context) ?: json_encode(['display_name' => 'Adresse non trouvée']);
?>