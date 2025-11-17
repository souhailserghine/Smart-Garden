<?php
include '../../Controller/planteC.php';
include '../../Model/plante.php';

$planteC = new planteC();


if (isset($_GET['id'])) {
    $planteData = $planteC->getPlanteById($_GET['id']);
    if (!$planteData) {
        die("Plante introuvable !");
    }
}


if (isset($_POST['modifier'])) {
    $p = new Plante(
        $_POST['id_plante'],          
        $_POST['nom_plante'],         
        $_POST['date_ajout'],          
        $_POST['niveau_humidite'],    
        $_POST['besoin_eau'],         
        $_POST['etat_sante'],       
        $_POST['idUtilisateur']     
    );

    
    $planteC->modifierPlante($p, $_POST['id_plante']);

    
    header('Location: plantes.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Plante</title>
    <style>
        form { max-width: 500px; margin: 50px auto; }
        input, button { display: block; margin: 10px 0; width: 100%; padding: 8px; }
    </style>
</head>
<body>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Plante</title>
    <style>
        form { max-width: 500px; margin: 50px auto; background: #f9f9f9; padding: 20px; border-radius: 8px; }
        input, select, button { display: block; margin: 10px 0; width: 100%; padding: 8px; font-size: 16px; }
        button { cursor: pointer; }
        button[type="submit"] { background-color: #28a745; color: white; border: none; }
        button[type="submit"]:hover { background-color: #1e7e34; }
        button[type="button"] { background-color: #ccc; border: none; }
    </style>
</head>
<body>

<h2>Modifier Plante</h2>

<form method="POST">
    <!-- Champ caché pour l'id -->
    <input type="hidden" name="id_plante" value="<?php echo $planteData['id_plante']; ?>">

    Nom plante:
    <input type="text" name="nom_plante" value="<?php echo $planteData['nom_plante']; ?>" required>

    Date ajout:
    <input type="date" name="date_ajout" value="<?php echo $planteData['date_ajout']; ?>" required>

    Niveau humidité:
    <input type="number" name="niveau_humidite" value="<?php echo $planteData['niveau_humidite']; ?>" min="0" max="100" required>

    Besoin eau:
    <input type="number" name="besoin_eau" value="<?php echo $planteData['besoin_eau']; ?>" min="0" required>

    État santé:
    <select name="etat_sante" required>
        <option value="Bon état" <?php if($planteData['etat_sante'] == "Bon état") echo "selected"; ?>>Bon état</option>
        <option value="Moyen" <?php if($planteData['etat_sante'] == "Moyen") echo "selected"; ?>>Moyen</option>
        <option value="Mauvais état" <?php if($planteData['etat_sante'] == "Mauvais état") echo "selected"; ?>>Mauvais état</option>
    </select>

    ID Utilisateur:
    <input type="number" name="idUtilisateur" value="<?php echo $planteData['idUtilisateur']; ?>" required>

    <button type="submit" name="modifier">Modifier</button>
    <button type="button" onclick="window.history.back()">Annuler</button>
</form>

</body>
</html>
