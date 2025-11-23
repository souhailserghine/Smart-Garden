<?php
require_once '../../controller/utilisateurController.php';
require_once '../../password_helpers.php';
require_once '../../mailer.php';


$name = $_POST["nom"];
$email = $_POST["email"];
$localisation = $_POST["localisation"];
$motDePasse = $_POST["motDePasse"];

$controller = new UtilisateurC();

$utilisateur = $controller->ajouterUtilisateur($name, $email, $motDePasse, $localisation, "actif");
if ($utilisateur) {
    $token = genererToken(32);
    $tokenHash = hasherToken($token);
    $controller->sauvegarderTokenReset($email, $tokenHash, 3600);
    $verifyUrl = 'http://localhost/website/app/view/frontoffice/verify_account.php?token=' . urlencode($token) . '&email=' . urlencode($email);
    
    $sujet = "Welcome to SmartGarden - Registration Confirmation";
    $html = "
        <p>Hi " . htmlspecialchars($name) . ",</p>
        <p>Welcome to SmartGarden!</p>
        <p>Your account has been successfully created with email: <strong>" . htmlspecialchars($email) . "</strong></p>
        <p>Please click the button below to verify your account:</p>
        <p><a href=\"{$verifyUrl}\" style=\"background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;\">Verify My Account</a></p>
        <p>If you did not create this account, you can ignore this email.</p>
        <p>Best regards,<br>SmartGarden Team</p>
    ";
    
    $envoi = envoyerEmail($email, $name, $sujet, $html);
    
    if (!$envoi['succes']) {
        error_log("Failed to send verification email for {$email}: " . ($envoi['erreur'] ?? 'unknown'));
    }
    
    header("Location: sign-in.php");
    exit();
} else {
    echo "Registration failed. Please try again.";
}
