<?php 
session_start();
include_once '../../config.php';
include_once '../../Controller/suggestionC.php';

// Simuler l'ID admin
if (!isset($_SESSION['idUtilisateur'])) {
    $_SESSION['idUtilisateur'] = 1; // Admin
}

$suggestionC = new suggestionC();

// Récupérer le filtre depuis la requête
$filter = $_GET['filter'] ?? 'En attente';
$suggestions = $suggestionC->listSuggestions($filter);
$pendingCount = $suggestionC->countPendingSuggestions();

// Traiter les actions AJAX
if ($_POST) {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';
    $idSuggestion = $_POST['id_suggestion'] ?? null;
    
    if ($action === 'accept') {
        $result = $suggestionC->acceptSuggestion($idSuggestion);
        echo json_encode($result);
    } elseif ($action === 'reject') {
        $result = $suggestionC->rejectSuggestion($idSuggestion);
        echo json_encode($result);
    } elseif ($action === 'delete') {
        $result = $suggestionC->deleteSuggestion($idSuggestion);
        echo json_encode($result);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Suggestions - Admin</title>
    <link href="./assets/css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/boxicons@2.0.1/css/boxicons.min.css' rel='stylesheet'>
    <link href="./css/style.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        
        .badge-pending {
            background: #ff6b6b;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .filters {
            padding: 20px 30px;
            display: flex;
            gap: 10px;
            border-bottom: 1px solid #eee;
            flex-wrap: wrap;
        }
        
        .filters a {
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            color: #666;
            background: #f0f0f0;
            transition: all 0.3s;
        }
        
        .filters a.active {
            background: #667eea;
            color: white;
        }
        
        .filters a:hover {
            background: #667eea;
            color: white;
        }
        
        .content {
            padding: 30px;
        }
        
        .suggestion-card {
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s;
            background: #f9f9f9;
        }
        
        .suggestion-card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            border-color: #667eea;
        }
        
        .suggestion-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }
        
        .suggestion-info h5 {
            margin: 0;
            color: #333;
            font-weight: 600;
        }
        
        .suggestion-info small {
            color: #999;
        }
        
        .suggestion-status {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-accepted {
            background: #d4edda;
            color: #155724;
        }
        
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        
        .suggestion-body {
            margin-bottom: 15px;
        }
        
        .suggestion-body p {
            margin: 10px 0;
            color: #555;
        }
        
        .suggestion-image {
            max-width: 200px;
            max-height: 150px;
            border-radius: 8px;
            margin-top: 10px;
        }
        
        .suggestion-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        
        .suggestion-actions button {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .btn-accept {
            background: #28a745;
            color: white;
        }
        
        .btn-accept:hover {
            background: #218838;
        }
        
        .btn-reject {
            background: #dc3545;
            color: white;
        }
        
        .btn-reject:hover {
            background: #c82333;
        }
        
        .btn-delete {
            background: #6c757d;
            color: white;
        }
        
        .btn-delete:hover {
            background: #5a6268;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 30px;
            color: #999;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="container" style="max-width: 900px; margin: auto;">
        <div class="header">
            <div>
                <h1><i class='bx bx-leaf'></i> Gestion des Suggestions</h1>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Modérez et validez les suggestions de plantes</p>
            </div>
            <div class="badge-pending" title="Suggestions en attente">
                <?= $pendingCount ?>
            </div>
        </div>
        
        <div class="filters">
            <a href="?filter=Toutes" <?= $filter === 'Toutes' ? 'class="active"' : '' ?>>
                <i class='bx bx-list-ul'></i> Toutes
            </a>
            <a href="?filter=En attente" <?= $filter === 'En attente' ? 'class="active"' : '' ?>>
                <i class='bx bx-time'></i> En attente
            </a>
            <a href="?filter=Acceptée" <?= $filter === 'Acceptée' ? 'class="active"' : '' ?>>
                <i class='bx bx-check'></i> Acceptées
            </a>
            <a href="?filter=Rejetée" <?= $filter === 'Rejetée' ? 'class="active"' : '' ?>>
                <i class='bx bx-x'></i> Rejetées
            </a>
        </div>
        
        <div class="content">
            <?php if (empty($suggestions)): ?>
                <div class="empty-state">
                    <i class='bx bx-inbox'></i>
                    <h4>Aucune suggestion</h4>
                    <p>Il n'y a aucune suggestion dans cette catégorie.</p>
                </div>
            <?php else: ?>
                <?php foreach ($suggestions as $suggestion): ?>
                    <div class="suggestion-card">
                        <div class="suggestion-header">
                            <div class="suggestion-info">
                                <h5><?= htmlspecialchars($suggestion['nom_plante']) ?></h5>
                                <small>
                                    <strong>Type:</strong> <?= htmlspecialchars($suggestion['type_plante']) ?> | 
                                    <strong>Par:</strong> <?= htmlspecialchars($suggestion['utilisateur_nom'] ?? 'Anonyme') ?> | 
                                    <strong>Date:</strong> <?= date('d/m/Y H:i', strtotime($suggestion['date_suggestion'])) ?>
                                </small>
                            </div>
                            <span class="suggestion-status status-<?= strtolower(str_replace(' ', '-', $suggestion['statut'])) ?>">
                                <?= $suggestion['statut'] ?>
                            </span>
                        </div>
                        
                        <div class="suggestion-body">
                            <p><strong>Description:</strong></p>
                            <p><?= nl2br(htmlspecialchars($suggestion['description'] ?? 'Aucune description')) ?></p>
                            
                            <?php if ($suggestion['image']): ?>
                                <img src="<?= $suggestion['image'] ?>" alt="<?= htmlspecialchars($suggestion['nom_plante']) ?>" class="suggestion-image">
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($suggestion['statut'] === 'En attente'): ?>
                            <div class="suggestion-actions">
                                <button class="btn-accept" onclick="actionSuggestion(<?= $suggestion['id_suggestion'] ?>, 'accept')">
                                    <i class='bx bx-check me-2'></i>Accepter
                                </button>
                                <button class="btn-reject" onclick="actionSuggestion(<?= $suggestion['id_suggestion'] ?>, 'reject')">
                                    <i class='bx bx-x me-2'></i>Rejeter
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="suggestion-actions">
                                <button class="btn-delete" onclick="actionSuggestion(<?= $suggestion['id_suggestion'] ?>, 'delete')">
                                    <i class='bx bx-trash me-2'></i>Supprimer
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="./assets/js/jquery/jquery-3.3.1.min.js"></script>
    <script src="./assets/js/bootstrap/bootstrap.min.js"></script>
    
    <script>
        function actionSuggestion(idSuggestion, action) {
            const formData = new FormData();
            formData.append('id_suggestion', idSuggestion);
            formData.append('action', action);
            
            const confirmMsg = action === 'accept' ? 'Accepter cette suggestion?' : 
                              action === 'reject' ? 'Rejeter cette suggestion?' : 
                              'Supprimer cette suggestion?';
            
            if (!confirm(confirmMsg)) return;
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ ' + data.message);
                    location.reload();
                } else {
                    alert('❌ ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            });
        }
    </script>
</body>
</html>
