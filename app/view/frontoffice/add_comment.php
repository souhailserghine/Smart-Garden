<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['idUtilisateur'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour commenter']);
    exit;
}

// Include necessary files
include_once '../../config.php';
include_once '../../controller/CommentaireC.php';

// Check if required data is present
if (!isset($_POST['idPublication']) || !isset($_POST['contenuCommentaire'])) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit;
}

$idPublication = intval($_POST['idPublication']);
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
    
    // Create new comment
    $commentaire = new Commentaire(
        null, // idCommentaire will be auto-generated
        $contenuCommentaire,
        date('Y-m-d H:i:s'),
        $idPublication,
        $idUtilisateur
    );
    
    // Add comment to database
    $result = $commentaireC->ajouterCommentaire($commentaire);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Commentaire ajouté avec succès']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout du commentaire']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
?>
