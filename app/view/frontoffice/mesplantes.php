<?php
require_once 'check_session.php';
include '../../controller/planteC.php';
$planteC = new planteC();

$userId = $_SESSION['idUtilisateur']; 
$mesPlantes = $planteC->listPlantesByUser($userId);
?>
