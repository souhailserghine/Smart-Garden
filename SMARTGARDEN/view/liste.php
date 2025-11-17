<?php
include 'controller/PublicationC.php';

$publicationC = new PublicationC();
$publications = $publicationC->listePublications();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste Publications</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .publication { border: 1px solid #ccc; padding: 15px; margin: 10px 0; }
        .actions a { margin-right: 10px; color: green; }
    </style>
</head>
<body>
    <h1>Publications SmartGarden</h1>
    
    <a href="add.html" style="background: green; color: white; padding: 10px; text-decoration: none;">
        Nouvelle Publication
    </a>
    
    <?php foreach ($publications as $pub): ?>
    <div class="publication">
        <h3><?= $pub['titre'] ?></h3>
        <p><?= nl2br($pub['contenu']) ?></p>
        <small>Date: <?= $pub['date_publication'] ?> | Auteur: <?= $pub['auteur_id'] ?></small>
        
        <div class="actions">
            <a href="modifier.php?id=<?= $pub['id'] ?>">Modifier</a>
            <a href="supprimer.php?id=<?= $pub['id'] ?>" onclick="return confirm('Supprimer?')">Supprimer</a>
        </div>
    </div>
    <?php endforeach; ?>
</body>
</html>