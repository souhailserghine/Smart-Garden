<?php
include '../config.php';
include 'Publication.php';  // Ajout de l'inclusion

class PublicationC {
    
    public function addPublication($publication){
        $db = config::getConnexion();
        try{
            $req = $db->prepare('
                INSERT INTO publication (titre, contenu, auteur_id, date_publication) 
                VALUES (:titre, :contenu, :auteur_id, NOW())
            ');
            
            $req->execute([
                'titre' => $publication->getTitre(),
                'contenu' => $publication->getContenu(),
                'auteur_id' => $publication->getAuteurId()
            ]);
            return $db->lastInsertId();
            
        } catch(Exception $e){
            error_log("Erreur: " . $e->getMessage());
            return false;
        }
    }
    
    public function deletePublication($id){
        $db = config::getConnexion();
        try {
            $req = $db->prepare('DELETE FROM publication WHERE id = ?');
            return $req->execute([$id]);
        } catch(Exception $e) {
            error_log("Erreur: " . $e->getMessage());
            return false;
        }
    }
    
    public function updatePublication($id, $publication){
        $db = config::getConnexion();
        try {
            $req = $db->prepare('
                UPDATE publication 
                SET titre = ?, contenu = ? 
                WHERE id = ?
            ');
            return $req->execute([
                $publication->getTitre(),
                $publication->getContenu(),
                $id
            ]);
        } catch(Exception $e) {
            error_log("Erreur: " . $e->getMessage());
            return false;
        }
    }
    
    public function listePublications() {
        $db = config::getConnexion();
        $req = $db->query("SELECT * FROM publication ORDER BY date_publication DESC");
        return $req->fetchAll();
    }
    
    public function getPublication($id) {
        $db = config::getConnexion();
        $req = $db->prepare("SELECT * FROM publication WHERE id = ?");
        $req->execute([$id]);
        $data = $req->fetch();
        
        if ($data) {
            $publication = new Publication(
                $data['titre'],
                $data['contenu'], 
                $data['auteur_id']
            );
            $publication->setId($data['id']);
            return $publication;
        }
        return null;
    }
}
?>