<?php
// update_comment.php
session_start();

// Chemin correct vers le contrôleur
include_once __DIR__ . '/../../controller/commentaireC.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idCommentaire = $_POST['idCommentaire'];
    $idPublication = $_POST['idPublication'];
    $contenuCommentaire = trim($_POST['contenuCommentaire']);
    
    // Validation
    if (!empty($contenuCommentaire) && !empty($idCommentaire) && !empty($idPublication)) {
        try {
            $commentaireC = new CommentaireC();
            $success = $commentaireC->modifierCommentaire($idCommentaire, $contenuCommentaire);
            
            if ($success) {
                header("Location: liste.php?success=5&open_comments=" . $idPublication);
            } else {
                header("Location: liste.php?error=5");
            }
        } catch (Exception $e) {
            header("Location: liste.php?error=5");
        }
    } else {
        header("Location: liste.php?error=5");
    }
    exit;
} else {
    header("Location: liste.php");
    exit;
}
?>
