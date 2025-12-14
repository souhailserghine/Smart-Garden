<?php
// commenter.php
session_start();
include_once '../../controller/CommentaireC.php';
include_once '../../model/Commentaire.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validation manuelle (pas de HTML5)
        $errors = [];
        
        if (!isset($_POST['idPublication']) || !is_numeric($_POST['idPublication']) || $_POST['idPublication'] <= 0) {
            $errors[] = "ID publication invalide";
        }
        
        if (!isset($_POST['idUtilisateur']) || !is_numeric($_POST['idUtilisateur']) || $_POST['idUtilisateur'] <= 0) {
            $errors[] = "ID utilisateur invalide";
        }
        
        if (!isset($_POST['contenuCommentaire']) || empty(trim($_POST['contenuCommentaire']))) {
            $errors[] = "Le commentaire ne peut pas être vide";
        } elseif (strlen(trim($_POST['contenuCommentaire'])) > 500) {
            $errors[] = "Le commentaire ne peut pas dépasser 500 caractères";
        }
        
        if (!empty($errors)) {
            $_SESSION['comment_errors'] = $errors;
            header("Location: liste.php?error=4");
            exit;
        }
        
        // Création de l'objet (POO)
        $commentaire = new Commentaire(
            0, // idCommentaire
            trim($_POST['contenuCommentaire']),
            date('Y-m-d H:i:s'),
            intval($_POST['idPublication']),
            intval($_POST['idUtilisateur'])
        );
        
        // Utilisation du contrôleur (MVC)
        $commentaireC = new CommentaireC();
        $result = $commentaireC->ajouterCommentaire($commentaire);
        
        if ($result) {
            header("Location: liste.php?success=4&open_comments=" . intval($_POST['idPublication']));
        } else {
            header("Location: liste.php?error=4");
        }
        
    } catch (Exception $e) {
        $_SESSION['comment_errors'] = ["Erreur technique: " . $e->getMessage()];
        header("Location: liste.php?error=4");
    }
    exit;
} else {
    header("Location: liste.php");
    exit;
}
?>