<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/Commentaire.php';

class CommentaireService {
    private $db;

    public function __construct() {
        $this->db = config::getConnexion();
    }

    // Validation simple
    public function validateCommentaireData($commentaire) {
        $errors = [];
        $contenu = trim($commentaire->getContenuCommentaire() ?? '');
        if ($contenu === '') $errors[] = "Le commentaire ne peut pas être vide.";
        if (mb_strlen($contenu) > 500) $errors[] = "Le commentaire est trop long (max 500 caractères).";
        if (!is_numeric($commentaire->getIdPublication()) || $commentaire->getIdPublication() <= 0) $errors[] = "ID publication invalide.";
        if (!is_numeric($commentaire->getIdUtilisateur()) || $commentaire->getIdUtilisateur() <= 0) $errors[] = "ID utilisateur invalide.";
        return $errors;
    }

    // Ajouter -> retourne id inséré
    public function addCommentaire($commentaire) {
        $errors = $this->validateCommentaireData($commentaire);
        if (!empty($errors)) throw new Exception(implode(" | ", $errors));

        $sql = "INSERT INTO commentaire 
            (contenuCommentaire, dateCommentaire, idPublication, idUtilisateur, statut_moderation, raison_moderation, date_moderation, status, created_at)
            VALUES (:contenuCommentaire, :dateCommentaire, :idPublication, :idUtilisateur, :statut_moderation, :raison_moderation, :date_moderation, :status, :created_at)";
        $now = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([
            ':contenuCommentaire' => $commentaire->getContenuCommentaire(),
            ':dateCommentaire' => $commentaire->getDateCommentaire() ?? $now,
            ':idPublication' => $commentaire->getIdPublication(),
            ':idUtilisateur' => $commentaire->getIdUtilisateur(),
            ':statut_moderation' => $commentaire->getStatutModeration() ?? 'en_attente',
            ':raison_moderation' => $commentaire->getRaisonModeration(),
            ':date_moderation' => $commentaire->getDateModeration(),
            ':status' => $commentaire->estApprouve() ? 1 : 0,
            ':created_at' => $now
        ]);

        if ($ok) return $this->db->lastInsertId();
        return false;
    }

    // Récupérer commentaires par publication (option: uniquement approuvés)
    public function getCommentairesByPublication($idPublication, $onlyApproved = false) {
        $sql = "SELECT c.*, u.nom, u.prenom FROM commentaire c
                LEFT JOIN utilisateur u ON c.idUtilisateur = u.idUtilisateur
                WHERE c.idPublication = :idPublication";
        if ($onlyApproved) $sql .= " AND (c.statut_moderation = 'approuve' OR c.status = 1)";
        $sql .= " ORDER BY c.dateCommentaire ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':idPublication' => $idPublication]);
        $rows = $stmt->fetchAll();

        $result = [];
        foreach ($rows as $row) {
            // Le constructeur de Commentaire dans ton projet attend : (idCommentaire, contenuCommentaire, dateCommentaire, idPublication, idUtilisateur, nom, prenom)
            $c = new Commentaire(
                $row['idCommentaire'],
                $row['contenuCommentaire'],
                $row['dateCommentaire'],
                $row['idPublication'],
                $row['idUtilisateur'],
                $row['nom'] ?? null,
                $row['prenom'] ?? null
            );
            // si tu veux exposer modération via getters, on les a déjà dans la classe
            // mais si tu veux setter (aucun setter pour id dans ta class), on l'a mis via constructeur
            $result[] = $c;
        }
        return $result;
    }

    // Modifier commentaire
    public function updateCommentaire($idCommentaire, $nouveauContenu) {
        // validation basique
        $contenu = trim($nouveauContenu);
        if ($contenu === '') throw new Exception("Le commentaire ne peut pas être vide.");
        if (mb_strlen($contenu) > 500) throw new Exception("Le commentaire est trop long (500).");

        $sql = "UPDATE commentaire SET contenuCommentaire = :contenu WHERE idCommentaire = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':contenu' => $contenu, ':id' => $idCommentaire]);
    }

    // Supprimer
    public function deleteCommentaire($idCommentaire) {
        $sql = "DELETE FROM commentaire WHERE idCommentaire = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $idCommentaire]);
    }

    // Modération : approuver
    public function approveCommentaire($idCommentaire, $raison = null) {
        $sql = "UPDATE commentaire SET statut_moderation = 'approuve', raison_moderation = :raison, date_moderation = :now, status = 1 WHERE idCommentaire = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':raison' => $raison, ':now' => date('Y-m-d H:i:s'), ':id' => $idCommentaire]);
    }

    // Modération : rejeter
    public function rejectCommentaire($idCommentaire, $raison) {
        $sql = "UPDATE commentaire SET statut_moderation = 'rejete', raison_moderation = :raison, date_moderation = :now, status = 0 WHERE idCommentaire = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':raison' => $raison, ':now' => date('Y-m-d H:i:s'), ':id' => $idCommentaire]);
    }

    // Compter commentaires d'une publication
    public function countCommentairesByPublication($idPublication, $onlyApproved = false) {
        $sql = "SELECT COUNT(*) as nb FROM commentaire WHERE idPublication = :idPublication";
        if ($onlyApproved) $sql .= " AND (statut_moderation = 'approuve' OR status = 1)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':idPublication' => $idPublication]);
        $row = $stmt->fetch();
        return $row ? intval($row['nb']) : 0;
    }
}
