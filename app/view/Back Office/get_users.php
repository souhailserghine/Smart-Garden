<?php
require_once '../../controller/utilisateurController.php';

$controller = new UtilisateurC();

$searchNom = '';
$searchEmail = '';
$searchLocalisation = '';
$filterStatut = '';

if (isset($_GET['searchNom'])) {
    $searchNom = $_GET['searchNom'];
}
if (isset($_GET['searchEmail'])) {
    $searchEmail = $_GET['searchEmail'];
}
if (isset($_GET['searchLocalisation'])) {
    $searchLocalisation = $_GET['searchLocalisation'];
}
if (isset($_GET['filterStatut'])) {
    $filterStatut = $_GET['filterStatut'];
}

$users = $controller->afficher();

if ($searchNom || $searchEmail || $searchLocalisation || $filterStatut) {
    $filteredUsers = array();
    
    foreach ($users as $user) {
        $match = true;
        
        if ($searchNom && stripos($user['nom'], $searchNom) === false) {
            $match = false;
        }
        if ($searchEmail && stripos($user['email'], $searchEmail) === false) {
            $match = false;
        }
        if ($searchLocalisation && stripos($user['localisation'], $searchLocalisation) === false) {
            $match = false;
        }
        if ($filterStatut && $user['statut'] != $filterStatut) {
            $match = false;
        }
        
        if ($match) {
            $filteredUsers[] = $user;
        }
    }
    
    $users = $filteredUsers;
}


$editUser = null;
if (isset($_GET['edit'])) {
    $editUser = $controller->afficherParId($_GET['edit']);
}

$viewUser = null;
if (isset($_GET['view'])) {
    $viewUser = $controller->afficherParId($_GET['view']);
}
