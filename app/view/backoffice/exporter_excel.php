<?php
require_once 'check_session.php';
require_once '../../controller/utilisateurController.php';

$controller = new UtilisateurC();
$users = $controller->afficher();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=utilisateurs_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');

fputcsv($output, array('ID', 'Nom', 'Email', 'Localisation', 'Date Inscription', 'Statut'));

foreach ($users as $user) {
    fputcsv($output, array(
        $user['idUtilisateur'],
        $user['nom'],
        $user['email'],
        $user['localisation'] ,
        date('d M Y', strtotime($user['dateInscription'])),
        $user['statut']
    ));
}

fclose($output);
exit;

