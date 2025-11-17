<?php
require_once 'check_session.php';
require_once '../../controller/utilisateurController.php';

$controller = new UtilisateurC();

$id = $_POST['id'];
$statut = $_POST['statut'];

if ($controller->changerStatut($id, $statut)) {
    if ($statut == 'bloque') {
        $_SESSION['success_message'] = "Utilisateur bloqué avec succès";
    } else {
        $_SESSION['success_message'] = "Utilisateur débloqué avec succès";
    }
} else {
    $_SESSION['error_message'] = "Erreur lors du changement de statut";
}

header("Location: utilisateurs.php");
die();
