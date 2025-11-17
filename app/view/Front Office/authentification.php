<?php
session_start();
require_once '../../controller/utilisateurController.php';

$email = $_POST["email"];
$motDePasse = $_POST["password"];

$controller = new UtilisateurC();

$utilisateur = $controller->authentifier($email, $motDePasse);

if ($utilisateur) {
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $utilisateur['idUtilisateur'];
    $_SESSION['user_name'] = $utilisateur['nom'];
    $_SESSION['user_email'] = $utilisateur['email'];
    $_SESSION['user_localisation'] = $utilisateur['localisation'];
    $_SESSION['user_role'] = $utilisateur['role'];
    
    if ($utilisateur['role'] == 'admin') {
        header("Location: ../Back Office/index.php");
    } else {
        header("Location: index.php");
    }
    die();
} else {
    header("Location: sign-in.html");
    die();
}
?>
