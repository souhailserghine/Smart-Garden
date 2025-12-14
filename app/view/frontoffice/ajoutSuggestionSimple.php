<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once 'check_session.php';

try {
    // Connexion directe
    $serveur = 'localhost';
    $user = 'root';
    $password = '';
    $database = 'smartgarden';
    
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
        $nomPlante = $connexion->real_escape_string($_POST['nom_plante'] ?? '');
        $niveauHumidite = (int)($_POST['niveau_humidite'] ?? 50);
        $besoinEau = (int)($_POST['besoin_eau'] ?? 0);
        $etatSante = $connexion->real_escape_string($_POST['etat_sante'] ?? 'Bon état');
        $temperature = (float)($_POST['temperature'] ?? 20.0);
        $imagePath = null;
        
        if (empty($nomPlante)) {
            echo json_encode(['success' => false, 'message' => 'Le nom de la plante est obligatoire']);
            exit;
        }
        
        // Gérer l'upload d'image
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
        
        // Insérer dans suggestionplante avec temperature et image
        $imagePath = $connexion->real_escape_string($imagePath);
        $query = "INSERT INTO suggestionplante 
                  (id_utilisateur, nom_plante, niveau_humidite, besoin_eau, etat_sante, temperature, image, date_suggestion, statut) 
                  VALUES ($idUtilisateur, '$nomPlante', $niveauHumidite, $besoinEau, '$etatSante', $temperature, '$imagePath', NOW(), 'En attente')";
        
        if ($connexion->query($query)) {
            echo json_encode(['success' => true, 'message' => 'Suggestion ajoutée avec succès!']);
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
