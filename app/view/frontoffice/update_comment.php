<?php
// update_comment.php
session_start();

// Chemin correct vers le contrôleur
include_once __DIR__ . '/../../controller/CommentaireC.php';
include_once __DIR__ . '/../../model/Commentaire.php';
include_once __DIR__ . '/../../config.php';

// Check if this is an AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// For AJAX requests, return JSON
if ($isAjax) {
    header('Content-Type: application/json');
    
    // Check if user is logged in
    if (!isset($_SESSION['idUtilisateur'])) {
        echo json_encode(['success' => false, 'message' => 'Vous devez être connecté']);
        exit;
    }
    
    // Check if required data is present
    if (!isset($_POST['idCommentaire']) || !isset($_POST['contenuCommentaire'])) {
        echo json_encode(['success' => false, 'message' => 'Données manquantes']);
        exit;
    }
    
    $idCommentaire = intval($_POST['idCommentaire']);
    $contenuCommentaire = trim($_POST['contenuCommentaire']);
    $idUtilisateur = $_SESSION['idUtilisateur'];
    
    // Validate comment content
    if (empty($contenuCommentaire)) {
        echo json_encode(['success' => false, 'message' => 'Le commentaire ne peut pas être vide']);
        exit;
    }
    
    if (strlen($contenuCommentaire) > 1000) {
        echo json_encode(['success' => false, 'message' => 'Le commentaire est trop long (maximum 1000 caractères)']);
        exit;
    }
    
    try {
        $commentaireC = new CommentaireC();
        
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
            echo json_encode(['success' => false, 'message' => 'Vous ne pouvez modifier que vos propres commentaires']);
            exit;
        }
        
        // Update comment
        $commentaire = new Commentaire(
            $idCommentaire,
            $contenuCommentaire,
            date('Y-m-d H:i:s'),
            isset($_POST['idPublication']) ? intval($_POST['idPublication']) : null,
            $idUtilisateur
        );
        
        $result = $commentaireC->modifierCommentaire($commentaire, $idCommentaire);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Commentaire modifié avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la modification']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
    }
    exit;
}

// Original redirect-based logic for non-AJAX requests (liste.php compatibility)
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
