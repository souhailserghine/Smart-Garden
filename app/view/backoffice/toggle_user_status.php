<?php
require_once 'check_session.php';
require_once '../../controller/utilisateurController.php';

$controller = new UtilisateurC();

$id = $_POST['id'];
$statut = $_POST['statut'];

$controller->changerStatut($id, $statut);

header("Location: utilisateurs.php");
die();
