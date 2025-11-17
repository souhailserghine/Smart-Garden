<?php
require_once 'check_session.php';
require_once '../../controller/utilisateurController.php';

$controller = new UtilisateurC();

$id = $_POST['id'];
$controller->supprimerUtilisateur($id);

header("Location: utilisateurs.php");
die();
