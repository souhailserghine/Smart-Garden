<?php
header('Content-Type: application/json');

include '../../Controller/tacheC.php';
include '../../Model/tache.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Méthode non autorisée');
    }

    // Récupérer les données
    $type_dosage = $_POST['type_dosage'] ?? null;
    $quantite = $_POST['quantite'] ?? null;
    $mode_dosage = $_POST['mode_dosage'] ?? null;
    $date_dosage = $_POST['date_dosage'] ?? null;
    $derniereExecution = $_POST['derniereExecution'] ?? null;
    $prochaineExecution = $_POST['prochaineExecution'] ?? null;
    $estComplete = $_POST['estComplete'] ?? 0;
    $priorite = $_POST['priorite'] ?? null;
    $id_plante = $_POST['id_plante'] ?? null;

    // Valider les champs requis
    if (!$type_dosage || !$quantite || !$mode_dosage || !$date_dosage || !$priorite || !$id_plante) {
        throw new Exception('Veuillez remplir tous les champs obligatoires');
    }

    // Créer l'objet tâche (id_dosage = 0 pour l'auto-increment)
    $tache = new tache(
        0, // id_dosage (auto-increment)
        $type_dosage,
        $quantite,
        $mode_dosage,
        $date_dosage,
        $derniereExecution,
        $prochaineExecution,
        $estComplete,
        $priorite,
        $id_plante
    );

    // Ajouter la tâche
    $tacheC = new tacheC();
    $tacheC->ajouterTache($tache);

    echo json_encode([
        'success' => true,
        'message' => 'Tâche ajoutée avec succès'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
