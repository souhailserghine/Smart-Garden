<?php
include '../../Controller/planteC.php';
include '../../Model/plante.php';

$planteC = new planteC();

// Crée l'objet plante avec les données du formulaire
$p = new plante(
    $_POST['id_plante'],      // id de la plante
    $_POST['nom_plante'],
    $_POST['date_ajout'],
    $_POST['niveau_humidite'],
    $_POST['besoin_eau'],
    $_POST['etat_sante'],
    $_POST['idUtilisateur']
);

// Appelle la méthode avec l'objet et l'id
$planteC->modifierPlante($p, $_POST['id_plante']);

// Redirige vers la liste
header('Location: plantes.php');

