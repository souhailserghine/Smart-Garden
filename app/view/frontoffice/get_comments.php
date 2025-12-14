<?php
include_once '../config.php';  // Ajoutez cette ligne
include_once '../controller/CommentaireC.php';

header('Content-Type: application/json');

// Réponse de test simple
echo json_encode([
    [
        'idCommentaire' => 1,
        'contenuCommentaire' => 'Test de commentaire',
        'dateCommentaire' => '2024-01-15 10:00:00',
        'idPublication' => $_GET['idPublication'] ?? 1,
        'idUtilisateur' => 1,
        'nom' => 'Test',
        'prenom' => 'Utilisateur'
    ]
]);
?>