<?php
class ModerationC {
    private $publicationModel;
    private $commentaireModel;
    
    public function __construct($db) {
        $this->publicationModel = new Publication($db);
        $this->commentaireModel = new Commentaire($db);
    }
    
    // Afficher le dashboard de modération
    public function showDashboard() {
        $publicationsEnAttente = $this->publicationModel->getPublicationsEnAttente();
        $commentairesEnAttente = $this->commentaireModel->getCommentairesEnAttente();
        
        return [
            'publications_attente' => $publicationsEnAttente,
            'commentaires_attente' => $commentairesEnAttente,
            'total_attente' => count($publicationsEnAttente) + count($commentairesEnAttente)
        ];
    }
    
    // Lister toutes les publications
    public function listPublications() {
        return $this->publicationModel->getToutesPublications();
    }
    
    // Lister tous les commentaires
    public function listCommentaires() {
        return $this->commentaireModel->getTousCommentaires();
    }
    
    // Modérer une publication
    public function modererPublication($id, $action, $raison = null) {
        return $this->publicationModel->modererPublication($id, $action, $raison);
    }
    
    // Modérer un commentaire
    public function modererCommentaire($id, $action, $raison = null) {
        return $this->commentaireModel->modererCommentaire($id, $action, $raison);
    }
    
    // Obtenir les détails d'une publication
    public function getDetailPublication($id) {
        return $this->publicationModel->getPublicationById($id);
    }
    
    // Obtenir les détails d'un commentaire
    public function getDetailCommentaire($id) {
        return $this->commentaireModel->getCommentaireById($id);
    }
    
    // Supprimer une publication (contenu inapproprié)
    public function supprimerPublication($id) {
        // Implémentez votre logique de suppression ici
        $sql = "DELETE FROM publications WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    // Supprimer un commentaire (contenu inapproprié)
    public function supprimerCommentaire($id) {
        // Implémentez votre logique de suppression ici
        $sql = "DELETE FROM commentaires WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>