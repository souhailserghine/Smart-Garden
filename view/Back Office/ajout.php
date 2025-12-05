<?php
session_start(); // Démarre la session

include '../../Controller/planteC.php';
include '../../Model/plante.php';

$pl = new planteC();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = trim($_POST['nom_plante']);
    $date = $_POST['date_ajout'];
    $humidite = $_POST['niveau_humidite'];
    $eau = $_POST['besoin_eau'];
    $etat = $_POST['etat_sante'];
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

    // Si toutes les validations passent, on peut supprimer les données stockées en session
    unset($_SESSION['formData']);

    // Ajout plante
    try {
        $plante = new Plante(null, $nom, $date, $humidite, $eau, $etat, $user);

        $pl->ajouterPlante($plante);

        $_SESSION['successMsg'] = "Plante ajoutée avec succès !";
        header("Location: plantes.php");
        exit;

    } catch (Exception $e) {
        $_SESSION['errorMsg'] = $e->getMessage();
        header("Location: plantes.php");
        exit;
    }
}

// Mauvaise méthode
$_SESSION['errorMsg'] = "Méthode non autorisée";
header("Location: plantes.php");
exit;
?>
