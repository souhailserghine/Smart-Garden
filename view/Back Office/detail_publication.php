<?php include 'header_back.php'; ?>

<div class="container-fluid pt-4 px-4">
    <div class="bg-light rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0">📋 Détail de la publication</h4>
            <a href="backoffice.php" class="btn btn-secondary">
                <i class="fa fa-arrow-left me-2"></i>Retour
            </a>
        </div>

        <?php if($publication): ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-4">
                        <h5>Contenu de la publication</h5>
                        <div class="bg-white p-4 border rounded">
                            <h4><?php echo htmlspecialchars($publication['titre']); ?></h4>
                            <p class="text-muted">
                                Par <?php echo htmlspecialchars($publication['auteur']); ?> 
                                le <?php echo date('d/m/Y à H:i', strtotime($publication['date_creation'])); ?>
                            </p>
                            <hr>
                            <div style="white-space: pre-line;"><?php echo htmlspecialchars($publication['contenu']); ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="bg-white p-4 border rounded">
                        <h5>Actions de modération</h5>
                        <div class="d-grid gap-2">
                            <a href="backoffice.php?action=supprimer_publication&id=<?php echo $publication['id']; ?>" 
                               class="btn btn-danger"
                               onclick="return confirm('Supprimer définitivement cette publication ?')">
                                <i class="fa fa-trash me-2"></i>Supprimer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">Publication non trouvée.</div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer_back.php'; ?>
