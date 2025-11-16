<?php
session_start();
include '../../controller/CategorieC.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $categorieC = new CategorieC();
    
    try {
        $result = $categorieC->deleteCategorie($_GET['id']);
        
        if ($result) {
            $_SESSION['success_message'] = "✅ Catégorie supprimée avec succès !";
        } else {
            $_SESSION['error_message'] = "❌ Erreur lors de la suppression de la catégorie.";
        }
    } catch (Exception $e) {
        error_log("Erreur deleteCategorie: " . $e->getMessage());
        $_SESSION['error_message'] = "❌ Erreur technique : " . $e->getMessage();
    }
} else {
    $_SESSION['error_message'] = "❌ ID de la catégorie manquant.";
}

header("Location: listCategorie.php");
exit();
?>