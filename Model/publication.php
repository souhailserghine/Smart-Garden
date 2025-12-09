<?php
// Publication.php - Version complète avec PDO
class Publication {
    private $idPublication;
    private $contenuTexte;
    private $datePublication;
    private $nbLikes;
    private $idUtilisateur;
    private $images;
    private $videos;
    
    private $statutModeration;
    private $raisonModeration;
    private $dateModeration;

    private static $db; // Référence à la connexion PDO

    // MODIFICATION : Initialisation de la connexion PDO
    public static function init($pdoConnection) {
        self::$db = $pdoConnection;
    }

    // MODIFICATION : Ajouter $datePublication en paramètre optionnel
    public function __construct($contenuTexte, $idUtilisateur, $nbLikes = 0, $images = null, $videos = null, $datePublication = null) {
        $this->contenuTexte = $contenuTexte;
        $this->idUtilisateur = $idUtilisateur;
        $this->nbLikes = $nbLikes;
        $this->images = $images;
        $this->videos = $videos;
        
        // MODIFICATION : Utiliser la date fournie ou la date actuelle
        $this->datePublication = $datePublication ? $datePublication : date('Y-m-d H:i:s');
        
        // INITIALISATION DES NOUVELLES PROPRIÉTÉS
        $this->statutModeration = 'en_attente';
        $this->raisonModeration = null;
        $this->dateModeration = null;
    }

    // Getters
    public function getIdPublication() { return $this->idPublication; }
    public function getContenuTexte() { return $this->contenuTexte; }
    public function getDatePublication() { return $this->datePublication; }
    public function getNbLikes() { return $this->nbLikes; }
    public function getIdUtilisateur() { return $this->idUtilisateur; }
    public function getImages() { return $this->images; }
    public function getVideos() { return $this->videos; }
    
    public function getStatutModeration() { return $this->statutModeration; }
    public function getRaisonModeration() { return $this->raisonModeration; }
    public function getDateModeration() { return $this->dateModeration; }

    // Setters
    public function setIdPublication($idPublication) { 
        $this->idPublication = $idPublication; 
        return $this;
    }
    
    public function setContenuTexte($contenuTexte) { 
        $this->contenuTexte = $contenuTexte; 
        return $this;
    }
    
    // MODIFICATION : Ajouter return $this pour le chaînage
    public function setDatePublication($date) { 
        $this->datePublication = $date; 
        return $this;
    }
    
    public function setNbLikes($nbLikes) { 
        $this->nbLikes = $nbLikes; 
        return $this;
    }
    
    public function setIdUtilisateur($idUtilisateur) { 
        $this->idUtilisateur = $idUtilisateur; 
        return $this;
    }
    
    public function setImages($images) { 
        $this->images = $images; 
        return $this;
    }
    
    public function setVideos($videos) { 
        $this->videos = $videos; 
        return $this;
    }
    
    public function setStatutModeration($statutModeration) { 
        $this->statutModeration = $statutModeration; 
        return $this;
    }
    
    public function setRaisonModeration($raisonModeration) { 
        $this->raisonModeration = $raisonModeration; 
        return $this;
    }
    
    public function setDateModeration($dateModeration) { 
        $this->dateModeration = $dateModeration; 
        return $this;
    }

    // MÉTHODES POUR LA MODÉRATION
    public function approuver() {
        $this->statutModeration = 'approuve';
        $this->raisonModeration = null;
        $this->dateModeration = date('Y-m-d H:i:s');
        return $this;
    }

    public function rejeter($raison) {
        $this->statutModeration = 'rejete';
        $this->raisonModeration = $raison;
        $this->dateModeration = date('Y-m-d H:i:s');
        return $this;
    }

    public function estEnAttente() {
        return $this->statutModeration === 'en_attente';
    }

    public function estApprouve() {
        return $this->statutModeration === 'approuve';
    }

    public function estRejete() {
        return $this->statutModeration === 'rejete';
    }
    
    // MÉTHODE UTILE POUR LA PLANIFICATION
    public function estPublicationFuture() {
        $datePub = new DateTime($this->datePublication);
        $dateNow = new DateTime();
        return $datePub > $dateNow;
    }
    
    public function getDateFormatee() {
        return date('d/m/Y H:i', strtotime($this->datePublication));
    }

    // ============================================================
    // MODIFICATIONS AJOUTÉES : MÉTHODES PDO POUR LA BASE DE DONNÉES
    // ============================================================

