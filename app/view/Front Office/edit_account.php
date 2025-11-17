<?php
require_once 'check_session.php';
require_once '../../controller/utilisateurController.php';

$controller = new UtilisateurC();

$id = $_POST['id'];
$nom = $_POST['nom'];
$email = $_POST['email'];
$localisation = $_POST['localisation'];
$statut = $_POST['statut'];

$result = $controller->modifierUtilisateur($id, $nom, $email, $localisation, $statut);
$_SESSION['user_name'] = $nom;
$_SESSION['user_email'] = $email;
header("Location: settings.php");
die();
