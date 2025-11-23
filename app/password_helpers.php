<?php
function genererToken($bytes = 32) {
    return bin2hex(random_bytes($bytes));
}

function hasherToken($token) {
    return hash('sha256', $token);
}
?>