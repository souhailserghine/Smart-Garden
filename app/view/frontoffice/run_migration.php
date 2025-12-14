<?php
/**
 * Database Migration Runner
 * This script creates the publication_likes table
 */

require_once '../../config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Migration - Publication Likes</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .success { color: #28a745; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0; }
        .error { color: #dc3545; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px 0; }
        .info { color: #004085; padding: 10px; background: #cce5ff; border: 1px solid #b8daff; border-radius: 4px; margin: 10px 0; }
        .sql-box { background: #f8f9fa; padding: 15px; border-radius: 4px; border-left: 4px solid #007bff; margin: 15px 0; overflow-x: auto; }
        pre { margin: 0; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin-top: 20px; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 Database Migration: Publication Likes System</h1>
";

try {
    $db = config::getConnexion();
    
    echo "<div class='info'><strong>Step 1:</strong> Checking if table already exists...</div>";
    
    // Check if table exists
    $checkTable = $db->query("SHOW TABLES LIKE 'publication_likes'");
    $tableExists = $checkTable->rowCount() > 0;
    
    if ($tableExists) {
        echo "<div class='success'>✓ Table 'publication_likes' already exists! No migration needed.</div>";
    } else {
        echo "<div class='info'>Table doesn't exist. Creating now...</div>";
        
        echo "<div class='info'><strong>Step 2:</strong> Creating publication_likes table...</div>";
        
        // Create the table
        $sql = "CREATE TABLE IF NOT EXISTS publication_likes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            idPublication INT NOT NULL,
            idUtilisateur INT NOT NULL,
            dateLike DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_publication (idPublication, idUtilisateur),
            FOREIGN KEY (idPublication) REFERENCES publication(idPublication) ON DELETE CASCADE,
            FOREIGN KEY (idUtilisateur) REFERENCES utilisateur(idUtilisateur) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $db->exec($sql);
        
        echo "<div class='success'>✓ Table 'publication_likes' created successfully!</div>";
        
        echo "<div class='sql-box'><strong>SQL Executed:</strong><pre>$sql</pre></div>";
    }
    
    echo "<div class='info'><strong>Step 3:</strong> Verifying table structure...</div>";
    
    // Verify table structure
    $describe = $db->query("DESCRIBE publication_likes");
    $columns = $describe->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div class='success'>✓ Table structure verified. Columns:</div>";
    echo "<div class='sql-box'><table border='1' cellpadding='8' style='width: 100%; border-collapse: collapse;'>";
    echo "<tr style='background: #007bff; color: white;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table></div>";
    
    echo "<h2>✅ Migration Complete!</h2>";
    echo "<div class='success'>";
    echo "<p><strong>What's New:</strong></p>";
    echo "<ul>";
    echo "<li>✓ Each user can now like publications independently</li>";
    echo "<li>✓ Duplicate likes are prevented at the database level</li>";
    echo "<li>✓ Like tracking is stored permanently in the database</li>";
    echo "<li>✓ System works across all browsers and devices</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<a href='publications.php' class='btn'>Go to Publications Page</a>";
    
} catch (PDOException $e) {
    echo "<div class='error'>";
    echo "<strong>❌ Migration Failed!</strong><br>";
    echo "Error: " . htmlspecialchars($e->getMessage());
    echo "</div>";
    
    echo "<div class='info'>";
    echo "<strong>Troubleshooting:</strong><br>";
    echo "<ul>";
    echo "<li>Make sure XAMPP MySQL is running</li>";
    echo "<li>Check that the database 'smartgarden' exists</li>";
    echo "<li>Verify that tables 'publication' and 'utilisateur' exist</li>";
    echo "<li>Check MySQL user has CREATE TABLE permissions</li>";
    echo "</ul>";
    echo "</div>";
}

echo "
    </div>
</body>
</html>";
?>
