<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/Commentaire.php';

class CommentaireService {
    private $db;

    public function __construct() {
        $this->db = config::getConnexion();
    }

    // Validation complète sans HTML5
    public function validateCommentaireData($commentaire) {
        $errors = [];
        
        // Validation du contenu
        $contenu = $commentaire->getContenuCommentaire();
        if (empty($contenu) || trim($contenu) === '') {
            $errors[] = "Le commentaire ne peut pas être vide.";
        } elseif (strlen($contenu) > 500) {
            $errors[] = "Le commentaire ne doit pas dépasser 500 caractères.";
        } elseif (preg_match('/<[^>]*>/', $contenu)) {
            $errors[] = "Le commentaire ne doit pas contenir de balises HTML.";
        }
        
        // Validation des ID
        if (!filter_var($commentaire->getIdPublication(), FILTER_VALIDATE_INT) || 
            $commentaire->getIdPublication() <= 0) {
            $errors[] = "L'ID de publication est invalide.";
        }
        
        if (!filter_var($commentaire->getIdUtilisateur(), FILTER_VALIDATE_INT) || 
            $commentaire->getIdUtilisateur() <= 0) {
            $errors[] = "L'ID d'utilisateur est invalide.";
        }
        
        return $errors;
    }

    // Nettoyage des données
    private function sanitizeData($data) {
        if (is_string($data)) {
            // Supprimer les espaces inutiles
            $data = trim($data);
            // Échapper les caractères spéciaux
            $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
            // Supprimer les balises HTML/JavaScript
            $data = strip_tags($data);
            // Échapper les backslashes
            $data = addslashes($data);
        }
        return $data;
    }

    // Ajouter un commentaire avec validation PDO
    public function addCommentaire($commentaire) {
        $errors = $this->validateCommentaireData($commentaire);
        if (!empty($errors)) {
            throw new Exception(implode(" | ", $errors));
        }
        
        // Nettoyage des données
        $contenuNettoye = $this->sanitizeData($commentaire->getContenuCommentaire());
        
        // Vérifier que la publication existe
        $sqlCheckPub = "SELECT COUNT(*) FROM publication WHERE idPublication = :idPublication";
        $stmtCheckPub = $this->db->prepare($sqlCheckPub);
        $stmtCheckPub->execute([':idPublication' => $commentaire->getIdPublication()]);
        
        if ($stmtCheckPub->fetchColumn() == 0) {
            throw new Exception("La publication spécifiée n'existe pas.");
        }
        
        // Vérifier que l'utilisateur existe
        $sqlCheckUser = "SELECT COUNT(*) FROM utilisateur WHERE idUtilisateur = :idUtilisateur";
        $stmtCheckUser = $this->db->prepare($sqlCheckUser);
        $stmtCheckUser->execute([':idUtilisateur' => $commentaire->getIdUtilisateur()]);
        
        if ($stmtCheckUser->fetchColumn() == 0) {
            throw new Exception("L'utilisateur spécifié n'existe pas.");
        }
        
        // Préparation de la requête d'insertion
        $sql = "INSERT INTO commentaire (
            contenuCommentaire, 
            dateCommentaire, 
            idPublication, 
            idUtilisateur, 
            statut_moderation, 
            raison_moderation, 
            date_moderation, 
            status, 
            created_at
        ) VALUES (
            :contenuCommentaire, 
            :dateCommentaire, 
            :idPublication, 
            :idUtilisateur, 
            :statut_moderation, 
            :raison_moderation, 
            :date_moderation, 
            :status, 
            :created_at
        )";
        
        $now = date('Y-m-d H:i:s');
        
