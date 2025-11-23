<?php
require 'db.php';
require 'vendor/autoload.php';
require 'mailer.php';
require 'password_reset_helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $msg = "Reset link sent if account exists.";

    $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    if ($user) {
        $stmt2 = $conn->prepare("SELECT reset_requested_at FROM users WHERE email=?");
        $stmt2->bind_param('s', $email);
        $stmt2->execute();
        $r = $stmt2->get_result()->fetch_assoc();
        
        if ($r && $r['reset_requested_at']) {
            $last = strtotime($r['reset_requested_at']);
            if (time() - $last < 60) {
                echo $msg;
                exit;
            }
        }

        $token = generateToken(32);
        saveResetToken($conn, $email, $token, 3600);
        $resetUrl = 'http://localhost/yourproject/reset_password.php?token=' . urlencode($token) . '&email=' . urlencode($email);

        $subject = 'Reset your password';
        $html = "<p>Hi " . htmlspecialchars($user['name']) . ",</p>
            <p>Click the link below to reset your password. Link expires in 1 hour.</p>
            <p><a href=\"{$resetUrl}\">Reset password</a></p>";

        $send = envoyerEmail($email, $user['name'], $subject, $html);

        if (!$send['success']) {
            error_log("Password reset failed for {$email}");
        }
    }

    echo $msg;
}
?>

<form method="post">
  <input type="email" name="email" placeholder="Your email">
  <button type="submit">Send reset link</button>
</form>
