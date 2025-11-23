<?php
require_once 'check_session.php';
require_once '../../controller/utilisateurController.php';

$controller = new UtilisateurC();

$nom = $_POST['nom'];
$email = $_POST['email'];
$localisation = $_POST['localisation'];
$motDePasse = $_POST['motDePasse'];
$statut = $_POST['statut'];
$controller->ajouterUtilisateur($nom, $email, $motDePasse, $localisation, $statut);

header("Location: utilisateurs.php");
die();
