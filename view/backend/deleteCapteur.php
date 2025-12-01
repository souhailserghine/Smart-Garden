<?php
session_start();
include '../../controller/CapteurC.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $capteurC = new CapteurC();
    
    try {
        $result = $capteurC->deleteCapteur($_GET['id']);
        
        if ($result) {
            $_SESSION['success_message'] = "✅ Capteur supprimé avec succès !";
        } else {
            $_SESSION['error_message'] = "❌ Erreur lors de la suppression du capteur.";
        }
    } catch (Exception $e) {
        error_log("Erreur deleteCapteur: " . $e->getMessage());
        $_SESSION['error_message'] = "❌ Erreur technique : " . $e->getMessage();
    }
} else {
    $_SESSION['error_message'] = "❌ ID du capteur manquant.";
}

header("Location: listCapteur.php");
exit();
?>