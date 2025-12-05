<?php
// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "smart_garden");

if ($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}

// Lire le fichier SQL
$sql = file_get_contents('create_suggestiontache_table.sql');

// Exécuter la requête
if ($conn->query($sql) === TRUE) {
    echo "✓ Table suggestiontache créée avec succès!";
} else {
    echo "Erreur: " . $conn->error;
}

$conn->close();
?>
