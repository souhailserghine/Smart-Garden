<?php
include '../../Controller/planteC.php';
include '../../Model/plante.php';

$planteC=new planteC();
$p=new plante(1,$_POST['nom'],$_POST['date'],$_POST['niveau_humidite'],$_POST['besoin_eau'],$_POST['etat_sante'],$_POST['idUtilisateur']);
$planteC->ajouterPlante($p);
$plantes = $planteC->listPlantes();
header('Location: add.php');