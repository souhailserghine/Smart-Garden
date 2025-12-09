<?php
include '/../config.php';
include '/publication.php';

class PublicationService {
    private $db;

    public function __construct() {
        $this->db = config::getConnexion();
    }

    // Nettoyage des données
    private function sanitizeData($data) {
        if (is_string($data)) {
            $data = trim($data);
            $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
            $data = strip_tags($data);
            $data = addslashes($data);
        }
        return $data;
    }

    // Validation complète des données de publication
    public function validatePublicationData($publication) {
        $errors = [];
        
        // Validation du contenu texte
        $contenu = $publication->getContenuTexte();
        if (empty($contenu) || trim($contenu) === '') {
            $errors[] = "Le contenu de la publication ne peut pas être vide.";
        } elseif (strlen($contenu) > 5000) {
            $errors[] = "Le contenu ne doit pas dépasser 5000 caractères.";
        } elseif (preg_match('/<[^>]*>/', $contenu)) {
            $errors[] = "Le contenu ne doit pas contenir de balises HTML.";
        }
        
        // Validation de l'ID utilisateur
        if (!filter_var($publication->getIdUtilisateur(), FILTER_VALIDATE_INT) || 
            $publication->getIdUtilisateur() <= 0) {
            $errors[] = "L'ID d'utilisateur est invalide.";
        }
        
        // Validation du nombre de likes
        $nbLikes = $publication->getNbLikes();
        if (!is_numeric($nbLikes) || $nbLikes < 0) {
            $errors[] = "Le nombre de likes doit être un nombre positif.";
        }
        
        // Validation des images (si présentes)
        $images = $publication->getImages();
        if (!empty($images)) {
            if (!is_string($images)) {
                $errors[] = "Les images doivent être une chaîne de caractères.";
            } elseif (strlen($images) > 1000) {
                $errors[] = "Les chemins d'images sont trop longs.";
            }
        }
        
        // Validation des vidéos (si présentes)
        $videos = $publication->getVideos();
        if (!empty($videos)) {
            if (!is_string($videos)) {
                $errors[] = "Les vidéos doivent être une chaîne de caractères.";
            } elseif (strlen($videos) > 1000) {
                $errors[] = "Les chemins de vidéos sont trop longs.";
            }
        }
        
        return $errors;
    }

