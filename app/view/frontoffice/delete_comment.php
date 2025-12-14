<?php
session_start();
include '../../controller/CommentaireC.php';
include '../../config.php';

// Check if this is an AJAX request (from publications.php)
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// For AJAX requests, return JSON
if ($isAjax && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    // Check if user is logged in
    if (!isset($_SESSION['idUtilisateur'])) {
        echo json_encode(['success' => false, 'message' => 'Vous devez être connecté']);
        exit;
    }
    
    // Check if required data is present
    if (!isset($_POST['idCommentaire'])) {
        echo json_encode(['success' => false, 'message' => 'Données manquantes']);
        exit;
    }
    
    $idCommentaire = intval($_POST['idCommentaire']);
    $idUtilisateur = $_SESSION['idUtilisateur'];
    
    try {
        // Verify that the comment belongs to the current user
        $db = config::getConnexion();
        $query = "SELECT idUtilisateur FROM commentaire WHERE idCommentaire = :idCommentaire";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':idCommentaire', $idCommentaire, PDO::PARAM_INT);
        $stmt->execute();
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$comment) {
            echo json_encode(['success' => false, 'message' => 'Commentaire introuvable']);
            exit;
        }
        
        if ($comment['idUtilisateur'] != $idUtilisateur) {
            echo json_encode(['success' => false, 'message' => 'Vous ne pouvez supprimer que vos propres commentaires']);
            exit;
        }
        
        // Delete comment
        $commentaireC = new CommentaireC();
        $result = $commentaireC->supprimerCommentaire($idCommentaire);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Commentaire supprimé avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
    }
    exit;
}

// Original redirect-based logic for non-AJAX requests (liste.php compatibility)
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