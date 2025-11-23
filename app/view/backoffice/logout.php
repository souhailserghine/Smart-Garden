<?php
session_start();

// Ensure the Historique controller is available
require_once __DIR__ . '/../../controller/historiqueController.php';

// If there's no logged-in user, destroy any session and redirect to sign-in
if (!isset($_SESSION['user_id'])) {
    session_destroy();
    header("Location: ../frontoffice/sign-in.php");
    exit();
}

$hcontroller = new HistoriqueC();

// Use a direct timestamp for deconnexion
$dateDeconnexion = date('Y-m-d H:i:s');

// Find the open login session to calculate duration
$sessionouverte = $hcontroller->sessionOuverte($_SESSION['user_id']);
if ($sessionouverte) {
    $duree = $hcontroller->calculerDuree($sessionouverte['dateConnexion'], $dateDeconnexion);
    // Add a new logout record
    $log = $hcontroller->ajouterHistorique(
        $dateDeconnexion,
        $dateDeconnexion,
        'logout',
        $duree,
        $_SESSION['user_id']
    );
}

session_destroy();

header("Location: ../frontoffice/sign-in.php");
exit();
?>
