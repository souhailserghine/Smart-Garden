<?php
// Manual password reset script
require_once '../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['newPassword'])) {
    $email = $_POST['email'];
    $newPassword = $_POST['newPassword'];
    
    if (empty($newPassword)) {
        echo "<h2 style='color: red;'>Error</h2>";
        echo "<p>Password cannot be empty.</p>";
        echo "<p><a href='debug_login.php'>Go Back</a></p>";
        exit;
    }
    
    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    $db = config::getConnexion();
    
    $sql = "UPDATE utilisateur SET motDePasse = :password WHERE email = :email";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':password', $hashedPassword);
    $stmt->bindValue(':email', $email);
    
    if ($stmt->execute()) {
        echo "<h2 style='color: green;'>✓ Password Reset Successfully!</h2>";
        echo "<p>The password for <strong>" . htmlspecialchars($email) . "</strong> has been changed.</p>";
        echo "<p><strong>New Password:</strong> " . htmlspecialchars($newPassword) . "</p>";
        echo "<p><strong>Password Hash:</strong> " . htmlspecialchars($hashedPassword) . "</p>";
        echo "<p><a href='sign-in.php'>Go to Login Page</a></p>";
    } else {
        echo "<h2 style='color: red;'>✗ Password Reset Failed</h2>";
        echo "<p>Could not update the password.</p>";
        echo "<p><a href='debug_login.php'>Go Back</a></p>";
    }
} else {
    echo "<h2>Error</h2>";
    echo "<p>Invalid request.</p>";
    echo "<p><a href='debug_login.php'>Go Back</a></p>";
}
?>
