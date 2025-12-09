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
            $query = "SELECT s.*, u.nom as utilisateur_nom FROM suggestionplante s LEFT JOIN utilisateur u ON s.id_utilisateur = u.idUtilisateur ORDER BY s.date_suggestion DESC";
        } else {
            $query = "SELECT s.*, u.nom as utilisateur_nom FROM suggestionplante s LEFT JOIN utilisateur u ON s.id_utilisateur = u.idUtilisateur WHERE s.statut = '" . $connexion->real_escape_string($filter) . "' ORDER BY s.date_suggestion DESC";
        }
        
        $result = $connexion->query($query);
        $suggestions = [];
        
        while ($row = $result->fetch_assoc()) {
            $suggestions[] = $row;
        }
        
        echo json_encode(['success' => true, 'suggestions' => $suggestions]);
        
    } elseif ($action === 'accept') {
        $idSuggestion = (int)($_POST['id_suggestion'] ?? 0);
        
        // Récupérer la suggestion
        $query = "SELECT * FROM suggestionplante WHERE id_suggestion = $idSuggestion";
        $result = $connexion->query($query);
        $suggestion = $result->fetch_assoc();
        
        if ($suggestion) {
            // Créer la plante avec les champs qui existent dans plante
            $nomPlante = $connexion->real_escape_string($suggestion['nom_plante']);
            $idUtilisateur = (int)$suggestion['id_utilisateur'];
            $niveauHumidite = (int)$suggestion['niveau_humidite'];
            $besoinEau = (int)$suggestion['besoin_eau'];
            $etatSante = $connexion->real_escape_string($suggestion['etat_sante']);
            $temperature = (float)($suggestion['temperature'] ?? 20.0);
            
            // INSERT dans plante avec temperature
            $insertQuery = "INSERT INTO plante (nom_plante, etat_sante, niveau_humidite, besoin_eau, temperature, idUtilisateur, date_ajout) 
                           VALUES ('$nomPlante', '$etatSante', $niveauHumidite, $besoinEau, $temperature, $idUtilisateur, NOW())";
            
            if ($connexion->query($insertQuery)) {
                // Mettre à jour le statut
                $updateQuery = "UPDATE suggestionplante SET statut = 'Acceptée', date_traitement = NOW() WHERE id_suggestion = $idSuggestion";
                $connexion->query($updateQuery);
                
                echo json_encode(['success' => true, 'message' => 'Suggestion acceptée et plante créée!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur: ' . $connexion->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Suggestion non trouvée']);
        }
        
    } elseif ($action === 'reject') {
        $idSuggestion = (int)($_POST['id_suggestion'] ?? 0);
        $query = "UPDATE suggestionplante SET statut = 'Rejetée', date_traitement = NOW() WHERE id_suggestion = $idSuggestion";
        
        if ($connexion->query($query)) {
            echo json_encode(['success' => true, 'message' => 'Suggestion rejetée']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $connexion->error]);
        }
        
    } elseif ($action === 'count') {
        $query = "SELECT COUNT(*) as count FROM suggestionplante WHERE statut = 'En attente'";
        $result = $connexion->query($query);
        $row = $result->fetch_assoc();
        
        echo json_encode(['success' => true, 'count' => $row['count']]);
    }
    
    $connexion->close();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
?>
