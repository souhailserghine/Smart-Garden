<?php
// Debug script to check login issues
require_once '../../config.php';
require_once '../../controller/utilisateurController.php';

$email = 'souhail.seghine@esen.tn';

$controller = new UtilisateurC();
$db = config::getConnexion();

echo "<h2>Debug Information for: " . htmlspecialchars($email) . "</h2>";

// Get user from database
$sql = "SELECT * FROM utilisateur WHERE email = :email";
$stmt = $db->prepare($sql);
$stmt->bindValue(':email', $email);
$stmt->execute();
$user = $stmt->fetch();

if ($user) {
    echo "<h3>User Found!</h3>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    echo "<tr><td>ID</td><td>" . $user['idUtilisateur'] . "</td></tr>";
    echo "<tr><td>Nom</td><td>" . htmlspecialchars($user['nom']) . "</td></tr>";
    echo "<tr><td>Email</td><td>" . htmlspecialchars($user['email']) . "</td></tr>";
    echo "<tr><td>Statut</td><td>" . htmlspecialchars($user['statut']) . "</td></tr>";
    echo "<tr><td>Role</td><td>" . htmlspecialchars($user['role']) . "</td></tr>";
    echo "<tr><td>Verified</td><td>" . ($user['verified'] ?? 'NULL') . "</td></tr>";
    echo "<tr><td>Password Hash (first 50 chars)</td><td>" . htmlspecialchars(substr($user['motDePasse'], 0, 50)) . "...</td></tr>";
    echo "<tr><td>Password Hash Length</td><td>" . strlen($user['motDePasse']) . " characters</td></tr>";
    echo "</table>";
    
    echo "<h3>Authentication Tests</h3>";
    
    // Test password
    $testPassword = 'test123'; // Try common test password
    echo "<p><strong>Testing password 'test123':</strong> ";
    if (password_verify($testPassword, $user['motDePasse'])) {
        echo "<span style='color: green;'>✓ MATCH!</span>";
    } else {
        echo "<span style='color: red;'>✗ No match</span>";
    }
    echo "</p>";
    
    // Test if verified
    echo "<p><strong>Is Verified:</strong> ";
    if ($controller->estVerifie($email)) {
        echo "<span style='color: green;'>✓ YES</span>";
    } else {
        echo "<span style='color: red;'>✗ NO - Account needs verification!</span>";
    }
    echo "</p>";
    
    // Test if blocked
    echo "<p><strong>Is Blocked:</strong> ";
    if ($user['statut'] == 'bloque') {
        echo "<span style='color: red;'>✗ YES - Account is blocked!</span>";
    } else {
        echo "<span style='color: green;'>✓ NO</span>";
    }
    echo "</p>";
    
    echo "<hr>";
    echo "<h3>How to Fix:</h3>";
    
    if (!$controller->estVerifie($email)) {
        echo "<p><a href='verify_account_manual.php?email=" . urlencode($email) . "' style='background: green; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>Click to Verify Account</a></p>";
    }
    
    echo "<form method='POST' action='reset_password_manual.php'>";
    echo "<input type='hidden' name='email' value='" . htmlspecialchars($email) . "'>";
    echo "<p><strong>Reset Password:</strong><br>";
    echo "New Password: <input type='text' name='newPassword' placeholder='Enter new password'>";
    echo " <button type='submit' style='background: blue; color: white; padding: 10px; border: none; border-radius: 5px;'>Reset Password</button></p>";
    echo "</form>";
    
} else {
    echo "<h3 style='color: red;'>User NOT Found in Database!</h3>";
    echo "<p>The email '" . htmlspecialchars($email) . "' does not exist in the database.</p>";
}
?>