    /**
     * Sauvegarder la publication dans la base de données
     * @return bool Succès de l'opération
     */
    public function save() {
        if (!self::$db) {
            throw new Exception("Connexion PDO non initialisée. Utilisez Publication::init()");
        }

        try {
            if ($this->idPublication) {
                // Mise à jour
                $sql = "UPDATE publications SET 
                        contenuTexte = :contenu,
                        datePublication = :datePub,
                        nbLikes = :likes,
                        idUtilisateur = :idUser,
                        images = :images,
                        videos = :videos,
                        statutModeration = :statut,
                        raisonModeration = :raison,
                        dateModeration = :dateMod
                        WHERE idPublication = :id";
                
                $stmt = self::$db->prepare($sql);
                $stmt->execute([
                    ':contenu' => $this->contenuTexte,
                    ':datePub' => $this->datePublication,
                    ':likes' => $this->nbLikes,
                    ':idUser' => $this->idUtilisateur,
                    ':images' => $this->images,
                    ':videos' => $this->videos,
                    ':statut' => $this->statutModeration,
                    ':raison' => $this->raisonModeration,
                    ':dateMod' => $this->dateModeration,
                    ':id' => $this->idPublication
                ]);
                
                return $stmt->rowCount() > 0;
            } else {
                // Insertion
                $sql = "INSERT INTO publications 
                        (contenuTexte, datePublication, nbLikes, idUtilisateur, images, videos, statutModeration, raisonModeration, dateModeration) 
                        VALUES (:contenu, :datePub, :likes, :idUser, :images, :videos, :statut, :raison, :dateMod)";
                
                $stmt = self::$db->prepare($sql);
                $stmt->execute([
                    ':contenu' => $this->contenuTexte,
                    ':datePub' => $this->datePublication,
                    ':likes' => $this->nbLikes,
                    ':idUser' => $this->idUtilisateur,
                    ':images' => $this->images,
                    ':videos' => $this->videos,
                    ':statut' => $this->statutModeration,
                    ':raison' => $this->raisonModeration,
                    ':dateMod' => $this->dateModeration
                ]);
                
                $this->idPublication = self::$db->lastInsertId();
                return true;
            }
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la sauvegarde : " . $e->getMessage());
        }
    }

