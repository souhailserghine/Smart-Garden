<?php
// Script pour afficher tous les utilisateurs
require_once '../../config.php';
session_start();

echo "<h2>🔍 Informations de Debug</h2>";
echo "<div style='background: #e3f2fd; padding: 15px; margin: 10px 0; border-left: 4px solid #2196F3;'>";
echo "<h3>Session actuelle :</h3>";
echo "ID dans la session : " . ($_SESSION['idUtilisateur'] ?? 'NON DÉFINI') . "<br>";
echo "Nom dans la session : " . ($_SESSION['user_name'] ?? 'NON DÉFINI') . "<br>";
echo "Email dans la session : " . ($_SESSION['email'] ?? 'NON DÉFINI') . "<br>";
echo "</div>";

try {
    $db = config::getConnexion();
    
    echo "<h3>📋 Liste de tous les utilisateurs :</h3>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f5f5f5;'>";
    echo "<th>ID</th><th>Nom</th><th>Email</th><th>Rôle</th><th>Statut</th>";
    echo "</tr>";
    
    $query = $db->query("SELECT idUtilisateur, nom, email, role, statut FROM utilisateur ORDER BY idUtilisateur");
    $users = $query->fetchAll();
    
    foreach ($users as $user) {
        // Highlight current user
        $highlight = ($user['idUtilisateur'] == ($_SESSION['idUtilisateur'] ?? 0)) ? 
            "background: #c8e6c9; font-weight: bold;" : "";
        
        echo "<tr style='$highlight'>";
        echo "<td>" . $user['idUtilisateur'] . "</td>";
        echo "<td>" . htmlspecialchars($user['nom']) . "</td>";
        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
        echo "<td>" . htmlspecialchars($user['role']) . "</td>";
        echo "<td>" . htmlspecialchars($user['statut']) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<br><h3>📝 Publications par utilisateur :</h3>";
    $query2 = $db->query("
        SELECT idUtilisateur, COUNT(*) as nb_publications 
        FROM publication 
        GROUP BY idUtilisateur
        ORDER BY idUtilisateur
    ");
    $pubStats = $query2->fetchAll();
    
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr style='background: #f5f5f5;'>";
    echo "<th>ID Utilisateur</th><th>Nombre de publications</th>";
    echo "</tr>";
    
    foreach ($pubStats as $stat) {
        $highlight = ($stat['idUtilisateur'] == ($_SESSION['idUtilisateur'] ?? 0)) ? 
            "background: #c8e6c9; font-weight: bold;" : "";
        
        echo "<tr style='$highlight'>";
        echo "<td>" . $stat['idUtilisateur'] . "</td>";
        echo "<td>" . $stat['nb_publications'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur : " . $e->getMessage() . "</p>";
}

echo "<br><br><a href='publications.php' style='display: inline-block; padding: 10px 20px; background: #2196F3; color: white; text-decoration: none; border-radius: 4px;'>← Retour aux publications</a>";
?>
