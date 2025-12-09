<?php 
include '../../Controller/planteC.php';
$pl = new planteC();

$listePlantes = $pl->listPlantes();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste </title>
    <table border="1">
    <tr>
        <th>ID</th>
        <th>Date</th>
        <th>Niveau Humidité</th>
        <th>Besoin Eau</th>
        <th>État Santé</th>
    </tr>

    <?php foreach($liste as $plante) { ?>
        <tr>
            <td><?php echo $plante['id']; ?></td>
            <td><?php echo $plante['date']; ?></td>
            <td><?php echo $plante['niveau_humidite']; ?></td>
            <td><?php echo $plante['besoin_eau']; ?></td>
            <td><?php echo $plante['etat_sante']; ?></td>
        </tr>
    <?php } ?>
</table>

    
</head>
<body>
    
</body>
</html>