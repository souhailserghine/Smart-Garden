<?php

include_once __DIR__ . '/../config.php';

// Charger PHPMailer
require __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer/src/SMTP.php';
require __DIR__ . '/../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class CapteurC {

    // Ajouter un capteur
    public function addCapteur($capteur) {
        $db = config::getConnexion();

        try {
            $req = $db->prepare('INSERT INTO capteur (etatCapteur, uniteCapteur, emplacement, dateInstallation, id_categorie, id_plante) 
                                 VALUES (:etat, :unit, :emp, :date, :id_cat, :id_plante)');

            $result = $req->execute([
                'etat' => $capteur->getEtatCapteur(),
                'unit' => $capteur->getUniteCapteur(),
                'emp' => $capteur->getEmplacement(),
                'date' => $capteur->getDateInstallation(),
                'id_cat' => $capteur->getIdCategorie(),
                'id_plante' => $capteur->getIdPlante()
            ]);

            return $result;

        } catch (Exception $e) {
            error_log('Erreur addCapteur: ' . $e->getMessage());
            throw $e;
        }
    }

    // Afficher tous les capteurs
    public function showCapteur() {
        $db = config::getConnexion();

        try {
            $liste = $db->query('SELECT * FROM capteur ORDER BY id_capteur');
            return $liste->fetchAll();
        } catch (Exception $e) {
            error_log('Erreur showCapteur: ' . $e->getMessage());
            throw $e;
        }
    }

    // Afficher tous les capteurs avec leurs catégories et plantes (JOIN)
    public function showCapteurWithDetails() {
        $db = config::getConnexion();

        try {
            $query = 'SELECT c.*, 
                             cat.nom_categorie, 
                             p.nom_plante 
                      FROM capteur c
                      LEFT JOIN categorie cat ON c.id_categorie = cat.id_categorie
                      LEFT JOIN plante p ON c.id_plante = p.id_plante
                      ORDER BY c.id_capteur';
            
            $liste = $db->query($query);
            return $liste->fetchAll();
        } catch (Exception $e) {
            error_log('Erreur showCapteurWithDetails: ' . $e->getMessage());
            throw $e;
        }
    }

    // Afficher les capteurs filtrés par catégorie
    public function showCapteurByCategorie($id_categorie) {
        $db = config::getConnexion();

        try {
            $query = 'SELECT c.*, 
                             cat.nom_categorie, 
                             p.nom_plante 
                      FROM capteur c
                      LEFT JOIN categorie cat ON c.id_categorie = cat.id_categorie
                      LEFT JOIN plante p ON c.id_plante = p.id_plante
                      WHERE c.id_categorie = :id_categorie
                      ORDER BY c.id_capteur';
            
            $req = $db->prepare($query);
            $req->execute(['id_categorie' => $id_categorie]);
            return $req->fetchAll();
        } catch (Exception $e) {
            error_log('Erreur showCapteurByCategorie: ' . $e->getMessage());
            throw $e;
        }
    }

    // Rechercher des capteurs par terme de recherche
    public function searchCapteur($searchTerm, $id_categorie = null) {
        $db = config::getConnexion();

        try {
            $query = 'SELECT c.*, 
                             cat.nom_categorie, 
                             p.nom_plante 
                      FROM capteur c
                      LEFT JOIN categorie cat ON c.id_categorie = cat.id_categorie
                      LEFT JOIN plante p ON c.id_plante = p.id_plante
                      WHERE (c.id_capteur LIKE :searchTerm 
                         OR c.etatCapteur LIKE :searchTerm
                         OR c.uniteCapteur LIKE :searchTerm
                         OR c.emplacement LIKE :searchTerm
                         OR cat.nom_categorie LIKE :searchTerm
                         OR p.nom_plante LIKE :searchTerm
                         OR c.dateInstallation LIKE :searchTerm)';
            
            if ($id_categorie !== null) {
                $query .= ' AND c.id_categorie = :id_categorie';
            }
            
            $query .= ' ORDER BY c.id_capteur';
            
            $req = $db->prepare($query);
            
            $params = ['searchTerm' => '%' . $searchTerm . '%'];
            if ($id_categorie !== null) {
                $params['id_categorie'] = $id_categorie;
            }
            
            $req->execute($params);
            return $req->fetchAll();
            
        } catch (Exception $e) {
            error_log('Erreur searchCapteur: ' . $e->getMessage());
            throw $e;
        }
    }

    // Filtrer et rechercher des capteurs avec options avancées
    public function filterCapteurs($searchTerm = '', $filterEtat = 'tous', $filterCategorie = null, $filterPlante = 'tous', $orderBy = 'id', $orderDir = 'ASC') {
        $db = config::getConnexion();

        try {
            $query = 'SELECT c.*, 
                             cat.nom_categorie, 
                             p.nom_plante,
                             sd.temperature,
                             sd.humidite,
                             sd.timestamp as last_reading
                      FROM capteur c
                      LEFT JOIN categorie cat ON c.id_categorie = cat.id_categorie
                      LEFT JOIN plante p ON c.id_plante = p.id_plante
                      LEFT JOIN sensor_data sd ON c.id_capteur = sd.id_capteur 
                          AND sd.timestamp = (
                              SELECT MAX(timestamp) 
                              FROM sensor_data 
                              WHERE id_capteur = c.id_capteur
                          )
                      WHERE 1=1';

            $params = [];

            if (!empty($searchTerm)) {
                $query .= ' AND (c.id_capteur LIKE :searchTerm 
                           OR c.etatCapteur LIKE :searchTerm
                           OR c.uniteCapteur LIKE :searchTerm
                           OR c.emplacement LIKE :searchTerm
                           OR cat.nom_categorie LIKE :searchTerm
                           OR p.nom_plante LIKE :searchTerm
                           OR c.dateInstallation LIKE :searchTerm)';
                $params['searchTerm'] = '%' . $searchTerm . '%';
            }

            if ($filterEtat !== 'tous') {
                $query .= ' AND c.etatCapteur = :etat';
                $params['etat'] = $filterEtat;
            }

            if ($filterCategorie !== null && $filterCategorie !== '' && $filterCategorie !== 'tous') {
                $query .= ' AND c.id_categorie = :id_categorie';
                $params['id_categorie'] = $filterCategorie;
            }

            if ($filterPlante === 'avec') {
                $query .= ' AND c.id_plante IS NOT NULL';
            } elseif ($filterPlante === 'sans') {
                $query .= ' AND c.id_plante IS NULL';
            }

            $validOrders = [
                'id' => 'c.id_capteur', 
                'categorie' => 'cat.nom_categorie', 
                'etat' => 'c.etatCapteur',
                'emplacement' => 'c.emplacement',
                'date' => 'c.dateInstallation'
            ];
            $orderColumn = $validOrders[$orderBy] ?? 'c.id_capteur';
            $orderDirection = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
            $query .= " ORDER BY $orderColumn $orderDirection";

            $req = $db->prepare($query);
            $req->execute($params);
            return $req->fetchAll();
            
        } catch (Exception $e) {
            error_log('Erreur filterCapteurs: ' . $e->getMessage());
            throw $e;
        }
    }

    // Supprimer un capteur
    public function deleteCapteur($id) {
        $db = config::getConnexion();

        try {
            $req = $db->prepare('DELETE FROM capteur WHERE id_capteur = :id');
            $result = $req->execute(['id' => $id]);
            
            return $req->rowCount();
            
        } catch (Exception $e) {
            error_log('Erreur deleteCapteur: ' . $e->getMessage());
            throw $e;
        }
    }

    // Récupérer un capteur par ID
    public function getCapteur($id) {
        $db = config::getConnexion();

        try {
            $req = $db->prepare('SELECT * FROM capteur WHERE id_capteur = :id');
            $req->execute(['id' => $id]);
            return $req->fetch();
        } catch (Exception $e) {
            error_log('Erreur getCapteur: ' . $e->getMessage());
            throw $e;
        }
    }

    // Modifier un capteur
    public function updateCapteur($capteur, $id) {
        $db = config::getConnexion();

        try {
            $req = $db->prepare('UPDATE capteur 
                                 SET etatCapteur = :etat, 
                                     uniteCapteur = :unit, 
                                     emplacement = :emp, 
                                     dateInstallation = :date,
                                     id_categorie = :id_cat,
                                     id_plante = :id_plante
                                 WHERE id_capteur = :id');
            
            $result = $req->execute([
                'id' => $id,
                'etat' => $capteur->getEtatCapteur(),
                'unit' => $capteur->getUniteCapteur(),
                'emp' => $capteur->getEmplacement(),
                'date' => $capteur->getDateInstallation(),
                'id_cat' => $capteur->getIdCategorie(),
                'id_plante' => $capteur->getIdPlante()
            ]);

            return $req->rowCount();
            
        } catch (Exception $e) {
            error_log('Erreur updateCapteur: ' . $e->getMessage());
            throw $e;
        }
    }

    // Récupérer toutes les catégories
    public function getAllCategories() {
        $db = config::getConnexion();

        try {
            $req = $db->query('SELECT id_categorie, nom_categorie FROM categorie ORDER BY nom_categorie');
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Erreur getAllCategories: ' . $e->getMessage());
            throw $e;
        }
    }

    // Récupérer toutes les plantes
    public function getAllPlantes() {
        $db = config::getConnexion();

        try {
            $req = $db->query('SELECT id_plante, nom_plante FROM plante ORDER BY nom_plante');
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Erreur getAllPlantes: ' . $e->getMessage());
            throw $e;
        }
    }

    // ========== MÉTHODES POUR RECOMMANDATIONS IA ==========
    
    /**
     * Générer une recommandation IA pour un capteur via Claude API
     */
    public function genererRecommandationIA($id_capteur) {
        try {
            // Récupérer le capteur
            $capteur = $this->getCapteur($id_capteur);
            
            if (!$capteur) {
                throw new Exception('Capteur introuvable');
            }
            
            // Récupérer les détails complets
            $details = $this->getCapteurWithDetails($id_capteur);
            
            // Préparer le contexte
            $contexte = $this->preparerContexteCapteur($capteur, $details);
            
            // Appeler Claude API
            $recommandationText = $this->appelClaudeAPI($contexte);
            
            // Extraire et parser le JSON
            $recommandationJSON = $this->extraireEtValiderJSON($recommandationText);
            
            return [
                'success' => true,
                'recommandation' => $recommandationJSON,
                'capteur' => $details,
                'contexte' => $contexte
            ];
            
        } catch (Exception $e) {
            error_log('❌ Erreur genererRecommandationIA: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Récupérer un capteur avec tous ses détails
     */
    private function getCapteurWithDetails($id_capteur) {
        $db = config::getConnexion();
        
        try {
            $query = 'SELECT c.*, 
                             cat.nom_categorie, 
                             p.nom_plante 
                      FROM capteur c
                      LEFT JOIN categorie cat ON c.id_categorie = cat.id_categorie
                      LEFT JOIN plante p ON c.id_plante = p.id_plante
                      WHERE c.id_capteur = :id';
            
            $req = $db->prepare($query);
            $req->execute(['id' => $id_capteur]);
            return $req->fetch();
        } catch (Exception $e) {
            error_log('Erreur getCapteurWithDetails: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Préparer le contexte du capteur pour l'analyse IA
     */
    private function preparerContexteCapteur($capteur, $details) {
        $contexte = [
            'id' => $capteur['id_capteur'],
            'categorie' => $details['nom_categorie'] ?? 'Non définie',
            'unite' => $capteur['uniteCapteur'] ?? 'N/A',
            'etat' => $capteur['etatCapteur'] ?? 'inconnu',
            'emplacement' => $capteur['emplacement'] ?? 'Non défini',
            'date_installation' => $capteur['dateInstallation'] ?? 'Inconnue',
            'plante_associee' => $details['nom_plante'] ?? 'Aucune'
        ];
        
        if ($capteur['dateInstallation']) {
            try {
                $dateInstall = new DateTime($capteur['dateInstallation']);
                $aujourd_hui = new DateTime();
                $anciennete = $aujourd_hui->diff($dateInstall);
                $contexte['anciennete_jours'] = $anciennete->days;
                $contexte['anciennete_format'] = $this->formaterAnciennete($anciennete);
            } catch (Exception $e) {
                $contexte['anciennete_format'] = 'Inconnue';
            }
        }
        
        return $contexte;
    }
    
    /**
     * Formater l'ancienneté de manière lisible
     */
    private function formaterAnciennete($anciennete) {
        if ($anciennete->y > 0) {
            return $anciennete->y . ' an' . ($anciennete->y > 1 ? 's' : '') . 
                   ($anciennete->m > 0 ? ' et ' . $anciennete->m . ' mois' : '');
        } elseif ($anciennete->m > 0) {
            return $anciennete->m . ' mois';
        } else {
            return $anciennete->days . ' jour' . ($anciennete->days > 1 ? 's' : '');
        }
    }
    
    /**
     * Appeler l'API Claude
     */
    private function appelClaudeAPI($contexte) {
        $claudeApiKey = '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';
        
        $prompt = $this->construirePrompt($contexte);
        
        $data = [
            'model' => 'claude-sonnet-4-20250514',
            'max_tokens' => 1024,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ]
        ];
        
        $ch = curl_init('https://api.anthropic.com/v1/messages');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-api-key: ' . $claudeApiKey,
            'anthropic-version: 2023-06-01'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('Erreur CURL: ' . $error);
        }
        
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception("Erreur API Claude: HTTP $httpCode - $response");
        }
        
        $result = json_decode($response, true);
        
        if (isset($result['content'][0]['text'])) {
            return $result['content'][0]['text'];
        }
        
        throw new Exception("Réponse API invalide");
    }
    
    /**
     * Extraire et valider le JSON d'une réponse texte (VERSION AMÉLIORÉE)
     */
    private function extraireEtValiderJSON($texte) {
        error_log('=== EXTRACTION JSON ===');
        error_log('Texte original: ' . substr($texte, 0, 200));
        
        // Nettoyer le texte
        $texte = trim($texte);
        
        // Retirer les balises markdown si présentes
        $texte = preg_replace('/```json\s*/i', '', $texte);
        $texte = preg_replace('/```\s*$/i', '', $texte);
        $texte = preg_replace('/```/i', '', $texte);
        
        // Trouver les accolades de début et fin
        $debut = strpos($texte, '{');
        $fin = strrpos($texte, '}');
        
        if ($debut !== false && $fin !== false && $fin > $debut) {
            $jsonStr = substr($texte, $debut, $fin - $debut + 1);
            error_log('JSON extrait: ' . $jsonStr);
            
            // Essayer de parser le JSON
            $jsonData = json_decode($jsonStr, true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
                error_log('✅ JSON valide extrait!');
                
                // Valider et sécuriser les données
                $recommandation = $this->validerRecommandation($jsonData);
                
                // Retourner le JSON string valide
                return json_encode($recommandation, JSON_UNESCAPED_UNICODE);
            } else {
                error_log('⚠️ Erreur parsing JSON: ' . json_last_error_msg());
            }
        }
        
        // Si échec, créer une recommandation par défaut
        error_log('⚠️ Création d\'une recommandation par défaut');
        return json_encode($this->creerRecommandationParDefaut(), JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Valider et nettoyer les données de recommandation
     */
    private function validerRecommandation($data) {
        return [
            'titre' => isset($data['titre']) && !empty($data['titre']) 
                ? strip_tags(trim($data['titre'])) 
                : 'Recommandation de maintenance',
            
            'priorite' => isset($data['priorite']) && in_array($data['priorite'], ['basse', 'moyenne', 'haute', 'critique'])
                ? $data['priorite']
                : 'moyenne',
            
            'diagnostic' => isset($data['diagnostic']) && !empty($data['diagnostic'])
                ? strip_tags(trim($data['diagnostic']))
                : 'Le capteur nécessite une vérification.',
            
            'actions' => isset($data['actions']) && is_array($data['actions']) && count($data['actions']) > 0
                ? array_map(function($action) { return strip_tags(trim($action)); }, $data['actions'])
                : ['Vérifier le fonctionnement', 'Effectuer une maintenance'],
            
            'benefices' => isset($data['benefices']) && !empty($data['benefices'])
                ? strip_tags(trim($data['benefices']))
                : 'Amélioration des performances',
            
            'delai' => isset($data['delai']) && !empty($data['delai'])
                ? strip_tags(trim($data['delai']))
                : 'Dans les 7 jours'
        ];
    }
    
    /**
     * Créer une recommandation par défaut en cas d'échec
     */
    private function creerRecommandationParDefaut() {
        return [
            'titre' => 'Maintenance recommandée',
            'priorite' => 'moyenne',
            'diagnostic' => 'Une vérification de routine est recommandée pour assurer le bon fonctionnement de votre capteur.',
            'actions' => [
                'Vérifier l\'état physique du capteur',
                'Contrôler les connexions électriques',
                'Tester les valeurs mesurées',
                'Nettoyer les composants si nécessaire'
            ],
            'benefices' => 'Garantir la fiabilité et la précision des mesures',
            'delai' => 'Dans les 7 jours'
        ];
    }
    
    /**
     * Construire le prompt pour Claude (VERSION SIMPLIFIÉE)
     */
    private function construirePrompt($contexte) {
        $prompt = "Tu es un expert en maintenance de capteurs IoT pour jardins intelligents.\n\n";
        
        $prompt .= "IMPORTANT: Réponds UNIQUEMENT avec un objet JSON valide, sans texte avant ni après.\n\n";
        
        $prompt .= "Analyse ce capteur et génère une recommandation:\n";
        $prompt .= "- ID: {$contexte['id']}\n";
        $prompt .= "- Catégorie: {$contexte['categorie']}\n";
        $prompt .= "- Unité: {$contexte['unite']}\n";
        $prompt .= "- État: {$contexte['etat']}\n";
        $prompt .= "- Emplacement: {$contexte['emplacement']}\n";
        $prompt .= "- Date installation: {$contexte['date_installation']}\n";
        
        if (isset($contexte['anciennete_format'])) {
            $prompt .= "- Ancienneté: {$contexte['anciennete_format']}\n";
        }
        
        $prompt .= "- Plante associée: {$contexte['plante_associee']}\n\n";
        
        $prompt .= "Réponds avec ce format JSON exact:\n";
        $prompt .= "{\n";
        $prompt .= '  "titre": "titre court",'."\n";
        $prompt .= '  "priorite": "basse|moyenne|haute|critique",'."\n";
        $prompt .= '  "diagnostic": "analyse détaillée",'."\n";
        $prompt .= '  "actions": ["action1", "action2", "action3"],'."\n";
        $prompt .= '  "benefices": "bénéfices attendus",'."\n";
        $prompt .= '  "delai": "délai recommandé"'."\n";
        $prompt .= "}\n\n";
        
        $prompt .= "Règles:\n";
        $prompt .= "- Si état 'defectueux' ou 'maintenance' → priorité haute/critique\n";
        $prompt .= "- Si ancienneté > 1 an → suggérer calibration\n";
        $prompt .= "- Tout en français\n";
        $prompt .= "- Réponse JSON uniquement";
        
        return $prompt;
    }
    
    /**
     * Récupérer tous les utilisateurs
     */
    public function getAllUtilisateurs() {
        $db = config::getConnexion();
        
        try {
            $req = $db->query('SELECT idUtilisateur, nom, email, localisation FROM utilisateur ORDER BY nom');
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Erreur getAllUtilisateurs: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Envoyer un email avec PHPMailer (SMTP)
     */
    public function envoyerEmailRecommandation($emailDest, $nomDest, $sujet, $contenuHTML) {
        try {
            $mail = new PHPMailer(true);
            
            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'rayenzinoubi10@gmail.com';
            $mail->Password   = 'kekjyjvweppycvep';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            
            // Encodage
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            
            // Expéditeur
            $mail->setFrom('rayenzinoubi10@gmail.com', 'SmartGarden AI');
            
            // Destinataire
            $mail->addAddress($emailDest, $nomDest);
            
            // Contenu
            $mail->isHTML(true);
            $mail->Subject = $sujet;
            $mail->Body    = $contenuHTML;
            $mail->AltBody = strip_tags($contenuHTML);
            
            // Envoyer
            $mail->send();
            
            error_log("✅ Email envoyé avec succès à: $emailDest");
            return true;
            
        } catch (Exception $e) {
            error_log("❌ Erreur envoi email: {$mail->ErrorInfo}");
            error_log("Exception: " . $e->getMessage());
            return false;
        }
    }

}

?>