<?php
// Script pour corriger les IDs utilisateur des publications
require_once '../../config.php';

try {
    $db = config::getConnexion();
    
    // Mettre à jour toutes les publications de l'utilisateur 18 vers l'utilisateur 7
    $query = $db->prepare("UPDATE publication SET idUtilisateur = 7 WHERE idUtilisateur = 18");
    $result = $query->execute();
    
    if ($result) {
        $count = $query->rowCount();
        echo "<h2>✅ Succès !</h2>";
        echo "<p>$count publication(s) ont été transférées de l'utilisateur #18 vers l'utilisateur #7.</p>";
        echo "<br><a href='publications.php'>Retour aux publications</a>";
    } else {
        echo "<h2>❌ Erreur</h2>";
        echo "<p>Impossible de mettre à jour les publications.</p>";
    }
    
} catch (Exception $e) {
    echo "<h2>❌ Erreur</h2>";
    echo "<p>Erreur : " . $e->getMessage() . "</p>";
}
?>
