<?php
include '../../controller/PublicationC.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $publicationId = $_POST['idPublication'];
    $userId = $_POST['idUtilisateur'];
    
    $publicationC = new PublicationC();
    
    // Ajouter le like (vous devrez implémenter cette méthode dans PublicationC)
    $result = $publicationC->ajouterLike($publicationId, $userId);
    
    if ($result) {
        header('Location: liste.php?success=4');
    } else {
        header('Location: liste.php?error=2');
    }
    exit();
}
?>