<?php
include 'controller/PublicationC.php';
include 'model/Publication.php';

$publicationC = new PublicationC();

if(isset($_POST['Save'])){
    $publication = new Publication($_POST['titre'], $_POST['contenu'], $_POST['auteur_id']);
    $publicationC->updatePublication($_POST['id'], $publication);
    header('Location: liste.php');
    exit;
}

if(isset($_GET['id'])){
    $publication = $publicationC->getPublication($_GET['id']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Publication</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        input, textarea { width: 100%; padding: 8px; margin: 5px 0; }
        button { background: blue; color: white; padding: 10px; border: none; }
    </style>
</head>
<body>
    <h2>Modifier la Publication</h2>
    
    <?php if(isset($publication)): ?>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $_GET['id'] ?>">
        
        <p>
            <label>Titre :</label>
            <input type="text" name="titre" value="<?= $publication->getTitre() ?>" required>
        </p>
        
        <p>
            <label>Contenu :</label>
            <textarea name="contenu" rows="5" required><?= $publication->getContenu() ?></textarea>
        </p>
        
        <input type="hidden" name="auteur_id" value="<?= $publication->getAuteurId() ?>">
        
        <button type="submit" name="Save">Sauvegarder</button>
        <a href="liste.php">Annuler</a>
    </form>
    <?php else: ?>
        <p style="color: red;">Publication non trouvée</p>
        <a href="liste.php">Retour</a>
    <?php endif; ?>
</body>
</html>