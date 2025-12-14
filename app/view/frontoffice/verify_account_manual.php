<?php
// Manual account verification script
require_once '../../config.php';
require_once '../../controller/utilisateurController.php';

if (isset($_GET['email'])) {
    $email = $_GET['email'];
    $controller = new UtilisateurC();
    
    $result = $controller->verifierCompte($email);
    
    if ($result) {
        echo "<h2 style='color: green;'>✓ Account Verified Successfully!</h2>";
        echo "<p>The account for <strong>" . htmlspecialchars($email) . "</strong> has been verified.</p>";
        echo "<p><a href='sign-in.php'>Go to Login Page</a></p>";
    } else {
        echo "<h2 style='color: red;'>✗ Verification Failed</h2>";
        echo "<p>Could not verify the account.</p>";
    }
} else {
    echo "<h2>Error</h2>";
    echo "<p>No email provided.</p>";
}
?>
