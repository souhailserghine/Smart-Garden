<?php
session_start(); // pour récupérer l'utilisateur connecté
$userId = $_SESSION['idUtilisateur']; // supposons que tu stockes l'ID utilisateur ici
?>

<form action="../Back Office/ajout.php" method="POST">
    Nom plante:
    <input type="text" name="nom" required>

    Date ajout:
    <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>

    Niveau humidité (%):
    <input type="number" name="niveau_humidite" required>

    Besoin eau (ml):
    <input type="number" name="besoin_eau" required>

    État santé:
    <select name="etat_sante" required>
        <option value="">--- Choisir ---</option>
        <option value="Bon état">Bon état</option>
        <option value="Moyen">Moyen</option>
        <option value="Mauvais état">Mauvais état</option>
    </select>

    <!-- Champ caché pour l'ID utilisateur -->
    <input type="hidden" name="idUtilisateur" value="<?php echo $userId; ?>">

    <input type="submit" value="Ajouter la Plante">
</form>
