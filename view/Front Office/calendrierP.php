<?php
header('Content-Type: application/json');

session_start();

include '../../config.php';
include '../../Controller/tacheC.php';
include '../../Controller/planteC.php';

$userId = $_SESSION['idUtilisateur'] ?? null;

if (!$userId) {
    echo json_encode([]);
    exit;
}

// Récupérer toutes les tâches
$tacheC = new tacheC();
$allTaches = $tacheC->listTaches();

// Récupérer les plantes de l'utilisateur
$planteC = new planteC();
$userPlantes = $planteC->listPlantesByUser($userId);
$plantesIds = array_column($userPlantes, 'id_plante');

$events = [];

foreach ($allTaches as $tache) {
    // Vérifier si la tâche appartient à une plante de l'utilisateur
    if (!in_array($tache['id_plante'], $plantesIds)) {
        continue;
    }

    // Déterminer la couleur selon le statut
    $bgColor = ($tache['estComplete'] == 1) ? '#28a745' : '#ffc107';
    if ($tache['priorite'] == 'Élevée' || $tache['priorite'] == 3) {
        $bgColor = '#dc3545';
    }

    $event = [
        'id' => $tache['id_dosage'],
        'title' => $tache['type_dosage'] . ' - ' . $tache['quantite'] . ' ' . $tache['mode_dosage'],
        'start' => $tache['prochaineExecution'] ?: $tache['date_dosage'],
        'backgroundColor' => $bgColor,
        'borderColor' => $bgColor,
        'extendedProps' => [
            'id_dosage' => $tache['id_dosage'],
            'type' => $tache['type_dosage'],
            'quantite' => $tache['quantite'],
            'mode' => $tache['mode_dosage'],
            'date_dosage' => $tache['date_dosage'],
            'derniere_execution' => $tache['derniereExecution'],
            'prochaine_execution' => $tache['prochaineExecution'],
            'priorite' => (is_numeric($tache['priorite'])) ? 
                ($tache['priorite'] == 1 ? 'Faible' : ($tache['priorite'] == 2 ? 'Moyen' : 'Élevée')) : 
                $tache['priorite'],
            'complete' => $tache['estComplete'],
            'id_plante' => $tache['id_plante']
        ]
    ];

    $events[] = $event;
}

echo json_encode($events);
?>
