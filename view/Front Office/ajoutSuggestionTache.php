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
    
    if (!isset($_SESSION['idUtilisateur'])) {
        $_SESSION['idUtilisateur'] = 18;
    }
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $idUtilisateur = (int)$_SESSION['idUtilisateur'];
        $typeDosage = $connexion->real_escape_string($_POST['type_dosage'] ?? '');
        $quantite = (int)($_POST['quantite'] ?? 0);
        $modeDosage = $connexion->real_escape_string($_POST['mode_dosage'] ?? '');
        $dateDosage = $connexion->real_escape_string($_POST['date_dosage'] ?? date('Y-m-d'));
        $prochaineExecution = $connexion->real_escape_string($_POST['prochaineExecution'] ?? date('Y-m-d H:i:s'));
        $priorite = $connexion->real_escape_string($_POST['priorite'] ?? 'Moyen');
        $idPlante = !empty($_POST['id_plante']) ? (int)($_POST['id_plante']) : null;
        
        if (empty($typeDosage)) {
            echo json_encode(['success' => false, 'message' => 'Le type de dosage est obligatoire']);
            exit;
        }
        
        // Insérer dans suggestiontache avec tous les champs
        if ($idPlante !== null) {
            $query = "INSERT INTO suggestiontache 
                      (id_utilisateur, type_dosage, quantite, mode_dosage, date_dosage, prochaineExecution, priorite, id_plante, date_suggestion, statut) 
                      VALUES ($idUtilisateur, '$typeDosage', $quantite, '$modeDosage', '$dateDosage', '$prochaineExecution', '$priorite', $idPlante, NOW(), 'En attente')";
        } else {
            $query = "INSERT INTO suggestiontache 
                      (id_utilisateur, type_dosage, quantite, mode_dosage, date_dosage, prochaineExecution, priorite, date_suggestion, statut) 
                      VALUES ($idUtilisateur, '$typeDosage', $quantite, '$modeDosage', '$dateDosage', '$prochaineExecution', '$priorite', NOW(), 'En attente')";
        }
        
        if ($connexion->query($query)) {
            echo json_encode(['success' => true, 'message' => 'Suggestion de tâche ajoutée avec succès!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $connexion->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
    }
    
    $connexion->close();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
?>
