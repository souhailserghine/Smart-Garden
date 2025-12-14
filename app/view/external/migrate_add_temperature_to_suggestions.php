<?php
/**
 * Migration: Ajouter la colonne temperature à la table suggestionplante
 */

$serveur = 'localhost';
$user = 'root';
$password = '';
$database = 'smart_garden';

$connexion = new mysqli($serveur, $user, $password, $database);

if ($connexion->connect_error) {
    die("❌ Connexion échouée: " . $connexion->connect_error);
}

$connexion->set_charset("utf8mb4");

// Vérifier si la colonne temperature existe déjà
$result = $connexion->query("SHOW COLUMNS FROM suggestionplante LIKE 'temperature'");

if ($result && $result->num_rows === 0) {
    // Ajouter la colonne temperature
    $query1 = "ALTER TABLE suggestionplante ADD COLUMN temperature DECIMAL(5,2) DEFAULT 20.0 AFTER description";
    
    if ($connexion->query($query1)) {
        echo "✅ Colonne 'temperature' ajoutée à la table 'suggestionplante'\n";
    } else {
        die("❌ Erreur lors de l'ajout de la colonne temperature: " . $connexion->error);
    }
} else {
    echo "ℹ️ La colonne 'temperature' existe déjà dans la table 'suggestionplante'\n";
}

// Vérifier si la colonne besoin_eau existe déjà
$result = $connexion->query("SHOW COLUMNS FROM suggestionplante LIKE 'besoin_eau'");

if ($result && $result->num_rows === 0) {
    // Ajouter la colonne besoin_eau
    $query2 = "ALTER TABLE suggestionplante ADD COLUMN besoin_eau INT DEFAULT 500 AFTER temperature";
    
    if ($connexion->query($query2)) {
        echo "✅ Colonne 'besoin_eau' ajoutée à la table 'suggestionplante'\n";
    } else {
        die("❌ Erreur lors de l'ajout de la colonne besoin_eau: " . $connexion->error);
    }
} else {
    echo "ℹ️ La colonne 'besoin_eau' existe déjà dans la table 'suggestionplante'\n";
}

// Vérifier si la colonne niveau_humidite existe déjà
$result = $connexion->query("SHOW COLUMNS FROM suggestionplante LIKE 'niveau_humidite'");

if ($result && $result->num_rows === 0) {
    // Ajouter la colonne niveau_humidite
    $query3 = "ALTER TABLE suggestionplante ADD COLUMN niveau_humidite INT DEFAULT 60 AFTER besoin_eau";
    
    if ($connexion->query($query3)) {
        echo "✅ Colonne 'niveau_humidite' ajoutée à la table 'suggestionplante'\n";
    } else {
        die("❌ Erreur lors de l'ajout de la colonne niveau_humidite: " . $connexion->error);
    }
} else {
    echo "ℹ️ La colonne 'niveau_humidite' existe déjà dans la table 'suggestionplante'\n";
}

// Vérifier si la colonne etat_sante existe déjà
$result = $connexion->query("SHOW COLUMNS FROM suggestionplante LIKE 'etat_sante'");

if ($result && $result->num_rows === 0) {
    // Ajouter la colonne etat_sante
    $query4 = "ALTER TABLE suggestionplante ADD COLUMN etat_sante VARCHAR(50) DEFAULT 'Bon état' AFTER niveau_humidite";
    
    if ($connexion->query($query4)) {
        echo "✅ Colonne 'etat_sante' ajoutée à la table 'suggestionplante'\n";
    } else {
        die("❌ Erreur lors de l'ajout de la colonne etat_sante: " . $connexion->error);
    }
} else {
    echo "ℹ️ La colonne 'etat_sante' existe déjà dans la table 'suggestionplante'\n";
}

$connexion->close();

echo "\n✅ Migration complétée avec succès!\n";
?>
