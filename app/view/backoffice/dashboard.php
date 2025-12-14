<?php
// Inclure votre header Back Office existant
include 'header_back.php'; 
?>

<div class="container-fluid pt-4 px-4">
    <!-- Statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-file-alt fa-3x text-primary"></i>
                <div class="ms-3 text-end">
                    <p class="mb-2">Publications en attente</p>
                    <h6 class="mb-0"><?php echo count($data['publications_attente']); ?></h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-comments fa-3x text-warning"></i>
                <div class="ms-3 text-end">
                    <p class="mb-2">Commentaires en attente</p>
                    <h6 class="mb-0"><?php echo count($data['commentaires_attente']); ?></h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-clock fa-3x text-info"></i>
                <div class="ms-3 text-end">
                    <p class="mb-2">Total en attente</p>
                    <h6 class="mb-0"><?php echo $data['total_attente']; ?></h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Publications en attente -->
    <div class="bg-light rounded p-4 mb-4">
        <h5 class="mb-4">
            <i class="fa fa-file-alt me-2"></i>Publications en attente de modération
            <span class="badge bg-warning ms-2"><?php echo count($data['publications_attente']); ?></span>
        </h5>
        
        <?php if(empty($data['publications_attente'])): ?>
            <div class="alert alert-success">
                <i class="fa fa-check-circle me-2"></i>Aucune publication en attente.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Auteur</th>
                            <th>Date</th>
                            <th>Extrait</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['publications_attente'] as $pub): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($pub['titre']); ?></strong></td>
                            <td><?php echo htmlspecialchars($pub['auteur']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($pub['date_creation'])); ?></td>
                            <td><?php echo htmlspecialchars(substr($pub['contenu'], 0, 80)) . '...'; ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="backoffice.php?action=detail_publication&id=<?php echo $pub['id']; ?>" 
                                       class="btn btn-info" title="Vérifier">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="backoffice.php?action=approuver_publication&id=<?php echo $pub['id']; ?>" 
                                       class="btn btn-success" 
                                       onclick="return confirm('Approuver cette publication ?')" title="Approuver">
                                        <i class="fa fa-check"></i>
                                    </a>
                                    <a href="backoffice.php?action=rejeter_publication&id=<?php echo $pub['id']; ?>" 
                                       class="btn btn-danger" title="Rejeter">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Commentaires en attente -->
    <div class="bg-light rounded p-4">
        <h5 class="mb-4">
            <i class="fa fa-comments me-2"></i>Commentaires en attente de modération
            <span class="badge bg-warning ms-2"><?php echo count($data['commentaires_attente']); ?></span>
        </h5>
        
        <?php if(empty($data['commentaires_attente'])): ?>
            <div class="alert alert-success">
                <i class="fa fa-check-circle me-2"></i>Aucun commentaire en attente.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Publication</th>
                            <th>Auteur</th>
                            <th>Commentaire</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['commentaires_attente'] as $com): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($com['publication_titre']); ?></td>
                            <td><?php echo htmlspecialchars($com['auteur']); ?></td>
                            <td><?php echo htmlspecialchars(substr($com['contenu'], 0, 60)) . '...'; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($com['date_commentaire'])); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="backoffice.php?action=detail_commentaire&id=<?php echo $com['id']; ?>" 
                                       class="btn btn-info" title="Vérifier">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="backoffice.php?action=approuver_commentaire&id=<?php echo $com['id']; ?>" 
                                       class="btn btn-success" 
                                       onclick="return confirm('Approuver ce commentaire ?')" title="Approuver">
                                        <i class="fa fa-check"></i>
                                    </a>
                                    <a href="backoffice.php?action=rejeter_commentaire&id=<?php echo $com['id']; ?>" 
                                       class="btn btn-danger" title="Rejeter">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer_back.php'; ?>