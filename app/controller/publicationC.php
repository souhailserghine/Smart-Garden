<?php
include  ("../../config.php");
include  '../../model/publication.php';


class PublicationC {
    // OLD METHOD - Keep for backward compatibility but should migrate to new system
    public function ajouterLike($idPublication) {
        $sql = "UPDATE publication SET nbLikes = nbLikes + 1 WHERE idPublication = :idPublication";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':idPublication', $idPublication);
        return $req->execute();
    }
    
    // NEW METHODS - User-specific like tracking
    
    /**
     * Check if a user has already liked a publication
     */
    public function aUtilisateurAime($idPublication, $idUtilisateur) {
        $db = config::getConnexion();
        $sql = "SELECT COUNT(*) as count FROM publication_likes 
                WHERE idPublication = :idPublication AND idUtilisateur = :idUtilisateur";
        $req = $db->prepare($sql);
        $req->bindValue(':idPublication', $idPublication, PDO::PARAM_INT);
        $req->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch();
        return $result['count'] > 0;
    }
    
    /**
     * Add a like from a specific user
     * Returns true if like was added, false if user already liked
     */
    public function ajouterLikeUtilisateur($idPublication, $idUtilisateur) {
        $db = config::getConnexion();
        
        // Check if user already liked this publication
        if ($this->aUtilisateurAime($idPublication, $idUtilisateur)) {
            return false; // User already liked
        }
        
        try {
            // Start transaction
            $db->beginTransaction();
            
            // Add like to tracking table
            $sql = "INSERT INTO publication_likes (idPublication, idUtilisateur, dateLike) 
                    VALUES (:idPublication, :idUtilisateur, NOW())";
            $req = $db->prepare($sql);
            $req->bindValue(':idPublication', $idPublication, PDO::PARAM_INT);
            $req->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
            $req->execute();
            
            // Update like count in publication table
            $sql = "UPDATE publication SET nbLikes = nbLikes + 1 WHERE idPublication = :idPublication";
            $req = $db->prepare($sql);
            $req->bindValue(':idPublication', $idPublication, PDO::PARAM_INT);
            $req->execute();
            
            // Commit transaction
            $db->commit();
            return true;
            
        } catch (Exception $e) {
            // Rollback on error
            $db->rollBack();
            return false;
        }
    }
    
    /**
     * Remove a like from a specific user
     */
    public function retirerLikeUtilisateur($idPublication, $idUtilisateur) {
        $db = config::getConnexion();
        
        // Check if user has liked this publication
        if (!$this->aUtilisateurAime($idPublication, $idUtilisateur)) {
            return false; // User hasn't liked
        }
        
        try {
            // Start transaction
            $db->beginTransaction();
            
            // Remove like from tracking table
            $sql = "DELETE FROM publication_likes 
                    WHERE idPublication = :idPublication AND idUtilisateur = :idUtilisateur";
            $req = $db->prepare($sql);
            $req->bindValue(':idPublication', $idPublication, PDO::PARAM_INT);
            $req->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
            $req->execute();
            
            // Update like count in publication table
            $sql = "UPDATE publication SET nbLikes = GREATEST(nbLikes - 1, 0) WHERE idPublication = :idPublication";
            $req = $db->prepare($sql);
            $req->bindValue(':idPublication', $idPublication, PDO::PARAM_INT);
            $req->execute();
            
            // Commit transaction
            $db->commit();
            return true;
            
        } catch (Exception $e) {
            // Rollback on error
            $db->rollBack();
            return false;
        }
    }
    
    /**
     * Get all publications with user-specific like status
     */
    public function getPublicationsAvecStatutLike($idUtilisateur) {
        $db = config::getConnexion();
        $sql = "SELECT p.*, 
                       u.nom,
                       EXISTS(
                           SELECT 1 FROM publication_likes pl 
                           WHERE pl.idPublication = p.idPublication 
                           AND pl.idUtilisateur = :idUtilisateur
                       ) as userHasLiked
                FROM publication p
                LEFT JOIN utilisateur u ON p.idUtilisateur = u.idUtilisateur
                ORDER BY p.datePublication DESC";
        
        $req = $db->prepare($sql);
        $req->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $req->execute();
        
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }


    public function addPublication($publication) {
    try {
        $db = config::getConnexion();
        
        // MODIFIER LA REQUÊTE POUR INCLURE datePublication
        $sql = "INSERT INTO publication (contenuTexte, datePublication, idUtilisateur, nbLikes, images, videos) 
                VALUES (:contenuTexte, :datePublication, :idUtilisateur, :nbLikes, :images, :videos)";
        
        $query = $db->prepare($sql);
        
        // MODIFIER L'EXECUTION POUR INCLURE LA DATE
        $query->execute([
            'contenuTexte' => $publication->getContenuTexte(),
            'datePublication' => $publication->getDatePublication(), // AJOUTER CETTE LIGNE
            'idUtilisateur' => $publication->getIdUtilisateur(),
            'nbLikes' => $publication->getNbLikes(),
            'images' => $publication->getImages(),
            'videos' => $publication->getVideos()
        ]);
        
        return true;
    } catch (Exception $e) {
        echo 'Erreur: ' . $e->getMessage();
        return false;
    }
}

    public function deletePublication($id){
        $db = config::getConnexion();
        try {
            $req = $db->prepare("DELETE FROM publication WHERE idPublication = ?");
            return $req->execute([$id]);
        } catch(Exception $e){
            error_log("Erreur: " . $e->getMessage());
            return false;
        }
    }

    public function updatePublication($id, $publication){
        $db = config::getConnexion();
        try {
            $req = $db->prepare("
                UPDATE publication 
                SET contenuTexte = ?, nbLikes = ?
                WHERE idPublication = ?
            ");

            return $req->execute([
                $publication->getContenuTexte(),
                $publication->getNbLikes(),
                $id
            ]);

        } catch(Exception $e){
            error_log("Erreur: " . $e->getMessage());
            return false;
        }
    }

    public function listePublications(){
        $db = config::getConnexion();
        // JOIN avec la table utilisateur pour récupérer le nom
        $req = $db->query("
            SELECT p.*, u.nom, u.email 
            FROM publication p 
            LEFT JOIN utilisateur u ON p.idUtilisateur = u.idUtilisateur 
            ORDER BY p.datePublication DESC
        ");
        return $req->fetchAll();
    }

    public function listePublicationsTrieesParLikes() {
    try {
        $sql = "SELECT * FROM publication ORDER BY nbLikes DESC, datePublication DESC";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    } catch (PDOException $e) {
        die('Erreur: ' . $e->getMessage());
    }
    }

    public function getPublication($id){
        $db = config::getConnexion();
        $req = $db->prepare("SELECT * FROM publication WHERE idPublication = ?");
        $req->execute([$id]);
        $data = $req->fetch();

        if ($data) {
            $publication = new Publication(
                $data['contenuTexte'],
                $data['idUtilisateur'],
                $data['nbLikes']
            );

            $publication->setIdPublication($data['idPublication']);
            return $publication;
        }
        return null;
    }
}
?>