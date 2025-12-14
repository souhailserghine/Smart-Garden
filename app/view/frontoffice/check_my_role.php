<?php
session_start();

echo "<h2>Current Session Information</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .info-box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
    .label { font-weight: bold; color: #333; }
    .value { color: #007bff; }
    .admin { color: #28a745; font-weight: bold; }
    .user { color: #ffc107; font-weight: bold; }
    .error { color: #dc3545; }
</style>";

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    echo "<div class='info-box'>";
    echo "<p><span class='label'>Logged In:</span> <span class='value'>✓ Yes</span></p>";
    echo "<p><span class='label'>User ID:</span> <span class='value'>" . htmlspecialchars($_SESSION['user_id'] ?? 'Not set') . "</span></p>";
    echo "<p><span class='label'>User ID (alt):</span> <span class='value'>" . htmlspecialchars($_SESSION['idUtilisateur'] ?? 'Not set') . "</span></p>";
    echo "<p><span class='label'>Username:</span> <span class='value'>" . htmlspecialchars($_SESSION['user_name'] ?? 'Not set') . "</span></p>";
    echo "<p><span class='label'>Email:</span> <span class='value'>" . htmlspecialchars($_SESSION['user_email'] ?? 'Not set') . "</span></p>";
    
    $role = $_SESSION['user_role'] ?? 'Not set';
    $roleClass = ($role === 'admin') ? 'admin' : 'user';
    echo "<p><span class='label'>Role:</span> <span class='$roleClass'>" . htmlspecialchars($role) . "</span></p>";
    
    echo "</div>";
    
    if ($role === 'admin') {
        echo "<div class='info-box admin'>";
        echo "<h3>✓ You have ADMIN access</h3>";
        echo "<p>You can access the backoffice.</p>";
        echo "<p><a href='../backoffice/index.php'>Go to Backoffice</a></p>";
        echo "</div>";
    } else {
        echo "<div class='info-box user'>";
        echo "<h3>⚠ You are a REGULAR USER</h3>";
        echo "<p>You do NOT have admin privileges.</p>";
        echo "<p>You cannot access the backoffice.</p>";
        echo "</div>";
    }
    
    echo "<div class='info-box'>";
    echo "<h3>All Session Variables:</h3>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    echo "</div>";
    
} else {
    echo "<div class='info-box error'>";
    echo "<h3>✗ You are NOT logged in</h3>";
    echo "<p><a href='sign-in.php'>Go to Login</a></p>";
    echo "</div>";
}

echo "<p><a href='index.html'>← Back to Home</a></p>";
?>
