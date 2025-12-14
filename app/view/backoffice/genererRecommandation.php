<?php
// IMPORTANT: Aucun espace ou caractère avant cette ligne!

// Désactiver l'affichage des erreurs (elles seront dans les logs)
ini_set('display_errors', '0');
error_reporting(E_ALL);

// Nettoyer tous les buffers existants
while (ob_get_level()) {
    ob_end_clean();
}

// Démarrer un nouveau buffer propre
ob_start();

session_start();

include '../../controller/capteurC.php';

// Headers JSON stricts
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

/**
 * Fonction pour envoyer une réponse JSON propre
 */
function envoyerReponseJSON($data) {
    // Nettoyer le buffer
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Envoyer le JSON
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

/**
 * Fonction pour logger les erreurs
 */
function loggerErreur($message, $context = []) {
    $logMessage = date('[Y-m-d H:i:s] ') . $message;
    if (!empty($context)) {
        $logMessage .= ' | Context: ' . json_encode($context);
    }
    error_log($logMessage);
}

// Vérifier la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    envoyerReponseJSON([
        'success' => false,
        'error' => 'Méthode non autorisée. Utilisez POST.'
    ]);
}

// Récupérer et valider les paramètres
$id_capteur = isset($_POST['id_capteur']) ? intval($_POST['id_capteur']) : 0;
$id_utilisateur = isset($_POST['id_utilisateur']) ? intval($_POST['id_utilisateur']) : 0;

loggerErreur("📥 Requête reçue", [
    'id_capteur' => $id_capteur,
    'id_utilisateur' => $id_utilisateur
]);

if ($id_capteur <= 0 || $id_utilisateur <= 0) {
    envoyerReponseJSON([
        'success' => false,
        'error' => 'Paramètres invalides',
        'details' => [
            'id_capteur' => $id_capteur,
            'id_utilisateur' => $id_utilisateur
        ]
    ]);
}

