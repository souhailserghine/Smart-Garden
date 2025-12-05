<?php
session_start();

// Erreur reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once '../../config.php';
include_once '../../Controller/suggestionC.php';

header('Content-Type: application/json');

if (!isset($_SESSION['idUtilisateur'])) {
    $_SESSION['idUtilisateur'] = 18;
}

try {
    $suggestionC = new suggestionC();
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $nomPlante = $_POST['nom_plante'] ?? '';
        $typePlante = $_POST['type_plante'] ?? '';
        $description = $_POST['description'] ?? '';
        $idUtilisateur = $_SESSION['idUtilisateur'];
        
        if (empty($nomPlante) || empty($typePlante)) {
            echo json_encode(['success' => false, 'message' => 'Veuillez remplir tous les champs obligatoires']);
            exit;
        }
        
        // Traiter l'image si uploadée
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $uploadDir = '../../view/image/suggestions/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $filename = uniqid() . '_' . basename($_FILES['image']['name']);
            $filepath = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
                $imagePath = '../image/suggestions/' . $filename;
            }
        }
        
        $result = $suggestionC->addSuggestion($idUtilisateur, $nomPlante, $typePlante, $description, $imagePath);
        echo json_encode($result);
        
    } elseif ($action === 'list') {
        $filter = $_POST['filter'] ?? 'Toutes';
        $suggestions = $suggestionC->listSuggestions($filter);
        echo json_encode(['success' => true, 'suggestions' => $suggestions]);
        
    } elseif ($action === 'accept') {
        $idSuggestion = $_POST['id_suggestion'] ?? null;
        $result = $suggestionC->acceptSuggestion($idSuggestion);
        echo json_encode($result);
        
    } elseif ($action === 'reject') {
        $idSuggestion = $_POST['id_suggestion'] ?? null;
        $result = $suggestionC->rejectSuggestion($idSuggestion);
        echo json_encode($result);
        
    } elseif ($action === 'delete') {
        $idSuggestion = $_POST['id_suggestion'] ?? null;
        $result = $suggestionC->deleteSuggestion($idSuggestion);
        echo json_encode($result);
    } else {
        echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
?>
