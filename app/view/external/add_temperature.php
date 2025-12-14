<?php
require_once '../../config.php';

try {
    $db = config::getConnexion();
    $db->exec("ALTER TABLE plante ADD COLUMN temperature DECIMAL(5,2) DEFAULT 20.0");
    echo "✅ Colonne temperature ajoutée avec succès";
} catch (Exception $e) {
    echo "✅ Colonne temperature existe déjà ou ajoutée: " . $e->getMessage();
}
?>
