<?php
session_start();
require_once '../../controller/utilisateurController.php';

$controller = new UtilisateurC();

$id = $_POST['id'];
$controller->supprimerUtilisateur($id);

session_destroy();

header("Location: sign-in.html");
die();
