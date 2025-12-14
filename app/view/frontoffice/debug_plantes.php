<?php
session_start();
include_once '../../config.php';
include_once '../../controller/planteC.php';

echo "<h2>Debug: Plantes Not Showing</h2>";

// Check session
echo "<h3>1. Session Check:</h3>";
echo "Session ID Utilisateur: " . ($_SESSION['idUtilisateur'] ?? 'NOT SET') . "<br>";

if (!isset($_SESSION['idUtilisateur'])) {
    $_SESSION['idUtilisateur'] = 18; // Fallback
    echo "Setting fallback ID: 18<br>";
}

$userId = $_SESSION['idUtilisateur'];
echo "Using User ID: " . $userId . "<br><br>";

// Check database connection
echo "<h3>2. Database Connection:</h3>";
try {
    $db = config::getConnexion();
    echo "✓ Database connected successfully<br><br>";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "<br><br>";
    exit;
}

// Check if plante table exists
echo "<h3>3. Table Check:</h3>";
try {
    $result = $db->query("SHOW TABLES LIKE 'plante'");
    if ($result->rowCount() > 0) {
        echo "✓ Table 'plante' exists<br>";
        
        // Check table structure
        $columns = $db->query("DESCRIBE plante")->fetchAll(PDO::FETCH_ASSOC);
        echo "Table columns: ";
        foreach ($columns as $col) {
            echo $col['Field'] . ", ";
        }
        echo "<br><br>";
    } else {
        echo "✗ Table 'plante' does NOT exist<br><br>";
    }
} catch (Exception $e) {
    echo "Error checking table: " . $e->getMessage() . "<br><br>";
}

// Check total plants in database
echo "<h3>4. Total Plants in Database:</h3>";
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM plante");
    $count = $stmt->fetch();
    echo "Total plants in database: " . $count['total'] . "<br><br>";
} catch (Exception $e) {
    echo "Error counting plants: " . $e->getMessage() . "<br><br>";
}

// Check plants for current user
echo "<h3>5. Plants for User ID $userId:</h3>";
try {
    $planteC = new planteC();
    $mesPlantes = $planteC->listPlantesByUser($userId);
    
    echo "Plants found: " . count($mesPlantes) . "<br>";
    
    if (empty($mesPlantes)) {
        echo "<strong style='color: orange;'>⚠ No plants found for user $userId</strong><br><br>";
        
        // Check if there are plants for other users
        $allPlants = $db->query("SELECT DISTINCT idUtilisateur FROM plante")->fetchAll();
        echo "Users with plants: ";
        foreach ($allPlants as $u) {
            echo $u['idUtilisateur'] . ", ";
        }
        echo "<br><br>";
    } else {
        echo "<strong style='color: green;'>✓ Plants found!</strong><br><br>";
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Name</th><th>User ID</th><th>Health</th><th>Humidity</th><th>Date Added</th></tr>";
        foreach ($mesPlantes as $p) {
            echo "<tr>";
            echo "<td>" . ($p['id_plante'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($p['nom_plante'] ?? 'N/A') . "</td>";
            echo "<td>" . ($p['idUtilisateur'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($p['etat_sante'] ?? 'N/A') . "</td>";
            echo "<td>" . ($p['niveau_humidite'] ?? 'N/A') . "%</td>";
            echo "<td>" . ($p['date_ajout'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    }
} catch (Exception $e) {
    echo "Error fetching plants: " . $e->getMessage() . "<br><br>";
}

// Check sample query directly
echo "<h3>6. Direct Query Test:</h3>";
try {
    $stmt = $db->prepare("SELECT * FROM plante WHERE idUtilisateur = :userId");
    $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $directResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Direct query results: " . count($directResults) . " plants<br><br>";
} catch (Exception $e) {
    echo "Error in direct query: " . $e->getMessage() . "<br><br>";
}

echo "<hr>";
echo "<h3>Recommendations:</h3>";
if (empty($mesPlantes)) {
    echo "<ul>";
    echo "<li>The user ID $userId has no plants in the database</li>";
    echo "<li>You need to add plants for this user</li>";
    echo "<li>Check if you're logged in with the correct user account</li>";
    echo "<li>Or try adding a test plant using the 'Add Plant' button</li>";
    echo "</ul>";
}

echo "<br><a href='plantes.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>← Back to Plantes Page</a>";
?>
