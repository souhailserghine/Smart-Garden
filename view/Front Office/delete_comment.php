<?php
include '../../controller/CommentaireC.php';

if (isset($_GET["id"])) {
    $commentaireC = new CommentaireC();
    $result = $commentaireC->supprimerCommentaire($_GET["id"]);
    
    // Rediriger vers la page précédente
    $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
    
    if ($result) {
        header('Location: ' . $referer . '?success_comment=1');
    } else {
        header('Location: ' . $referer . '?error_comment=0');
    }
} else {
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php') . '?error_comment=1');
}
exit;
?>