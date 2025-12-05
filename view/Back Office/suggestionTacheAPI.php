<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

session_start();

try {
    // Connexion directe
    $serveur = 'localhost';
    $user = 'root';
    $password = '';
    $database = 'smart_garden';
    
    $connexion = new mysqli($serveur, $user, $password, $database);
    
    if ($connexion->connect_error) {
        throw new Exception('Connexion échouée: ' . $connexion->connect_error);
    }
    
    $connexion->set_charset("utf8");
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'list') {
        $filter = $_POST['filter'] ?? 'En attente';
        
        if ($filter === 'Toutes') {
            $query = "SELECT s.*, u.nom as utilisateur_nom FROM suggestiontache s LEFT JOIN utilisateur u ON s.id_utilisateur = u.idUtilisateur ORDER BY s.date_suggestion DESC";
        } else {
            $query = "SELECT s.*, u.nom as utilisateur_nom FROM suggestiontache s LEFT JOIN utilisateur u ON s.id_utilisateur = u.idUtilisateur WHERE s.statut = '" . $connexion->real_escape_string($filter) . "' ORDER BY s.date_suggestion DESC";
        }
        
        $result = $connexion->query($query);
        $suggestions = [];
        
        while ($row = $result->fetch_assoc()) {
            $suggestions[] = $row;
        }
        
        echo json_encode(['success' => true, 'suggestions' => $suggestions]);
        
    } elseif ($action === 'accept') {
        $idSuggestion = (int)($_POST['id_suggestion'] ?? 0);
        $idPlante = (int)($_POST['id_plante'] ?? 0);
        
        if ($idPlante <= 0) {
            echo json_encode(['success' => false, 'message' => 'Plante invalide']);
            exit;
        }
        
        // Récupérer la suggestion
        $query = "SELECT * FROM suggestiontache WHERE id_suggestion = $idSuggestion";
        $result = $connexion->query($query);
        $suggestion = $result->fetch_assoc();
        
        if ($suggestion) {
            // Créer la tâche avec TOUS les champs et la plante choisie par l'admin
            $typeDosage = $connexion->real_escape_string($suggestion['type_dosage']);
            $quantite = (int)$suggestion['quantite'];
            $modeDosage = $connexion->real_escape_string($suggestion['mode_dosage']);
            $dateDosage = $connexion->real_escape_string($suggestion['date_dosage']);
            $derniereExecution = date('Y-m-d H:i:s');
            $prochaineExecution = $connexion->real_escape_string($suggestion['prochaineExecution']);
            $priorite = $connexion->real_escape_string($suggestion['priorite']);
            
            // INSERT complet dans tache avec l'id_plante sélectionné
            $insertQuery = "INSERT INTO tache (type_dosage, quantite, mode_dosage, date_dosage, derniereExecution, prochaineExecution, estComplete, priorite, id_plante) 
                           VALUES ('$typeDosage', $quantite, '$modeDosage', '$dateDosage', '$derniereExecution', '$prochaineExecution', 0, '$priorite', $idPlante)";
            
            if ($connexion->query($insertQuery)) {
                // Mettre à jour le statut
                $updateQuery = "UPDATE suggestiontache SET statut = 'Acceptée', date_traitement = NOW() WHERE id_suggestion = $idSuggestion";
                $connexion->query($updateQuery);
                
                echo json_encode(['success' => true, 'message' => 'Suggestion acceptée et tâche créée!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur: ' . $connexion->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Suggestion non trouvée']);
        }
        
    } elseif ($action === 'reject') {
        $idSuggestion = (int)($_POST['id_suggestion'] ?? 0);
        $query = "UPDATE suggestiontache SET statut = 'Rejetée', date_traitement = NOW() WHERE id_suggestion = $idSuggestion";
        
        if ($connexion->query($query)) {
            echo json_encode(['success' => true, 'message' => 'Suggestion rejetée']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $connexion->error]);
        }
        
    } elseif ($action === 'count') {
        $query = "SELECT COUNT(*) as count FROM suggestiontache WHERE statut = 'En attente'";
        $result = $connexion->query($query);
        $row = $result->fetch_assoc();
        
        echo json_encode(['success' => true, 'count' => $row['count']]);
    }
    
    $connexion->close();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
?>
