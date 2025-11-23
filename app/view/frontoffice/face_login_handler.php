<?php
session_start();
require_once '../../controller/utilisateurController.php';
require_once '../../controller/historiqueController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login_face.php");
    exit();
}

$faceData = $_POST['face'];

$ch = curl_init('http://localhost:5000/login');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['face'=>$faceData]));
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    header("Location: login_face.php?error=Connection error: " . urlencode($curlError));
    exit();
}

if ($response === false || $httpCode != 200) {
    header("Location: login_face.php?error=Server error (HTTP $httpCode)");
    exit();
}

$result = json_decode($response, true);
if ($result && isset($result['success']) && $result['success']) {
    $controller = new UtilisateurC();
    $hcontroller = new HistoriqueC();
    
    $utilisateur = $controller->trouverParEmail($result['email']);
    
    if ($utilisateur) {
        if ($utilisateur['statut'] == 'bloque') {
            header("Location: sign-in.php?error=blocked");
            exit();
        }
        
        $dateConnexion = date('Y-m-d H:i:s');
        $log = $hcontroller->ajouterHistorique($dateConnexion, '0000-00-00 00:00:00', 'face_login', 0, $utilisateur['idUtilisateur']);
        
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $utilisateur['idUtilisateur'];
        $_SESSION['user_name'] = $utilisateur['nom'];
        $_SESSION['user_email'] = $utilisateur['email'];
        $_SESSION['user_localisation'] = $utilisateur['localisation'];
        $_SESSION['user_role'] = $utilisateur['role'];
        
        if ($utilisateur['role'] == 'admin') {
            header("Location: ../backoffice/index.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        header("Location: sign-in.php?error=invalid");
        exit();
    }
} else {
    $message = $result['message'] ?? 'Face not recognized';
    header("Location: login_face.php?error=" . urlencode($message));
    exit();
}
?>
