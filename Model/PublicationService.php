<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/publication.php';

class PublicationService {
    private $db;

    public function __construct() {
        $this->db = config::getConnexion();
    }

    // Validation simple métier
    public function validatePublicationData($publication) {
        $errors = [];
        $contenu = trim($publication->getContenuTexte() ?? '');
        if ($contenu === '') $errors[] = "Le contenu ne peut pas être vide.";
        if (mb_strlen($contenu) > 5000) $errors[] = "Le contenu est trop long (max 5000 caractères).";
        // ajouter d'autres règles si besoin
        return $errors;
    }

    // Ajouter -> retourne id inséré ou false
    public function addPublication($publication) {
        $errors = $this->validatePublicationData($publication);
        if (!empty($errors)) throw new Exception(implode(" | ", $errors));

        $sql = "INSERT INTO publication 
            (contenuTexte, datePublication, nbLikes, idUtilisateur, images, videos, statut_moderation, raison_moderation, date_moderation, status, created_at)
            VALUES (:contenuTexte, :datePublication, :nbLikes, :idUtilisateur, :images, :videos, :statut_moderation, :raison_moderation, :date_moderation, :status, :created_at)";

        $now = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([
            ':contenuTexte' => $publication->getContenuTexte(),
            ':datePublication' => $publication->getDatePublication() ?? $now,
            ':nbLikes' => $publication->getNbLikes() ?? 0,
            ':idUtilisateur' => $publication->getIdUtilisateur(),
            ':images' => $publication->getImages(),
            ':videos' => $publication->getVideos(),
            ':statut_moderation' => $publication->getStatutModeration() ?? 'en_attente',
            ':raison_moderation' => $publication->getRaisonModeration(),
            ':date_moderation' => $publication->getDateModeration(),
            ':status' => $publication->estApprouve() ? 1 : 0,
            ':created_at' => $now
        ]);

        if ($ok) return $this->db->lastInsertId();
        return false;
    }

    // Mettre à jour (modification du contenu / images / videos / statut moderation)
    public function updatePublication($idPublication, $publication) {
        $errors = $this->validatePublicationData($publication);
        if (!empty($errors)) throw new Exception(implode(" | ", $errors));

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

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':contenuTexte' => $publication->getContenuTexte(),
            ':nbLikes' => $publication->getNbLikes(),
            ':images' => $publication->getImages(),
            ':videos' => $publication->getVideos(),
            ':statut_moderation' => $publication->getStatutModeration(),
            ':raison_moderation' => $publication->getRaisonModeration(),
            ':date_moderation' => $publication->getDateModeration(),
            ':status' => $publication->estApprouve() ? 1 : 0,
            ':idPublication' => $idPublication
        ]);
    }

    // Supprimer
    public function deletePublication($idPublication) {
        $sql = "DELETE FROM publication WHERE idPublication = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $idPublication]);
    }

    // Récupérer une publication (objet Publication)
    public function getPublicationById($idPublication) {
        $sql = "SELECT * FROM publication WHERE idPublication = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $idPublication]);
        $row = $stmt->fetch();
        if (!$row) return null;

        $pub = new Publication(
            $row['contenuTexte'],
            $row['idUtilisateur'],
            $row['nbLikes'],
            $row['images'],
            $row['videos']
        );
        $pub->setIdPublication($row['idPublication']);
        $pub->setStatutModeration($row['statut_moderation']);
        $pub->setRaisonModeration($row['raison_moderation']);
        $pub->setDateModeration($row['date_moderation']);
        // si created_at/datePublication différent, on peut setter datePublication si setter existe
        return $pub;
    }

    // Lister publications (option: uniquement approuvées)
    public function listPublications($onlyApproved = false, $limit = null, $offset = null) {
        $sql = "SELECT * FROM publication";
        if ($onlyApproved) $sql .= " WHERE statut_moderation = 'approuve' OR status = 1";
        $sql .= " ORDER BY datePublication DESC";
        if (is_numeric($limit)) $sql .= " LIMIT " . intval($limit);
        if (is_numeric($offset)) $sql .= " OFFSET " . intval($offset);

        $stmt = $this->db->query($sql);
        $rows = $stmt->fetchAll();

        $result = [];
        foreach ($rows as $row) {
            $p = new Publication(
                $row['contenuTexte'],
                $row['idUtilisateur'],
                $row['nbLikes'],
                $row['images'],
                $row['videos']
            );
            $p->setIdPublication($row['idPublication']);
            $p->setStatutModeration($row['statut_moderation']);
            $p->setRaisonModeration($row['raison_moderation']);
            $p->setDateModeration($row['date_moderation']);
            $result[] = $p;
        }
        return $result;
    }

    // Like simple (incrémente)
    public function addLike($idPublication) {
        $sql = "UPDATE publication SET nbLikes = nbLikes + 1 WHERE idPublication = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $idPublication]);
    }

    // Modération : approuver
    public function approvePublication($idPublication, $raison = null) {
        $sql = "UPDATE publication SET statut_moderation = 'approuve', raison_moderation = :raison, date_moderation = :now, status = 1 WHERE idPublication = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':raison' => $raison, ':now' => date('Y-m-d H:i:s'), ':id' => $idPublication]);
    }

    // Modération : rejeter
    public function rejectPublication($idPublication, $raison) {
        $sql = "UPDATE publication SET statut_moderation = 'rejete', raison_moderation = :raison, date_moderation = :now, status = 0 WHERE idPublication = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':raison' => $raison, ':now' => date('Y-m-d H:i:s'), ':id' => $idPublication]);
    }
}
