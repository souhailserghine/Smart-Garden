<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    $serveur = 'localhost';
    $user = 'root';
    $password = '';
    $database = 'smartgarden';
    
    $connexion = new mysqli($serveur, $user, $password, $database);
    
    if ($connexion->connect_error) {
        throw new Exception('Connexion échouée: ' . $connexion->connect_error);
    }
    
    $connexion->set_charset("utf8");
    
    // Récupérer TOUTES les plantes
    $query = "SELECT id_plante, nom_plante FROM plante ORDER BY nom_plante ASC";
    $result = $connexion->query($query);
    $plantes = [];
    
    while ($row = $result->fetch_assoc()) {
        $plantes[] = $row;
    }
    
    echo json_encode($plantes);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
