<?php
/**
 * Migration script to add image column to plante table
 */

require_once 'config.php';

try {
    $db = config::getConnexion();
    
    // Check if image column already exists
    $result = $db->query("SHOW COLUMNS FROM plante LIKE 'image'");
    
    if ($result->rowCount() === 0) {
        // Add image column
        $db->exec("ALTER TABLE plante ADD COLUMN image VARCHAR(255) NULL DEFAULT NULL");
        echo "✓ Column 'image' added successfully to plante table\n";
    } else {
        echo "✓ Column 'image' already exists in plante table\n";
    }
    
    // Check if temperature column already exists
    $result = $db->query("SHOW COLUMNS FROM plante LIKE 'temperature'");
    
    if ($result->rowCount() === 0) {
        // Add temperature column
        $db->exec("ALTER TABLE plante ADD COLUMN temperature DECIMAL(5,2) DEFAULT 20.0");
        echo "✓ Column 'temperature' added successfully to plante table\n";
    } else {
        echo "✓ Column 'temperature' already exists in plante table\n";
    }
    
    echo "\nDatabase migration completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
