<?php
require_once 'check_session.php';
include '../../controller/planteC.php';
include '../../model/plante.php';

$planteC = new planteC();
$userId = $_SESSION['idUtilisateur'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $planteData = $planteC->getPlanteById($_POST['id_plante']);
        
        if (!$planteData) {
            die("Plante introuvable !");
        }

        // Vérifier que la plante appartient à l'utilisateur
        if ($planteData['idUtilisateur'] != $userId) {
            die("Accès refusé !");
        }

        $plante = new Plante(
            $_POST['id_plante'],
            $_POST['nom_plante'],
            $_POST['date_ajout'],
            $_POST['niveau_humidite'],
            $_POST['besoin_eau'],
            $_POST['etat_sante'],
            $_POST['temperature'] ?? $planteData['temperature'] ?? null,
            $planteData['image'] ?? null, // Keep existing image
            $userId
        );

        $ok = $planteC->modifierPlante($plante, $_POST['id_plante']);
        if ($ok) {
            header('Location: plantes.php');
            exit;
        } else {
            echo "<p style='color:red;'>Modification échouée</p>";
        }
    } catch (Exception $e) {
        die("Erreur: " . $e->getMessage());
    }
}

// Get plant data for editing
$plante = null;
if (isset($_GET['id'])) {
    $plante = $planteC->getPlanteById($_GET['id']);
    
    if (!$plante) {
        die("Plante introuvable !");
    }
    
    // Vérifier que la plante appartient à l'utilisateur
    if ($plante['idUtilisateur'] != $userId) {
        die("Accès refusé !");
    }
} else {
    die("ID de plante manquant !");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="./assets/images/logo-16x16.png" />
    <title>SmartGarden</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .edit-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
        }
        
        .edit-card h2 {
            color: #667eea;
            margin-bottom: 30px;
            font-weight: 700;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 12px;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-update {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 40px;
            font-weight: 600;
            color: white;
            transition: transform 0.2s;
        }
        
        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-cancel {
            background: #6c757d;
            border: none;
            border-radius: 10px;
            padding: 12px 40px;
            font-weight: 600;
            color: white;
            transition: transform 0.2s;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .range-value {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="edit-card">
        <h2><i class="bx bx-edit"></i> Modifier la Plante</h2>
        
        <form method="POST" action="">
            <input type="hidden" name="id_plante" value="<?= htmlspecialchars($plante['id_plante']) ?>">
            
            <div class="mb-3">
                <label for="nom_plante" class="form-label">
                    <i class="bx bx-leaf"></i> Nom de la Plante
                </label>
                <input type="text" 
                       class="form-control" 
                       id="nom_plante" 
                       name="nom_plante" 
                       value="<?= htmlspecialchars($plante['nom_plante']) ?>" 
                       required>
            </div>
            
            <div class="mb-3">
                <label for="date_ajout" class="form-label">
                    <i class="bx bx-calendar"></i> Date d'Ajout
                </label>
                <input type="date" 
                       class="form-control" 
                       id="date_ajout" 
                       name="date_ajout" 
                       value="<?= htmlspecialchars($plante['date_ajout']) ?>" 
                       required>
            </div>
            
            <div class="mb-3">
                <label for="niveau_humidite" class="form-label">
                    <i class="bx bx-droplet"></i> Niveau d'Humidité
                    <span class="range-value" id="humiditeValue"><?= htmlspecialchars($plante['niveau_humidite']) ?>%</span>
                </label>
                <input type="range" 
                       class="form-range" 
                       id="niveau_humidite" 
                       name="niveau_humidite" 
                       min="0" 
                       max="100" 
                       value="<?= htmlspecialchars($plante['niveau_humidite']) ?>"
                       oninput="document.getElementById('humiditeValue').textContent = this.value + '%'">
            </div>
            
            <div class="mb-3">
                <label for="besoin_eau" class="form-label">
                    <i class="bx bx-water"></i> Besoin en Eau (ml)
                </label>
                <input type="number" 
                       class="form-control" 
                       id="besoin_eau" 
                       name="besoin_eau" 
                       value="<?= htmlspecialchars($plante['besoin_eau']) ?>" 
                       required>
            </div>
            
            <div class="mb-3">
                <label for="temperature" class="form-label">
                    <i class="bx bx-thermometer"></i> Température (°C)
                </label>
                <input type="number" 
                       class="form-control" 
                       id="temperature" 
                       name="temperature" 
                       step="0.1"
                       value="<?= htmlspecialchars($plante['temperature'] ?? '') ?>" 
                       placeholder="Optionnel">
            </div>
            
            <div class="mb-4">
                <label for="etat_sante" class="form-label">
                    <i class="bx bx-heart"></i> État de Santé
                </label>
                <select class="form-select" id="etat_sante" name="etat_sante" required>
                    <option value="Excellente" <?= $plante['etat_sante'] == 'Excellente' ? 'selected' : '' ?>>Excellente</option>
                    <option value="Bonne" <?= $plante['etat_sante'] == 'Bonne' ? 'selected' : '' ?>>Bonne</option>
                    <option value="Moyenne" <?= $plante['etat_sante'] == 'Moyenne' ? 'selected' : '' ?>>Moyenne</option>
                    <option value="Mauvaise" <?= $plante['etat_sante'] == 'Mauvaise' ? 'selected' : '' ?>>Mauvaise</option>
                </select>
            </div>
            
            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-update flex-grow-1">
                    <i class="bx bx-save"></i> Enregistrer
                </button>
                <a href="plantes.php" class="btn btn-cancel">
                    <i class="bx bx-x"></i> Annuler
                </a>
            </div>
        </form>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
