<?php
// Test du système de suggestions de tâches
$conn = new mysqli("localhost", "root", "", "smart_garden");

if ($conn->connect_error) {
    die("Erreur: " . $conn->connect_error);
}

echo "=== TEST SYSTÈME DE SUGGESTIONS DE TÂCHES ===\n\n";

// 1. Vérifier la table suggestiontache
echo "1. Structure de la table suggestiontache:\n";
$result = $conn->query("DESCRIBE suggestiontache");
while ($row = $result->fetch_assoc()) {
    echo "   - " . $row['Field'] . " (" . $row['Type'] . ")\n";
}

// 2. Vérifier les relations
echo "\n2. Vérification des clés étrangères:\n";
$result = $conn->query("SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'suggestiontache'");
while ($row = $result->fetch_assoc()) {
    echo "   - " . $row['CONSTRAINT_NAME'] . ": " . $row['COLUMN_NAME'] . " -> " . $row['REFERENCED_TABLE_NAME'] . "\n";
}

// 3. Tester l'insertion d'une suggestion de tâche
echo "\n3. Test d'insertion d'une suggestion:\n";
$query = "INSERT INTO suggestiontache 
          (id_utilisateur, type_dosage, quantite, mode_dosage, date_dosage, prochaineExecution, priorite, id_plante, statut) 
          VALUES (18, 'Arrosage', 2.50, 'Par aspersion', '2025-12-05', '2025-12-06 10:00:00', 'Moyen', 1, 'En attente')";

if ($conn->query($query)) {
    $lastId = $conn->insert_id;
    echo "   ✓ Suggestion ajoutée avec l'ID: $lastId\n";
    
    // 4. Vérifier l'insertion
    echo "\n4. Vérification de la suggestion:\n";
    $result = $conn->query("SELECT * FROM suggestiontache WHERE id_suggestion = $lastId");
    if ($row = $result->fetch_assoc()) {
        echo "   ID: " . $row['id_suggestion'] . "\n";
        echo "   Type: " . $row['type_dosage'] . "\n";
        echo "   Quantité: " . $row['quantite'] . "\n";
        echo "   Mode: " . $row['mode_dosage'] . "\n";
        echo "   Date: " . $row['date_dosage'] . "\n";
        echo "   Prochaine: " . $row['prochaineExecution'] . "\n";
        echo "   Priorité: " . $row['priorite'] . "\n";
        echo "   Statut: " . $row['statut'] . "\n";
    }
} else {
    echo "   ✗ Erreur: " . $conn->error . "\n";
}

$conn->close();
echo "\n✓ Test complété!\n";
?>