    // Ajouter une publication avec validation
    public function addPublication($publication) {
        $errors = $this->validatePublicationData($publication);
        if (!empty($errors)) {
            throw new Exception(implode(" | ", $errors));
        }
        
        // Nettoyage des données
        $contenuNettoye = $this->sanitizeData($publication->getContenuTexte());
        $imagesNettoyees = $this->sanitizeData($publication->getImages());
        $videosNettoyees = $this->sanitizeData($publication->getVideos());
        
        // Vérifier que l'utilisateur existe
        $sqlCheckUser = "SELECT COUNT(*) FROM utilisateur WHERE idUtilisateur = :idUtilisateur";
        $stmtCheckUser = $this->db->prepare($sqlCheckUser);
        $stmtCheckUser->execute([':idUtilisateur' => $publication->getIdUtilisateur()]);
        
        if ($stmtCheckUser->fetchColumn() == 0) {
            throw new Exception("L'utilisateur spécifié n'existe pas.");
        }
        
        // Préparation de la requête d'insertion
        $sql = "INSERT INTO publication (
            contenuTexte, 
            datePublication, 
            nbLikes, 
            idUtilisateur, 
            images, 
            videos, 
            statut_moderation, 
            raison_moderation, 
            date_moderation, 
            status, 
            created_at
        ) VALUES (
            :contenuTexte, 
            :datePublication, 
            :nbLikes, 
            :idUtilisateur, 
            :images, 
            :videos, 
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
                ':contenuTexte' => $contenuNettoye,
                ':datePublication' => $publication->getDatePublication() ?? $now,
                ':nbLikes' => (int)$publication->getNbLikes() ?? 0,
                ':idUtilisateur' => (int)$publication->getIdUtilisateur(),
                ':images' => $imagesNettoyees,
                ':videos' => $videosNettoyees,
                ':statut_moderation' => $this->sanitizeData($publication->getStatutModeration() ?? 'en_attente'),
                ':raison_moderation' => $this->sanitizeData($publication->getRaisonModeration()),
                ':date_moderation' => $publication->getDateModeration(),
                ':status' => $publication->estApprouve() ? 1 : 0,
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

    // Mettre à jour une publication
    public function updatePublication($idPublication, $publication) {
        // Validation de l'ID
        if (!filter_var($idPublication, FILTER_VALIDATE_INT) || $idPublication <= 0) {
            throw new Exception("ID de publication invalide.");
        }
        
        $errors = $this->validatePublicationData($publication);
        if (!empty($errors)) {
            throw new Exception(implode(" | ", $errors));
        }
        
        // Vérifier que la publication existe
        $sqlCheck = "SELECT COUNT(*) FROM publication WHERE idPublication = :id";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute([':id' => $idPublication]);
        
        if ($stmtCheck->fetchColumn() == 0) {
            throw new Exception("La publication spécifiée n'existe pas.");
        }
        
        // Nettoyage des données
        $contenuNettoye = $this->sanitizeData($publication->getContenuTexte());
        $imagesNettoyees = $this->sanitizeData($publication->getImages());
        $videosNettoyees = $this->sanitizeData($publication->getVideos());
        
        $sql = "UPDATE publication SET 
                    contenuTexte = :contenuTexte,
                    nbLikes = :nbLikes,
                    images = :images,
                    videos = :videos,
                    statut_moderation = :statut_moderation,
                    raison_moderation = :raison_moderation,
                    date_moderation = :date_moderation,
                    status = :status
                WHERE idPublication = :idPublication";
        
        try {
            $stmt = $this->db->prepare($sql);
            
            $params = [
                ':contenuTexte' => $contenuNettoye,
                ':nbLikes' => (int)$publication->getNbLikes(),
                ':images' => $imagesNettoyees,
                ':videos' => $videosNettoyees,
                ':statut_moderation' => $this->sanitizeData($publication->getStatutModeration()),
                ':raison_moderation' => $this->sanitizeData($publication->getRaisonModeration()),
                ':date_moderation' => $publication->getDateModeration(),
                ':status' => $publication->estApprouve() ? 1 : 0,
                ':idPublication' => (int)$idPublication
            ];
            
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la modification: " . $e->getMessage());
        }
    }

    // Supprimer une publication
    public function deletePublication($idPublication) {
        // Validation de l'ID
        if (!filter_var($idPublication, FILTER_VALIDATE_INT) || $idPublication <= 0) {
            throw new Exception("ID de publication invalide.");
        }
        
        // Vérifier que la publication existe
        $sqlCheck = "SELECT COUNT(*) FROM publication WHERE idPublication = :id";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute([':id' => $idPublication]);
        
        if ($stmtCheck->fetchColumn() == 0) {
            throw new Exception("La publication spécifiée n'existe pas.");
        }
        
        // Supprimer d'abord les commentaires associés 
        $sqlDeleteComments = "DELETE FROM commentaire WHERE idPublication = :id";
        $stmtComments = $this->db->prepare($sqlDeleteComments);
        $stmtComments->execute([':id' => $idPublication]);
        
        $sql = "DELETE FROM publication WHERE idPublication = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => (int)$idPublication]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression: " . $e->getMessage());
        }
    }

    // Récupérer une publication par ID
    public function getPublicationById($idPublication) {
        // Validation de l'ID
        if (!filter_var($idPublication, FILTER_VALIDATE_INT) || $idPublication <= 0) {
            throw new Exception("ID de publication invalide.");
        }
        
        $sql = "SELECT * FROM publication WHERE idPublication = :id LIMIT 1";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => (int)$idPublication]);
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) return null;
            
            // Nettoyage des données
            foreach ($row as $key => $value) {
                if (!is_numeric($value)) {
                    $row[$key] = $this->sanitizeData($value);
                }
            }
            
            $pub = new Publication(
                $row['contenuTexte'],
                (int)$row['idUtilisateur'],
                (int)$row['nbLikes'],
                $row['images'],
                $row['videos']
            );
            $pub->setIdPublication((int)$row['idPublication']);
            $pub->setDatePublication($row['datePublication']);
            $pub->setStatutModeration($row['statut_moderation']);
            $pub->setRaisonModeration($row['raison_moderation']);
            $pub->setDateModeration($row['date_moderation']);
            $pub->setStatus((int)$row['status']);
            $pub->setCreatedAt($row['created_at']);
            
            return $pub;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération: " . $e->getMessage());
        }
    }

    // Lister publications
    public function listPublications($onlyApproved = false, $limit = null, $offset = null) {
        // Validation des paramètres de pagination
        if ($limit !== null && (!filter_var($limit, FILTER_VALIDATE_INT) || $limit < 0)) {
            throw new Exception("La limite doit être un nombre positif.");
        }
        
        if ($offset !== null && (!filter_var($offset, FILTER_VALIDATE_INT) || $offset < 0)) {
            throw new Exception("L'offset doit être un nombre positif.");
        }
        
        $sql = "SELECT * FROM publication";
        if ($onlyApproved) {
            $sql .= " WHERE statut_moderation = 'approuve' OR status = 1";
        }
        $sql .= " ORDER BY datePublication DESC";
        
        try {
            if ($limit !== null || $offset !== null) {
                if ($limit !== null) {
                    $sql .= " LIMIT :limit";
                }
                if ($offset !== null) {
                    $sql .= " OFFSET :offset";
                }
                
                $stmt = $this->db->prepare($sql);
                
                if ($limit !== null) {
                    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
                }
                if ($offset !== null) {
                    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
                }
                
                $stmt->execute();
            } else {
                $stmt = $this->db->query($sql);
            }
            
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $result = [];
            
            foreach ($rows as $row) {
                // Nettoyage des données
                foreach ($row as $key => $value) {
                    if (!is_numeric($value)) {
                        $row[$key] = $this->sanitizeData($value);
                    }
                }
                
                $p = new Publication(
                    $row['contenuTexte'],
                    (int)$row['idUtilisateur'],
                    (int)$row['nbLikes'],
                    $row['images'],
                    $row['videos']
                );
                $p->setIdPublication((int)$row['idPublication']);
                $p->setDatePublication($row['datePublication']);
                $p->setStatutModeration($row['statut_moderation']);
                $p->setRaisonModeration($row['raison_moderation']);
                $p->setDateModeration($row['date_moderation']);
                $p->setStatus((int)$row['status']);
                $p->setCreatedAt($row['created_at']);
                
                $result[] = $p;
            }
            
            return $result;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du listing: " . $e->getMessage());
        }
    }

    // Ajouter un like
    public function addLike($idPublication) {
        // Validation de l'ID
        if (!filter_var($idPublication, FILTER_VALIDATE_INT) || $idPublication <= 0) {
            throw new Exception("ID de publication invalide.");
        }
        
        // Vérifier que la publication existe
        $sqlCheck = "SELECT COUNT(*) FROM publication WHERE idPublication = :id";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute([':id' => $idPublication]);
        
        if ($stmtCheck->fetchColumn() == 0) {
            throw new Exception("La publication spécifiée n'existe pas.");
        }
        
        $sql = "UPDATE publication SET nbLikes = nbLikes + 1 WHERE idPublication = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => (int)$idPublication]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de l'ajout du like: " . $e->getMessage());
        }
    }

    // Retirer un like
    public function removeLike($idPublication) {
        // Validation de l'ID
        if (!filter_var($idPublication, FILTER_VALIDATE_INT) || $idPublication <= 0) {
            throw new Exception("ID de publication invalide.");
        }
        
        // Vérifier que la publication existe et a au moins 1 like
        $sqlCheck = "SELECT nbLikes FROM publication WHERE idPublication = :id";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute([':id' => $idPublication]);
        $likes = $stmtCheck->fetchColumn();
        
        if ($likes === false) {
            throw new Exception("La publication spécifiée n'existe pas.");
        }
        
        if ($likes <= 0) {
            return true; // Déjà à 0, pas besoin de mettre à jour
        }
        
        $sql = "UPDATE publication SET nbLikes = nbLikes - 1 WHERE idPublication = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => (int)$idPublication]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du retrait du like: " . $e->getMessage());
        }
    }

    // Modération : approuver
    public function approvePublication($idPublication, $raison = null) {
        // Validation de l'ID
        if (!filter_var($idPublication, FILTER_VALIDATE_INT) || $idPublication <= 0) {
            throw new Exception("ID de publication invalide.");
        }
        
        // Vérifier que la publication existe
        $sqlCheck = "SELECT COUNT(*) FROM publication WHERE idPublication = :id";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute([':id' => $idPublication]);
        
        if ($stmtCheck->fetchColumn() == 0) {
            throw new Exception("La publication spécifiée n'existe pas.");
        }
        
        $raisonNettoyee = $this->sanitizeData($raison);
        $sql = "UPDATE publication 
                SET statut_moderation = 'approuve', 
                    raison_moderation = :raison, 
                    date_moderation = :now, 
                    status = 1 
                WHERE idPublication = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $params = [
                ':raison' => $raisonNettoyee,
                ':now' => date('Y-m-d H:i:s'),
                ':id' => (int)$idPublication
            ];
            
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de l'approbation: " . $e->getMessage());
        }
    }

    // Modération : rejeter
    public function rejectPublication($idPublication, $raison) {
        // Validation de l'ID
        if (!filter_var($idPublication, FILTER_VALIDATE_INT) || $idPublication <= 0) {
            throw new Exception("ID de publication invalide.");
        }
        
        if (empty(trim($raison))) {
            throw new Exception("Une raison de rejet est obligatoire.");
        }
        
        // Vérifier que la publication existe
        $sqlCheck = "SELECT COUNT(*) FROM publication WHERE idPublication = :id";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute([':id' => $idPublication]);
        
        if ($stmtCheck->fetchColumn() == 0) {
            throw new Exception("La publication spécifiée n'existe pas.");
        }
        
        $raisonNettoyee = $this->sanitizeData($raison);
        $sql = "UPDATE publication 
                SET statut_moderation = 'rejete', 
                    raison_moderation = :raison, 
                    date_moderation = :now, 
                    status = 0 
                WHERE idPublication = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $params = [
                ':raison' => $raisonNettoyee,
                ':now' => date('Y-m-d H:i:s'),
                ':id' => (int)$idPublication
            ];
            
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du rejet: " . $e->getMessage());
        }
    }

    // Compter le nombre total de publications
    public function countPublications($onlyApproved = false) {
        $sql = "SELECT COUNT(*) as total FROM publication";
        if ($onlyApproved) {
            $sql .= " WHERE statut_moderation = 'approuve' OR status = 1";
        }
        
        try {
            $stmt = $this->db->query($sql);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? (int)$row['total'] : 0;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du comptage: " . $e->getMessage());
        }
    }

    // Récupérer les publications d'un utilisateur spécifique
    public function getPublicationsByUser($idUtilisateur, $onlyApproved = false, $limit = null) {
        // Validation de l'ID utilisateur
        if (!filter_var($idUtilisateur, FILTER_VALIDATE_INT) || $idUtilisateur <= 0) {
            throw new Exception("ID d'utilisateur invalide.");
        }
        
        // Vérifier que l'utilisateur existe
        $sqlCheckUser = "SELECT COUNT(*) FROM utilisateur WHERE idUtilisateur = :id";
        $stmtCheckUser = $this->db->prepare($sqlCheckUser);
        $stmtCheckUser->execute([':id' => $idUtilisateur]);
        
        if ($stmtCheckUser->fetchColumn() == 0) {
            throw new Exception("L'utilisateur spécifié n'existe pas.");
        }
        
        $sql = "SELECT * FROM publication WHERE idUtilisateur = :idUtilisateur";
        if ($onlyApproved) {
            $sql .= " AND (statut_moderation = 'approuve' OR status = 1)";
        }
        $sql .= " ORDER BY datePublication DESC";
        
        if ($limit !== null && filter_var($limit, FILTER_VALIDATE_INT) && $limit > 0) {
            $sql .= " LIMIT :limit";
        }
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':idUtilisateur', (int)$idUtilisateur, PDO::PARAM_INT);
            
            if ($limit !== null && filter_var($limit, FILTER_VALIDATE_INT) && $limit > 0) {
                $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $result = [];
            foreach ($rows as $row) {
                // Nettoyage des données
                foreach ($row as $key => $value) {
                    if (!is_numeric($value)) {
                        $row[$key] = $this->sanitizeData($value);
                    }
                }
                
                $p = new Publication(
                    $row['contenuTexte'],
                    (int)$row['idUtilisateur'],
                    (int)$row['nbLikes'],
                    $row['images'],
                    $row['videos']
                );
                $p->setIdPublication((int)$row['idPublication']);
                $p->setDatePublication($row['datePublication']);
                $p->setStatutModeration($row['statut_moderation']);
                $p->setRaisonModeration($row['raison_moderation']);
                $p->setDateModeration($row['date_moderation']);
                $p->setStatus((int)$row['status']);
                $p->setCreatedAt($row['created_at']);
                
                $result[] = $p;
            }
            
            return $result;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération: " . $e->getMessage());
        }
    }

    // Vérifier si un utilisateur est propriétaire d'une publication
    public function isPublicationOwner($idPublication, $idUtilisateur) {
        // Validation des IDs
        if (!filter_var($idPublication, FILTER_VALIDATE_INT) || $idPublication <= 0) {
            throw new Exception("ID de publication invalide.");
        }
        
        if (!filter_var($idUtilisateur, FILTER_VALIDATE_INT) || $idUtilisateur <= 0) {
            throw new Exception("ID d'utilisateur invalide.");
        }
        
        $sql = "SELECT COUNT(*) FROM publication 
                WHERE idPublication = :idPublication 
                AND idUtilisateur = :idUtilisateur";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':idPublication' => (int)$idPublication,
                ':idUtilisateur' => (int)$idUtilisateur
            ]);
            
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erreur de vérification: " . $e->getMessage());
        }
    }
}