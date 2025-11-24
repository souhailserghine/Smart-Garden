<?php
echo "ok";
exit;
?>
<?php
header('Content-Type: application/json; charset=utf-8');

include '../../Controller/tacheC.php';
include '../../Model/tache.php';

$tacheC = new tacheC();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Création de l'objet Tache
        $tache = new Tache(
            null, // id auto-incrémenté
            $_POST['type_dosage'],
            $_POST['quantite'],
            $_POST['mode_dosage'],
            $_POST['date_dosage'],
            $_POST['derniereExecution'],
            $_POST['prochaineExecution'],
            $_POST['estComplete'],
            $_POST['priorite'],
            $_POST['id_plante']
        );

        // Ajout dans la base
        $result = $tacheC->ajouterTache($tache);

        echo json_encode([
            'success' => true,
            'message' => 'Tâche ajoutée avec succès'
        ]);
        exit;

    } catch (Exception $e) {

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
        exit;
    }
}

echo json_encode([
    'success' => false,
    'message' => 'Méthode non autorisée'
]);
?>
