<?php
/**
 * Script pour créer la table sensor_data
 */

require_once '../../config.php';

echo "<h2>Création de la table sensor_data</h2>";

try {
    $db = config::getConnexion();
    
    // Vérifier si la table existe déjà
    $checkTable = $db->query("SHOW TABLES LIKE 'sensor_data'");
    
    if ($checkTable->rowCount() > 0) {
        echo "<p style='color: orange;'>⚠️ La table 'sensor_data' existe déjà.</p>";
        
        // Afficher la structure actuelle
        echo "<h3>Structure actuelle :</h3>";
        $structure = $db->query("DESCRIBE sensor_data");
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th><th>Extra</th></tr>";
        while ($col = $structure->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $col['Field'] . "</td>";
            echo "<td>" . $col['Type'] . "</td>";
            echo "<td>" . $col['Null'] . "</td>";
            echo "<td>" . $col['Key'] . "</td>";
            echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
            echo "<td>" . $col['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Compter les enregistrements
        $count = $db->query("SELECT COUNT(*) as total FROM sensor_data")->fetch();
        echo "<p>📊 Nombre d'enregistrements : <strong>" . $count['total'] . "</strong></p>";
        
    } else {
        echo "<p style='color: blue;'>🔧 Création de la table 'sensor_data'...</p>";
        
        // Créer la table
        $sql = "CREATE TABLE IF NOT EXISTS `sensor_data` (
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `id_capteur` INT(11) NOT NULL,
          `temperature` DECIMAL(5,2) NOT NULL,
          `humidite` DECIMAL(5,2) NOT NULL,
          `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `idx_capteur_timestamp` (`id_capteur`, `timestamp`),
          CONSTRAINT `fk_sensor_capteur` FOREIGN KEY (`id_capteur`) REFERENCES `capteur` (`id_capteur`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->exec($sql);
        
        echo "<p style='color: green;'>✅ Table 'sensor_data' créée avec succès !</p>";
        
        // Créer les index
        try {
            $db->exec("CREATE INDEX `idx_timestamp_desc` ON `sensor_data` (`timestamp` DESC)");
            echo "<p style='color: green;'>✅ Index créés avec succès !</p>";
        } catch (PDOException $e) {
            // Index peut déjà exister
            echo "<p style='color: orange;'>⚠️ Index déjà existants (normal)</p>";
        }
    }
    
    echo "<hr>";
    echo "<h3>Test de la table :</h3>";
    
    // Afficher les dernières données
    $stmt = $db->query("SELECT * FROM sensor_data ORDER BY timestamp DESC LIMIT 10");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($data)) {
        echo "<p style='color: blue;'>ℹ️ Aucune donnée dans la table (c'est normal si vous venez de la créer).</p>";
        echo "<p>L'ESP32 va commencer à enregistrer les données dans quelques secondes...</p>";
    } else {
        echo "<p style='color: green;'>✅ Données trouvées :</p>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Capteur ID</th><th>Température</th><th>Humidité</th><th>Timestamp</th></tr>";
        foreach ($data as $row) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['id_capteur'] . "</td>";
            echo "<td>" . $row['temperature'] . "°C</td>";
            echo "<td>" . $row['humidite'] . "%</td>";
            echo "<td>" . $row['timestamp'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<hr>";
    echo "<p><strong>🎉 Configuration terminée !</strong></p>";
    echo "<p>Attendez 5-10 secondes puis :</p>";
    echo "<ul>";
    echo "<li><a href='test_sensor_display.php' target='_blank'>→ Vérifier les données des capteurs</a></li>";
    echo "<li><a href='listCapteur.php' target='_blank'>→ Voir la liste des capteurs avec valeurs en temps réel</a></li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Erreur : " . $e->getMessage() . "</p>";
    
    if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
        echo "<p style='color: orange;'>⚠️ La contrainte de clé étrangère a échoué.</p>";
        echo "<p>Cela signifie probablement que vous essayez d'insérer des données pour un capteur (id_capteur) qui n'existe pas dans la table 'capteur'.</p>";
        echo "<p><strong>Solution :</strong> Créer d'abord les capteurs dans la table 'capteur', puis réessayer.</p>";
    }
}
?>