    /**
     * Charger une publication depuis la base de données par son ID
     * @param int $id ID de la publication
     * @return Publication|null
     */
    public static function findById($id) {
        if (!self::$db) {
            throw new Exception("Connexion PDO non initialisée. Utilisez Publication::init()");
        }

        try {
            $sql = "SELECT * FROM publications WHERE idPublication = :id";
            $stmt = self::$db->prepare($sql);
            $stmt->execute([':id' => $id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$data) {
                return null;
            }
            
            $publication = new Publication(
                $data['contenuTexte'],
                $data['idUtilisateur'],
                $data['nbLikes'],
                $data['images'],
                $data['videos'],
                $data['datePublication']
            );
            
            $publication->idPublication = $data['idPublication'];
            $publication->statutModeration = $data['statutModeration'];
            $publication->raisonModeration = $data['raisonModeration'];
            $publication->dateModeration = $data['dateModeration'];
            
            return $publication;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération : " . $e->getMessage());
        }
    }

    /**
     * Récupérer toutes les publications
     * @param string $statut Filtre par statut (optionnel)
     * @return array Liste des publications
     */
    public static function findAll($statut = null) {
        if (!self::$db) {
            throw new Exception("Connexion PDO non initialisée. Utilisez Publication::init()");
        }

        try {
            $sql = "SELECT * FROM publications";
            $params = [];
            
            if ($statut) {
                $sql .= " WHERE statutModeration = :statut";
                $params[':statut'] = $statut;
            }
            
            $sql .= " ORDER BY datePublication DESC";
            
            $stmt = self::$db->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $publications = [];
            foreach ($results as $data) {
                $publication = new Publication(
                    $data['contenuTexte'],
                    $data['idUtilisateur'],
                    $data['nbLikes'],
                    $data['images'],
                    $data['videos'],
                    $data['datePublication']
                );
                
                $publication->idPublication = $data['idPublication'];
                $publication->statutModeration = $data['statutModeration'];
                $publication->raisonModeration = $data['raisonModeration'];
                $publication->dateModeration = $data['dateModeration'];
                
                $publications[] = $publication;
            }
            
            return $publications;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération : " . $e->getMessage());
        }
    }

    /**
     * Supprimer la publication de la base de données
     * @return bool Succès de l'opération
     */
    public function delete() {
        if (!$this->idPublication) {
            throw new Exception("Impossible de supprimer une publication sans ID");
        }

        if (!self::$db) {
            throw new Exception("Connexion PDO non initialisée. Utilisez Publication::init()");
        }

        try {
            $sql = "DELETE FROM publications WHERE idPublication = :id";
            $stmt = self::$db->prepare($sql);
            $stmt->execute([':id' => $this->idPublication]);
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression : " . $e->getMessage());
        }
    }

    /**
     * Mettre à jour uniquement le statut de modération
     * @param string $statut Nouveau statut
     * @param string|null $raison Raison du rejet (optionnel)
     * @return bool Succès de l'opération
     */
    public function updateStatutModeration($statut, $raison = null) {
        if (!self::$db) {
            throw new Exception("Connexion PDO non initialisée. Utilisez Publication::init()");
        }

        try {
            $this->statutModeration = $statut;
            $this->raisonModeration = $raison;
            $this->dateModeration = date('Y-m-d H:i:s');
            
            $sql = "UPDATE publications SET 
                    statutModeration = :statut,
                    raisonModeration = :raison,
                    dateModeration = :dateMod
                    WHERE idPublication = :id";
            
            $stmt = self::$db->prepare($sql);
            $stmt->execute([
                ':statut' => $this->statutModeration,
                ':raison' => $this->raisonModeration,
                ':dateMod' => $this->dateModeration,
                ':id' => $this->idPublication
            ]);
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise à jour : " . $e->getMessage());
        }
    }

    /**
     * Incrémenter le nombre de likes
     * @return bool Succès de l'opération
     */
    public function incrementLikes() {
        $this->nbLikes++;
        
        if (!self::$db) {
            throw new Exception("Connexion PDO non initialisée. Utilisez Publication::init()");
        }

        try {
            $sql = "UPDATE publications SET nbLikes = :likes WHERE idPublication = :id";
            $stmt = self::$db->prepare($sql);
            $stmt->execute([
                ':likes' => $this->nbLikes,
                ':id' => $this->idPublication
            ]);
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise à jour des likes : " . $e->getMessage());
        }
    }

    /**
     * Rechercher des publications par contenu
     * @param string $keyword Mot-clé de recherche
     * @return array Publications trouvées
     */
    public static function search($keyword) {
        if (!self::$db) {
            throw new Exception("Connexion PDO non initialisée. Utilisez Publication::init()");
        }

        try {
            $sql = "SELECT * FROM publications 
                    WHERE contenuTexte LIKE :keyword 
                    AND statutModeration = 'approuve'
                    ORDER BY datePublication DESC";
            
            $stmt = self::$db->prepare($sql);
            $stmt->execute([':keyword' => "%$keyword%"]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $publications = [];
            foreach ($results as $data) {
                $publication = new Publication(
                    $data['contenuTexte'],
                    $data['idUtilisateur'],
                    $data['nbLikes'],
                    $data['images'],
                    $data['videos'],
                    $data['datePublication']
                );
                
                $publication->idPublication = $data['idPublication'];
                $publication->statutModeration = $data['statutModeration'];
                $publication->raisonModeration = $data['raisonModeration'];
                $publication->dateModeration = $data['dateModeration'];
                
                $publications[] = $publication;
            }
            
            return $publications;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la recherche : " . $e->getMessage());
        }
    }

    /**
     * Compter le nombre de publications par statut
     * @param string $statut Statut à compter
     * @return int Nombre de publications
     */
    public static function countByStatus($statut = null) {
        if (!self::$db) {
            throw new Exception("Connexion PDO non initialisée. Utilisez Publication::init()");
        }

        try {
            if ($statut) {
                $sql = "SELECT COUNT(*) as count FROM publications WHERE statutModeration = :statut";
                $stmt = self::$db->prepare($sql);
                $stmt->execute([':statut' => $statut]);
            } else {
                $sql = "SELECT COUNT(*) as count FROM publications";
                $stmt = self::$db->prepare($sql);
                $stmt->execute();
            }
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['count'];
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du comptage : " . $e->getMessage());
        }
    }
}
?>