        try {
            $stmt = $this->db->prepare($sql);
            
            // Préparation des paramètres
            $params = [
                ':contenuCommentaire' => $contenuNettoye,
                ':dateCommentaire' => $commentaire->getDateCommentaire() ?? $now,
                ':idPublication' => (int)$commentaire->getIdPublication(),
                ':idUtilisateur' => (int)$commentaire->getIdUtilisateur(),
                ':statut_moderation' => $this->sanitizeData($commentaire->getStatutModeration() ?? 'en_attente'),
                ':raison_moderation' => $this->sanitizeData($commentaire->getRaisonModeration()),
                ':date_moderation' => $commentaire->getDateModeration(),
                ':status' => $commentaire->estApprouve() ? 1 : 0,
                ':created_at' => $now
            ];
            
            // Exécution avec vérification
            $result = $stmt->execute($params);
            
            if ($result) {
                return $this->db->lastInsertId();
            } else {
                $errorInfo = $stmt->errorInfo();
                throw new Exception("Erreur d'insertion: " . $errorInfo[2]);
            }
        } catch (PDOException $e) {
            throw new Exception("Erreur PDO: " . $e->getMessage());
        }
    }

    // Récupérer commentaires par publication
    public function getCommentairesByPublication($idPublication, $onlyApproved = false) {
        // Validation de l'ID
        if (!filter_var($idPublication, FILTER_VALIDATE_INT) || $idPublication <= 0) {
            throw new Exception("ID de publication invalide.");
        }
        
        $sql = "SELECT c.*, u.nom, u.prenom 
                FROM commentaire c
                LEFT JOIN utilisateur u ON c.idUtilisateur = u.idUtilisateur
                WHERE c.idPublication = :idPublication";
        
        if ($onlyApproved) {
            $sql .= " AND (c.statut_moderation = 'approuve' OR c.status = 1)";
        }
        
        $sql .= " ORDER BY c.dateCommentaire ASC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':idPublication' => (int)$idPublication]);
            
            $result = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Nettoyage des données avant création de l'objet
                foreach ($row as $key => $value) {
                    $row[$key] = $this->sanitizeData($value);
                }
                
                $commentaire = new Commentaire(
                    (int)$row['idCommentaire'],
                    $row['contenuCommentaire'],
                    $row['dateCommentaire'],
                    (int)$row['idPublication'],
                    (int)$row['idUtilisateur'],
                    $row['nom'] ?? null,
                    $row['prenom'] ?? null
                );
                
                $result[] = $commentaire;
            }
            
            return $result;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération: " . $e->getMessage());
        }
    }

    // Modifier commentaire avec validation
    public function updateCommentaire($idCommentaire, $nouveauContenu) {
        // Validation des paramètres
        if (!filter_var($idCommentaire, FILTER_VALIDATE_INT) || $idCommentaire <= 0) {
            throw new Exception("ID de commentaire invalide.");
        }
        
        $contenu = trim($nouveauContenu);
        if (empty($contenu)) {
            throw new Exception("Le commentaire ne peut pas être vide.");
        }
        
        if (strlen($contenu) > 500) {
            throw new Exception("Le commentaire ne doit pas dépasser 500 caractères.");
        }
        
        if (preg_match('/<[^>]*>/', $contenu)) {
            throw new Exception("Le commentaire ne doit pas contenir de balises HTML.");
        }
        
        // Nettoyage
        $contenuNettoye = $this->sanitizeData($contenu);
        
        // Vérifier que le commentaire existe
        $sqlCheck = "SELECT COUNT(*) FROM commentaire WHERE idCommentaire = :id";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute([':id' => $idCommentaire]);
        
        if ($stmtCheck->fetchColumn() == 0) {
            throw new Exception("Le commentaire spécifié n'existe pas.");
        }
        
        // Mise à jour
        $sql = "UPDATE commentaire 
                SET contenuCommentaire = :contenu, 
                    date_moderation = :date_mod
                WHERE idCommentaire = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $params = [
                ':contenu' => $contenuNettoye,
                ':date_mod' => date('Y-m-d H:i:s'),
                ':id' => (int)$idCommentaire
            ];
            
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la modification: " . $e->getMessage());
        }
    }

    // Supprimer commentaire avec vérification
    public function deleteCommentaire($idCommentaire) {
        // Validation de l'ID
        if (!filter_var($idCommentaire, FILTER_VALIDATE_INT) || $idCommentaire <= 0) {
            throw new Exception("ID de commentaire invalide.");
        }
        
        // Vérifier que le commentaire existe
        $sqlCheck = "SELECT COUNT(*) FROM commentaire WHERE idCommentaire = :id";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute([':id' => $idCommentaire]);
        
        if ($stmtCheck->fetchColumn() == 0) {
            throw new Exception("Le commentaire spécifié n'existe pas.");
        }
        
        $sql = "DELETE FROM commentaire WHERE idCommentaire = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => (int)$idCommentaire]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression: " . $e->getMessage());
        }
    }

    // Modération : approuver
    public function approveCommentaire($idCommentaire, $raison = null) {
        if (!filter_var($idCommentaire, FILTER_VALIDATE_INT) || $idCommentaire <= 0) {
            throw new Exception("ID de commentaire invalide.");
        }
        
        $raisonNettoyee = $this->sanitizeData($raison);
        $sql = "UPDATE commentaire 
                SET statut_moderation = 'approuve', 
                    raison_moderation = :raison, 
                    date_moderation = :now, 
                    status = 1 
                WHERE idCommentaire = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $params = [
                ':raison' => $raisonNettoyee,
                ':now' => date('Y-m-d H:i:s'),
                ':id' => (int)$idCommentaire
            ];
            
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de l'approbation: " . $e->getMessage());
        }
    }

    // Modération : rejeter
    public function rejectCommentaire($idCommentaire, $raison) {
        if (!filter_var($idCommentaire, FILTER_VALIDATE_INT) || $idCommentaire <= 0) {
            throw new Exception("ID de commentaire invalide.");
        }
        
        if (empty(trim($raison))) {
            throw new Exception("Une raison de rejet est obligatoire.");
        }
        
        $raisonNettoyee = $this->sanitizeData($raison);
        $sql = "UPDATE commentaire 
                SET statut_moderation = 'rejete', 
                    raison_moderation = :raison, 
                    date_moderation = :now, 
                    status = 0 
                WHERE idCommentaire = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $params = [
                ':raison' => $raisonNettoyee,
                ':now' => date('Y-m-d H:i:s'),
                ':id' => (int)$idCommentaire
            ];
            
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du rejet: " . $e->getMessage());
        }
    }

    // Compter commentaires d'une publication
    public function countCommentairesByPublication($idPublication, $onlyApproved = false) {
        if (!filter_var($idPublication, FILTER_VALIDATE_INT) || $idPublication <= 0) {
            throw new Exception("ID de publication invalide.");
        }
        
        $sql = "SELECT COUNT(*) as nb FROM commentaire WHERE idPublication = :idPublication";
        
        if ($onlyApproved) {
            $sql .= " AND (statut_moderation = 'approuve' OR status = 1)";
        }
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':idPublication' => (int)$idPublication]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $row ? (int)$row['nb'] : 0;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du comptage: " . $e->getMessage());
        }
    }

    // Méthode supplémentaire: Récupérer un commentaire par ID
    public function getCommentaireById($idCommentaire) {
        if (!filter_var($idCommentaire, FILTER_VALIDATE_INT) || $idCommentaire <= 0) {
            throw new Exception("ID de commentaire invalide.");
        }
        
        $sql = "SELECT c.*, u.nom, u.prenom 
                FROM commentaire c
                LEFT JOIN utilisateur u ON c.idUtilisateur = u.idUtilisateur
                WHERE c.idCommentaire = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => (int)$idCommentaire]);
            
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Nettoyage des données
                foreach ($row as $key => $value) {
                    $row[$key] = $this->sanitizeData($value);
                }
                
                return new Commentaire(
                    (int)$row['idCommentaire'],
                    $row['contenuCommentaire'],
                    $row['dateCommentaire'],
                    (int)$row['idPublication'],
                    (int)$row['idUtilisateur'],
                    $row['nom'] ?? null,
                    $row['prenom'] ?? null
                );
            }
            
            return null;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération: " . $e->getMessage());
        }
    }

    // Méthode pour vérifier si un utilisateur peut modifier un commentaire
    public function canUserEditCommentaire($idCommentaire, $idUtilisateur) {
        if (!filter_var($idCommentaire, FILTER_VALIDATE_INT) || $idCommentaire <= 0) {
            throw new Exception("ID de commentaire invalide.");
        }
        
        if (!filter_var($idUtilisateur, FILTER_VALIDATE_INT) || $idUtilisateur <= 0) {
            throw new Exception("ID d'utilisateur invalide.");
        }
        
        $sql = "SELECT COUNT(*) FROM commentaire 
                WHERE idCommentaire = :idCommentaire 
                AND idUtilisateur = :idUtilisateur";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':idCommentaire' => (int)$idCommentaire,
                ':idUtilisateur' => (int)$idUtilisateur
            ]);
            
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erreur de vérification: " . $e->getMessage());
        }
    }
}