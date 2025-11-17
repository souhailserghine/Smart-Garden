<?php
require_once '../../controller/utilisateurController.php';

$name = $_POST["nom"];
$email = $_POST["email"];
$localisation = $_POST["localisation"];
$motDePasse = $_POST["motDePasse"];

$controller = new UtilisateurC();

$utilisateur = $controller->ajouterUtilisateur($name, $email, $motDePasse, $localisation, "actif");
if ($utilisateur) {
    $receiver = $email;
    $subject = "Welcome to SmartGarden - Registration Confirmation";
    $body = "Hi " . $name . ",\n\nWelcome to SmartGarden!\n\nYour account has been successfully created.\n\nEmail: " . $email . "\n\nPlease sign in to get started.\n\nBest regards,\nSmartGarden Team";
    $headers = "From: noreply@smartgarden.com\r\nContent-Type: text/plain; charset=UTF-8";
    
    mail($receiver, $subject, $body, $headers);
    
    header("Location: sign-in.html");
    exit();
} else {
    echo "Registration failed. Please try again.";
}
