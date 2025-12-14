<?php
include '../../controller/tacheC.php';
include '../../model/tache.php';
include '../../controller/planteC.php';
include '../../model/plante.php';
$planteC = new planteC();

// Function to handle file upload
function handleImageUpload($file, $currentImage = null) {
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return $currentImage; // No new image uploaded, keep current
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Erreur d'upload d'image");
    }
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception("Format d'image non autorisé (JPG, PNG, GIF)");
    }
    
    // Validate file size (5MB max)
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception("Image trop volumineuse (max 5MB)");
    }
    
    // Create directory if it doesn't exist
    $uploadDir = '../../view/image/plants/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Delete old image if exists
    if ($currentImage && file_exists($uploadDir . basename($currentImage))) {
        @unlink($uploadDir . basename($currentImage));
    }
    
    // Generate unique filename
    $filename = uniqid('plant_') . '_' . basename($file['name']);
    $uploadPath = $uploadDir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception("Erreur lors de la sauvegarde de l'image");
    }
    
    // Return relative path for database
    return '../image/plants/' . $filename;
}

try {
    // Get current plant data to preserve image if not updated
    $currentPlante = $planteC->getPlanteById($_POST['id_plante']);
    $currentImage = $currentPlante['image'] ?? null;
    
    // Handle image upload
    $imagePath = handleImageUpload($_FILES['image'] ?? null, $currentImage);
    
    // Get temperature from POST
    $temperature = isset($_POST['temperature']) ? $_POST['temperature'] : ($currentPlante['temperature'] ?? '20.0');
    
    // Crée l'objet plante avec les données du formulaire
    $p = new plante(
        $_POST['id_plante'],      // id de la plante
        $_POST['nom_plante'],
        $_POST['date_ajout'],
        $_POST['niveau_humidite'],
        $_POST['besoin_eau'],
        $_POST['etat_sante'],
        $temperature,
        $imagePath,
        $_POST['idUtilisateur']
    );

    // Appelle la méthode avec l'objet et l'id
    $planteC->modifierPlante($p, $_POST['id_plante']);

    // Redirige vers la liste
    $_SESSION['successMsg'] = "Plante modifiée avec succès !";
    header('Location: plantes.php');
    exit;

} catch (Exception $e) {
    $_SESSION['errorMsg'] = $e->getMessage();
    header('Location: plantes.php?edit=' . $_POST['id_plante']);
    exit;
}
?>