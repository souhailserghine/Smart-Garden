<?php
session_start();
include '../../Controller/planteC.php';
$planteC = new planteC();

$userId = $_SESSION['idUtilisateur']; 
$mesPlantes = $planteC->listPlantesByUser($userId);
?>
