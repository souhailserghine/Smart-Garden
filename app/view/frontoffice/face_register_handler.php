<?php
session_start();
require_once '../../controller/utilisateurController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register_face.php");
    exit();
}

$email = $_POST['email'];
$faceData = $_POST['face'];

$ch = curl_init('http://localhost:5000/register');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['username'=>$email,'face'=>$faceData]));
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    header("Location: register_face.php?error=Connection error: " . urlencode($curlError));
    exit();
}

if ($response === false || $httpCode != 200) {
    header("Location: register_face.php?error=Server error (HTTP $httpCode)");
    exit();
}

$result = json_decode($response, true);
if ($result && isset($result['success']) && $result['success']) {
    $_SESSION['message'] = "Face registered successfully! You can now login with your face.";
    header("Location: sign-in.php");
} else {
    $message = $result['message'] ?? 'Unknown error';
    header("Location: register_face.php?error=" . urlencode($message));
}
exit();
?>
