<?php
session_start();

echo "<h2>🔧 Correction de la session</h2>";

echo "<div style='background: #fff3cd; padding: 15px; margin: 10px 0; border-left: 4px solid #ffc107;'>";
echo "<h3>Session AVANT correction :</h3>";
echo "ID (user_id) : " . ($_SESSION['user_id'] ?? 'NON DÉFINI') . "<br>";
echo "ID (idUtilisateur) : " . ($_SESSION['idUtilisateur'] ?? 'NON DÉFINI') . "<br>";
echo "Nom (user_name) : " . ($_SESSION['user_name'] ?? 'NON DÉFINI') . "<br>";
echo "Email (user_email) : " . ($_SESSION['user_email'] ?? 'NON DÉFINI') . "<br>";
echo "</div>";

// Récupérer les bonnes informations depuis la base de données
require_once '../../config.php';

if (isset($_SESSION['user_id'])) {
    try {
        $db = config::getConnexion();
        $query = $db->prepare("SELECT * FROM utilisateur WHERE idUtilisateur = ?");
        $query->execute([$_SESSION['user_id']]);
        $user = $query->fetch();
        
        if ($user) {
            // Corriger la session avec les bonnes informations
            $_SESSION['idUtilisateur'] = $user['idUtilisateur'];
            $_SESSION['user_name'] = $user['nom'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_localisation'] = $user['localisation'];
            $_SESSION['user_role'] = $user['role'];
            
            echo "<div style='background: #d4edda; padding: 15px; margin: 10px 0; border-left: 4px solid #28a745;'>";
            echo "<h3>✅ Session APRÈS correction :</h3>";
            echo "ID : " . $_SESSION['idUtilisateur'] . "<br>";
            echo "Nom : " . $_SESSION['user_name'] . "<br>";
            echo "Email : " . $_SESSION['user_email'] . "<br>";
            echo "Rôle : " . $_SESSION['user_role'] . "<br>";
            echo "</div>";
            
            echo "<p><strong>✨ Session corrigée avec succès !</strong></p>";
        } else {
            echo "<p style='color: red;'>❌ Utilisateur non trouvé</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erreur : " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Aucun utilisateur connecté</p>";
}

echo "<br><a href='publications.php' style='display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 4px;'>→ Aller aux publications</a>";
echo " ";
echo "<a href='profile.php' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px;'>→ Aller au profil</a>";
?>
