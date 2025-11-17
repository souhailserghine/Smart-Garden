<?php
 include '../../Controller/planteC.php';
    $nom=$_GET['nom'];
    $date=$_GET['date'];
    $niveau_humidite=$_GET['niveau_humidite'];
    $besoin_eau=$_GET['besoin_eau'];
    $etat_sante=$_GET['etat_sante'];
    $id=$_GET['id'];
    $plante=new planteC();

    $pl =new plante($id,$nom,$date,$niveau_humidite,$besoin_eau,$etat_sante,1);
    echo $p->getIdPlante();
    echo $p->getNomPlante();
    echo $p->getDateAjout();
    echo $p->getNiveauHumidite();
    echo $p->getBesoinEau();
    echo $p->getEtatSante();

    $plante->modifierPlante($pl);