try {
    $capteurC = new CapteurC();
    
    // ========== ÉTAPE 1: GÉNÉRER LA RECOMMANDATION IA ==========
    loggerErreur("🤖 Génération recommandation IA pour capteur #$id_capteur");
    
    $resultat = $capteurC->genererRecommandationIA($id_capteur);
    
    if (!$resultat['success']) {
        throw new Exception($resultat['error']);
    }
    
    // Extraire les données
    $recommandationJSON = $resultat['recommandation'];
    $capteur = $resultat['capteur'];
    
    loggerErreur("✅ Recommandation générée", [
        'json_length' => strlen($recommandationJSON)
    ]);
    
    // Parser le JSON
    $recommandationData = json_decode($recommandationJSON, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        loggerErreur("⚠️ Erreur parsing JSON: " . json_last_error_msg());
        throw new Exception("Erreur lors du parsing de la recommandation: " . json_last_error_msg());
    }
    
    // Valider les données essentielles
    if (!isset($recommandationData['titre']) || !isset($recommandationData['actions'])) {
        loggerErreur("⚠️ Données de recommandation incomplètes");
        throw new Exception("Données de recommandation incomplètes");
    }
    
    loggerErreur("✅ Recommandation validée: " . $recommandationData['titre']);
    
    // ========== ÉTAPE 2: RÉCUPÉRER L'UTILISATEUR ==========
    loggerErreur("👤 Recherche utilisateur #$id_utilisateur");
    
    $utilisateurs = $capteurC->getAllUtilisateurs();
    $utilisateur = null;
    
    foreach ($utilisateurs as $user) {
        if ($user['idUtilisateur'] == $id_utilisateur) {
            $utilisateur = $user;
            break;
        }
    }
    
    if (!$utilisateur) {
        throw new Exception("Utilisateur introuvable (ID: $id_utilisateur)");
    }
    
    loggerErreur("✅ Utilisateur trouvé: " . $utilisateur['nom'] . " <" . $utilisateur['email'] . ">");
    
    // ========== ÉTAPE 3: GÉNÉRER LE HTML DE L'EMAIL ==========
    loggerErreur("📝 Génération du HTML de l'email");
    
    $htmlEmail = genererHTMLEmail($recommandationData, $capteur, $utilisateur);
    
    // ========== ÉTAPE 4: ENVOYER L'EMAIL ==========
    $sujet = "🤖 SmartGarden - " . $recommandationData['titre'];
    
    loggerErreur("📧 Envoi de l'email à: " . $utilisateur['email']);
    
    $emailEnvoye = $capteurC->envoyerEmailRecommandation(
        $utilisateur['email'],
        $utilisateur['nom'],
        $sujet,
        $htmlEmail
    );
    
    if (!$emailEnvoye) {
        loggerErreur("❌ Échec de l'envoi de l'email");
        throw new Exception("Impossible d'envoyer l'email. Vérifiez la configuration SMTP.");
    }
    
    loggerErreur("✅ Email envoyé avec succès!");
    
    // ========== RÉPONSE DE SUCCÈS ==========
    envoyerReponseJSON([
        'success' => true,
        'message' => 'Recommandation générée et envoyée avec succès!',
        'data' => [
            'recommandation' => $recommandationData,
            'destinataire' => [
                'nom' => $utilisateur['nom'],
                'email' => $utilisateur['email']
            ],
            'capteur' => [
                'id' => $capteur['id_capteur'],
                'categorie' => $capteur['nom_categorie'] ?? 'N/A',
                'emplacement' => $capteur['emplacement']
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ]);
    
} catch (Exception $e) {
    loggerErreur("❌ ERREUR: " . $e->getMessage(), [
        'file' => basename($e->getFile()),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    
    envoyerReponseJSON([
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => [
            'file' => basename($e->getFile()),
            'line' => $e->getLine(),
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ]);
}

/**
 * Générer le HTML de l'email
 */
function genererHTMLEmail($recommandation, $capteur, $utilisateur) {
    
    // Couleur selon priorité
    $couleurPriorite = match($recommandation['priorite']) {
        'critique' => '#dc2626',
        'haute' => '#f59e0b',
        'moyenne' => '#3b82f6',
        default => '#10b981'
    };
    
    $iconePriorite = match($recommandation['priorite']) {
        'critique' => '🚨',
        'haute' => '⚠️',
        'moyenne' => '📊',
        default => '✅'
    };
    
    // Échapper toutes les données
    $nomUtilisateur = htmlspecialchars($utilisateur['nom'], ENT_QUOTES, 'UTF-8');
    $titreRecommandation = htmlspecialchars($recommandation['titre'], ENT_QUOTES, 'UTF-8');
    $diagnostic = nl2br(htmlspecialchars($recommandation['diagnostic'], ENT_QUOTES, 'UTF-8'));
    $benefices = htmlspecialchars($recommandation['benefices'], ENT_QUOTES, 'UTF-8');
    $delai = htmlspecialchars($recommandation['delai'], ENT_QUOTES, 'UTF-8');
    $nomCategorie = htmlspecialchars($capteur['nom_categorie'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
    $emplacement = htmlspecialchars($capteur['emplacement'], ENT_QUOTES, 'UTF-8');
    $etatCapteur = ucfirst(htmlspecialchars($capteur['etatCapteur'], ENT_QUOTES, 'UTF-8'));
    $prioriteUpper = strtoupper($recommandation['priorite']);
    
    $html = '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartGarden</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px;">🤖 SmartGarden AI</h1>
                            <p style="color: #ffffff; margin: 10px 0 0 0; opacity: 0.9;">Recommandation Intelligente</p>
                        </td>
                    </tr>
                    
                    <!-- Salutation -->
                    <tr>
                        <td style="padding: 30px;">
                            <p style="font-size: 16px; color: #374151; margin: 0 0 20px 0;">
                                Bonjour <strong>' . $nomUtilisateur . '</strong>,
                            </p>
                            <p style="font-size: 14px; color: #6b7280; margin: 0 0 30px 0;">
                                Notre système d\'intelligence artificielle a analysé votre capteur et généré une recommandation personnalisée.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Titre & Priorité -->
                    <tr>
                        <td style="padding: 0 30px;">
                            <div style="background-color: #f9fafb; border-left: 4px solid ' . $couleurPriorite . '; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                                <h2 style="margin: 0 0 10px 0; color: #1f2937; font-size: 20px;">
                                    ' . $iconePriorite . ' ' . $titreRecommandation . '
                                </h2>
                                <span style="background-color: ' . $couleurPriorite . '; color: #ffffff; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: bold; text-transform: uppercase;">
                                    ' . $prioriteUpper . '
                                </span>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Informations Capteur -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px;">
                            <div style="background-color: #eff6ff; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                                <h3 style="margin: 0 0 15px 0; color: #1e40af; font-size: 16px;">📡 Informations du Capteur</h3>
                                <table width="100%" cellpadding="8" cellspacing="0">
                                    <tr>
                                        <td style="color: #6b7280; font-size: 14px; width: 40%;"><strong>ID:</strong></td>
                                        <td style="color: #1f2937; font-size: 14px;">#' . $capteur['id_capteur'] . '</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #6b7280; font-size: 14px;"><strong>Catégorie:</strong></td>
                                        <td style="color: #1f2937; font-size: 14px;">' . $nomCategorie . '</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #6b7280; font-size: 14px;"><strong>État:</strong></td>
                                        <td style="color: #1f2937; font-size: 14px;">' . $etatCapteur . '</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #6b7280; font-size: 14px;"><strong>Emplacement:</strong></td>
                                        <td style="color: #1f2937; font-size: 14px;">' . $emplacement . '</td>
                                    </tr>';
    
    if (!empty($capteur['nom_plante'])) {
        $nomPlante = htmlspecialchars($capteur['nom_plante'], ENT_QUOTES, 'UTF-8');
        $html .= '
                                    <tr>
                                        <td style="color: #6b7280; font-size: 14px;"><strong>Plante:</strong></td>
                                        <td style="color: #1f2937; font-size: 14px;">🌱 ' . $nomPlante . '</td>
                                    </tr>';
    }
    
    $html .= '
                                </table>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Diagnostic -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px;">
                            <h3 style="margin: 0 0 15px 0; color: #1f2937; font-size: 16px;">🔍 Diagnostic</h3>
                            <p style="color: #4b5563; font-size: 14px; line-height: 1.6; margin: 0;">
                                ' . $diagnostic . '
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Actions Recommandées -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px;">
                            <h3 style="margin: 0 0 15px 0; color: #1f2937; font-size: 16px;">✅ Actions Recommandées</h3>
                            <ul style="margin: 0; padding-left: 20px;">';
    
    foreach ($recommandation['actions'] as $action) {
        $actionEscaped = htmlspecialchars($action, ENT_QUOTES, 'UTF-8');
        $html .= '<li style="color: #4b5563; font-size: 14px; line-height: 1.8; margin-bottom: 8px;">' . $actionEscaped . '</li>';
    }
    
    $html .= '
                            </ul>
                        </td>
                    </tr>
                    
                    <!-- Bénéfices -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px;">
                            <div style="background-color: #f0fdf4; padding: 20px; border-radius: 8px; border-left: 4px solid #10b981;">
                                <h3 style="margin: 0 0 10px 0; color: #065f46; font-size: 16px;">🎯 Bénéfices Attendus</h3>
                                <p style="color: #047857; font-size: 14px; line-height: 1.6; margin: 0;">
                                    ' . $benefices . '
                                </p>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Délai -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px;">
                            <div style="background-color: #fef3c7; padding: 15px; border-radius: 8px; text-align: center;">
                                <p style="margin: 0; color: #92400e; font-size: 14px;">
                                    ⏱️ <strong>Délai recommandé:</strong> ' . $delai . '
                                </p>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="color: #6b7280; font-size: 12px; margin: 0 0 10px 0;">
                                Cet email a été généré automatiquement par SmartGarden AI
                            </p>
                            <p style="color: #9ca3af; font-size: 11px; margin: 0;">
                                © ' . date('Y') . ' SmartGarden. Tous droits réservés.
                            </p>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    
    return $html;
}
?>