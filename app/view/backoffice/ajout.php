<?php
require_once 'check_session.php'; // Démarre la session

include '../../controller/tacheC.php';
include '../../model/tache.php';

include '../../controller/planteC.php';
include '../../model/plante.php';

$pl = new planteC();

// Function to handle file upload
function handleImageUpload($file) {
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // No image uploaded
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
    
    // Generate unique filename
    $filename = uniqid('plant_') . '_' . basename($file['name']);
    $uploadPath = $uploadDir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception("Erreur lors de la sauvegarde de l'image");
    }
    
    // Return relative path for database
    return '../image/plants/' . $filename;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = trim($_POST['nom_plante']);
    $date = $_POST['date_ajout'];
    $humidite = $_POST['niveau_humidite'];
    $eau = $_POST['besoin_eau'];
    $etat = $_POST['etat_sante'];
    $temperature = isset($_POST['temperature']) ? $_POST['temperature'] : '20.0';
    $user = $_POST['idUtilisateur'];

    // Stocker les données du formulaire pour réaffichage en cas d'erreur
    $_SESSION['formData'] = $_POST;

    // Validation serveur
    if ($nom === "" || strlen($nom) < 3) {
        $_SESSION['errorMsg'] = "Nom invalide";
        header("Location: plantes.php");
        exit;
    }

    if ($date === "") {
        $_SESSION['errorMsg'] = "Date invalide";
        header("Location: plantes.php");
        exit;
    }

    if (!is_numeric($humidite) || $humidite < 0 || $humidite > 100) {
        $_SESSION['errorMsg'] = "Humidité invalide (0-100)";
        header("Location: plantes.php");
        exit;
    }

    if (!is_numeric($eau) || $eau <= 0) {
        $_SESSION['errorMsg'] = "Besoin en eau incorrect";
        header("Location: plantes.php");
        exit;
    }

    if ($etat === "") {
        $_SESSION['errorMsg'] = "État de santé requis";
        header("Location: plantes.php");
        exit;
    }

    if (!is_numeric($user) || $user <= 0) {
        $_SESSION['errorMsg'] = "Utilisateur invalide";
        header("Location: plantes.php");
        exit;
    }

    // Handle image upload
    $imagePath = null;
    try {
        $imagePath = handleImageUpload($_FILES['image'] ?? null);
    } catch (Exception $e) {
        $_SESSION['errorMsg'] = $e->getMessage();
        header("Location: plantes.php");
        exit;
    }

    // Si toutes les validations passent, on peut supprimer les données stockées en session
    unset($_SESSION['formData']);

    // Ajout plante
    try {
        $plante = new Plante(null, $nom, $date, $humidite, $eau, $etat, $temperature, $imagePath, $user);

        $pl->ajouterPlante($plante);

        $_SESSION['successMsg'] = "Plante ajoutée avec succès !";
        header("Location: plantes.php");
        exit;

    } catch (Exception $e) {
        $_SESSION['errorMsg'] = $e->getMessage();
        $_SESSION['errorType'] = 'error';
        header("Location: plantes.php");
        exit;
    }
}

// Mauvaise méthode
$_SESSION['errorMsg'] = "Méthode non autorisée";
$_SESSION['errorType'] = 'error';
header("Location: plantes.php");
exit;
?>

