<?php
// Test rapide pour vérifier si les données des capteurs sont bien récupérées

require_once '../../config.php';

$db = config::getConnexion();

echo "<h2>Test de récupération des données capteurs</h2>";

// Test 1: Vérifier la table sensor_data
echo "<h3>1. Données dans sensor_data:</h3>";
$stmt = $db->query('SELECT * FROM sensor_data ORDER BY timestamp DESC LIMIT 5');
$sensorData = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($sensorData)) {
    echo "<p style='color: red;'>❌ Aucune donnée dans sensor_data. L'ESP32 n'a pas encore envoyé de données.</p>";
} else {
    echo "<p style='color: green;'>✅ " . count($sensorData) . " enregistrement(s) trouvé(s)</p>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID Capteur</th><th>Température</th><th>Humidité</th><th>Timestamp</th></tr>";
    foreach ($sensorData as $row) {
        echo "<tr>";
        echo "<td>" . $row['id_capteur'] . "</td>";
        echo "<td>" . $row['temperature'] . "°C</td>";
        echo "<td>" . $row['humidite'] . "%</td>";
        echo "<td>" . $row['timestamp'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Test 2: Vérifier la jointure avec capteur
echo "<h3>2. Jointure capteur + sensor_data:</h3>";
$query = 'SELECT c.id_capteur, c.etatCapteur, c.emplacement,
                 sd.temperature, sd.humidite, sd.timestamp as last_reading
          FROM capteur c
          LEFT JOIN sensor_data sd ON c.id_capteur = sd.id_capteur 
              AND sd.timestamp = (
                  SELECT MAX(timestamp) 
                  FROM sensor_data 
                  WHERE id_capteur = c.id_capteur
              )
          LIMIT 5';

$stmt = $db->query($query);
$joinData = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($joinData)) {
    echo "<p style='color: red;'>❌ Aucun capteur trouvé dans la table capteur</p>";
} else {
    echo "<p style='color: green;'>✅ " . count($joinData) . " capteur(s) trouvé(s)</p>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID Capteur</th><th>État</th><th>Emplacement</th><th>Température</th><th>Humidité</th><th>Dernière lecture</th></tr>";
    foreach ($joinData as $row) {
        echo "<tr>";
        echo "<td>" . $row['id_capteur'] . "</td>";
        echo "<td>" . $row['etatCapteur'] . "</td>";
        echo "<td>" . $row['emplacement'] . "</td>";
        echo "<td>" . ($row['temperature'] ? $row['temperature'] . "°C" : "<em>Aucune donnée</em>") . "</td>";
        echo "<td>" . ($row['humidite'] ? $row['humidite'] . "%" : "<em>Aucune donnée</em>") . "</td>";
        echo "<td>" . ($row['last_reading'] ?? "<em>Jamais</em>") . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<hr>";
echo "<p><a href='listCapteur.php'>→ Aller à la page Capteurs</a></p>";
?>
