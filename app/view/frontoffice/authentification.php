<?php
session_start();
require_once '../../controller/utilisateurController.php';
require_once '../../controller/historiqueController.php';

$email = $_POST["email"];
$motDePasse = $_POST["password"];

$controller = new UtilisateurC();
$hcontroller = new HistoriqueC();

$utilisateur = $controller->authentifier($email, $motDePasse);

if ($utilisateur && $controller->estVerifie($email)) {
    $dateConnexion = date('Y-m-d H:i:s');
    $log = $hcontroller->ajouterHistorique($dateConnexion, '0000-00-00 00:00:00', 'login', 0, $utilisateur['idUtilisateur']);
    
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $utilisateur['idUtilisateur'];
    $_SESSION['user_name'] = $utilisateur['nom'];
    $_SESSION['user_email'] = $utilisateur['email'];
    $_SESSION['user_localisation'] = $utilisateur['localisation'];
    $_SESSION['user_role'] = $utilisateur['role'];
    
    if ($utilisateur['role'] == 'admin') {
        header("Location: ../backoffice/index.php");
    } else {
        header("Location: index.php");
    }
    die();
} else {
    header("Location: sign-in.php?error=invalid");
    die();
}
?>
