<?php

if (!class_exists('suggestionC')) {
    class suggestionC {
        
        private $connexion;
        
        public function __construct() {
            include_once '../../config.php';
            $this->connexion = $GLOBALS['connexion'] ?? null;
        }
        
        // Ajouter une suggestion
        public function addSuggestion($idUtilisateur, $nomPlante, $typeType, $description, $imagePath = null, $temperature = 20.0, $besoin_eau = 500, $niveau_humidite = 60, $etat_sante = 'Bon état') {
            try {
                $query = "INSERT INTO suggestionplante (id_utilisateur, nom_plante, type_plante, description, image, temperature, besoin_eau, niveau_humidite, etat_sante, date_suggestion, statut) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'En attente')";
                
                $stmt = $this->connexion->prepare($query);
                
                if (!$stmt) {
                    return ['success' => false, 'message' => 'Erreur de préparation: ' . $this->connexion->error];
                }
                
                $stmt->bind_param('issssdiis', $idUtilisateur, $nomPlante, $typeType, $description, $imagePath, $temperature, $besoin_eau, $niveau_humidite, $etat_sante);
                
                if ($stmt->execute()) {
                    return ['success' => true, 'message' => 'Suggestion ajoutée avec succès!'];
                } else {
                    return ['success' => false, 'message' => 'Erreur lors de l\'insertion: ' . $stmt->error];
                }
            } catch (Exception $e) {
                return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
            }
        }
        
        // Lister toutes les suggestions
        public function listSuggestions($filter = null) {
            try {
                $query = "SELECT s.*, u.nom as utilisateur_nom 
                         FROM suggestionplante s
                         LEFT JOIN utilisateur u ON s.id_utilisateur = u.idUtilisateur";
                
                if ($filter && $filter !== 'Toutes') {
                    $query .= " WHERE s.statut = '" . $this->connexion->real_escape_string($filter) . "'";
                }
                
                $query .= " ORDER BY s.date_suggestion DESC";
                
                $result = $this->connexion->query($query);
                
                if (!$result) {
                    return [];
                }
                
                $suggestions = [];
                while ($row = $result->fetch_assoc()) {
                    $suggestions[] = $row;
                }
                return $suggestions;
            } catch (Exception $e) {
                return [];
            }
        }
        
        // Accepter une suggestion et la convertir en plante
        public function acceptSuggestion($idSuggestion, $idUtilisateur = null) {
            try {
                // Récupérer la suggestion
                $query = "SELECT * FROM suggestionplante WHERE id_suggestion = ?";
                $stmt = $this->connexion->prepare($query);
                $stmt->bind_param('i', $idSuggestion);
                $stmt->execute();
                $result = $stmt->get_result();
                $suggestion = $result->fetch_assoc();
                
                if (!$suggestion) {
                    return ['success' => false, 'message' => 'Suggestion non trouvée'];
                }
                
                // Créer la plante
                $queryPlante = "INSERT INTO plante (nom_plante, type_plante, description, etat_sante, humidite, id_utilisateur, image, date_ajout) 
                               VALUES (?, ?, ?, 'Bon état', 50, ?, ?, NOW())";
                
                $stmtPlante = $this->connexion->prepare($queryPlante);
                
                if (!$stmtPlante) {
                    return ['success' => false, 'message' => 'Erreur: ' . $this->connexion->error];
                }
                
                $userId = $idUtilisateur ?? $suggestion['id_utilisateur'];
                $stmtPlante->bind_param('sssss', 
                    $suggestion['nom_plante'], 
                    $suggestion['type_plante'],
                    $suggestion['description'],
                    $userId,
                    $suggestion['image']
                );
                
                if ($stmtPlante->execute()) {
                    // Mettre à jour le statut de la suggestion
                    $updateQuery = "UPDATE suggestionplante SET statut = 'Acceptée', date_traitement = NOW() WHERE id_suggestion = ?";
                    $stmtUpdate = $this->connexion->prepare($updateQuery);
                    $stmtUpdate->bind_param('i', $idSuggestion);
                    $stmtUpdate->execute();
                    
                    return ['success' => true, 'message' => 'Suggestion acceptée et plante créée!'];
                } else {
                    return ['success' => false, 'message' => 'Erreur lors de la création: ' . $stmtPlante->error];
                }
            } catch (Exception $e) {
                return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
            }
        }
        
        // Rejeter une suggestion
        public function rejectSuggestion($idSuggestion) {
            try {
                $query = "UPDATE suggestionplante SET statut = 'Rejetée', date_traitement = NOW() WHERE id_suggestion = ?";
                $stmt = $this->connexion->prepare($query);
                $stmt->bind_param('i', $idSuggestion);
                
                if ($stmt->execute()) {
                    return ['success' => true, 'message' => 'Suggestion rejetée'];
                } else {
                    return ['success' => false, 'message' => 'Erreur: ' . $stmt->error];
                }
            } catch (Exception $e) {
                return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
            }
        }
        
        // Supprimer une suggestion
        public function deleteSuggestion($idSuggestion) {
            try {
                $query = "DELETE FROM suggestionplante WHERE id_suggestion = ?";
                $stmt = $this->connexion->prepare($query);
                $stmt->bind_param('i', $idSuggestion);
                
                if ($stmt->execute()) {
                    return ['success' => true, 'message' => 'Suggestion supprimée'];
                } else {
                    return ['success' => false, 'message' => 'Erreur: ' . $stmt->error];
                }
            } catch (Exception $e) {
                return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
            }
        }
        
        // Compter les suggestions en attente
        public function countPendingSuggestions() {
            try {
                $query = "SELECT COUNT(*) as count FROM suggestionplante WHERE statut = 'En attente'";
                $result = $this->connexion->query($query);
                $row = $result->fetch_assoc();
                return $row['count'] ?? 0;
            } catch (Exception $e) {
                return 0;
            }
        }
    }
}
?>
