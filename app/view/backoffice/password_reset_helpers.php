<?php
// password_reset_helpers.php
function generateToken(int $bytes = 32): string {
    return bin2hex(random_bytes($bytes)); // 64 chars for 32 bytes
}

function hashToken(string $token): string {
    // store sha256 hash of token (not raw token)
    return hash('sha256', $token);
}

function saveResetToken(mysqli $conn, string $email, string $token, int $expirySeconds = 3600): bool {
    $hash = hashToken($token);
    $expires = date('Y-m-d H:i:s', time() + $expirySeconds);
    $now = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("UPDATE users SET reset_token_hash=?, reset_expires=?, reset_requested_at=? WHERE email=?");
    $stmt->bind_param('ssss', $hash, $expires, $now, $email);
    return $stmt->execute();
}
