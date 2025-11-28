<?php
// On s'assure qu'aucune erreur PHP ne soit affichée au client.
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

// Définir l'en-tête pour indiquer que la réponse est au format JSON
header('Content-Type: application/json; charset=utf-8');

include '../../Controller/tacheC.php';
include '../../Model/tache.php';

$tacheC = new tacheC();

// Fonction utilitaire pour envoyer une réponse JSON et arrêter le script
function sendJsonResponse($success, $message, $data = []) {
    echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // --- 1. VALIDATION DES DONNÉES D'ENTRÉE ---
        $required_fields = ['type_dosage', 'quantite', 'mode_dosage', 'date_dosage',
                            'derniereExecution', 'prochaineExecution', 'estComplete',
                            'priorite', 'id_plante'];
                            
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                sendJsonResponse(false, "Le champ '{$field}' est manquant ou vide.");
            }
        }
        
        // Validation basique des types
        if (!is_numeric($_POST['quantite']) || !is_numeric($_POST['estComplete']) || !is_numeric($_POST['priorite']) || !is_numeric($_POST['id_plante'])) {
             sendJsonResponse(false, "Les valeurs 'quantité', 'état' (estComplete), 'priorité' et 'ID Plante' doivent être numériques.");
        }
        // ------------------------------------------

        // Création de l'objet Tache
        $tache = new Tache(
            null, // L'ID Tâche est null car il sera auto-incrémenté
            $_POST['type_dosage'],
            (float)$_POST['quantite'],
            $_POST['mode_dosage'],
            $_POST['date_dosage'],
            $_POST['derniereExecution'],
            $_POST['prochaineExecution'],
            (int)$_POST['estComplete'], // Convertir en entier
            (int)$_POST['priorite'],    // Convertir en entier
            (int)$_POST['id_plante']    // Convertir en entier
        );

        // --- 2. EXÉCUTION DE L'AJOUT EN BASE DE DONNÉES ---
        $result = $tacheC->ajouterTache($tache);

        // --- 3. VÉRIFICATION DU RÉSULTAT ET RÉPONSE ---
        if ($result !== false) {
             sendJsonResponse(true, 'Tâche ajoutée avec succès');
        } else {
             sendJsonResponse(false, 'Erreur inconnue lors de l\'insertion de la tâche en base de données.');
        }

    } catch (Exception $e) {
        // Gestion des erreurs internes
        sendJsonResponse(false, "Erreur serveur interne lors de l'ajout de la tâche : " . $e->getMessage());
    }
} else {
    // Si la requête n'est pas POST (méthode incorrecte)
    sendJsonResponse(false, 'Méthode de requête non autorisée.', [], 405);
}
?>