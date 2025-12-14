<?php
require_once 'check_session.php';
include_once '../../controller/publicationC.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idPublication'])) {
    $publicationId = intval($_POST['idPublication']);
    
    // Get current user ID from session
    if (!isset($_SESSION['idUtilisateur'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Vous devez être connecté pour aimer une publication'
        ]);
        exit;
    }
    
    $userId = $_SESSION['idUtilisateur'];
    $publicationC = new PublicationC();
    
    try {
        // Check if user has already liked this publication
        if ($publicationC->aUtilisateurAime($publicationId, $userId)) {
            echo json_encode([
                'success' => false,
                'alreadyLiked' => true,
                'message' => 'Vous avez déjà aimé cette publication'
            ]);
            exit;
        }
        
        // Add like with user tracking
        $success = $publicationC->ajouterLikeUtilisateur($publicationId, $userId);
        
        if ($success) {
            // Get updated publication to return new count
            $publication = $publicationC->getPublication($publicationId);
            
            echo json_encode([
                'success' => true,
                'nbLikes' => $publication ? $publication->getNbLikes() : 0,
                'message' => 'Publication aimée avec succès'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout du like'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Requête invalide'
    ]);
}
?>
