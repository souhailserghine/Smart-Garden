<?php
include 'controller/PublicationC.php';
include 'model/Publication.php';

if ($_POST) {
    $titre = $_POST['titre'];
    $contenu = $_POST['contenu'];
    $auteur_id = $_POST['auteur_id'];
    
    if (!empty($titre) && !empty($contenu)) {
        $publication = new Publication($titre, $contenu, $auteur_id);
        $publicationC = new PublicationC();
        $result = $publicationC->addPublication($publication);
        
        if ($result) {
            header("Location: liste.php?success=1");
        } else {
            header("Location: add.html?error=0");
        }
    } else {
        header("Location: add.html?error=2");
    }
    exit;
}
?>