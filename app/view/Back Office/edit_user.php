<?php
require_once 'check_session.php';
require_once '../../controller/utilisateurController.php';

$controller = new UtilisateurC();

$id = $_POST['id'];
$nom = $_POST['nom'];
$email = $_POST['email'];
$localisation = $_POST['localisation'];
$statut = $_POST['statut'];

$controller->modifierUtilisateur($id, $nom, $email, $localisation, $statut);

header("Location: utilisateurs.php");
die();
