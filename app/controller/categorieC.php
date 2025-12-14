<?php

require_once __DIR__ . '/../config.php';


class CategorieC {

    // Ajouter une catégorie
    public function addCategorie($categorie) {
        $db = config::getConnexion();

        try {
            $req = $db->prepare('
                INSERT INTO categorie (nom_categorie) 
                VALUES (:nom)
            ');

            return $req->execute([
                'nom' => $categorie->getNomCategorie()
            ]);

        } catch (Exception $e) {
            error_log('Erreur addCategorie: ' . $e->getMessage());
            throw $e;
        }
    }

    // Afficher toutes les catégories
    public function showCategorie() {
        $db = config::getConnexion();

        try {
            $liste = $db->query('SELECT * FROM categorie ORDER BY id_categorie');
            return $liste->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log('Erreur showCategorie: ' . $e->getMessage());
            throw $e;
        }
    }

    // Afficher catégories avec nombre de capteurs (JOIN)
    public function showCategorieWithDetails() {
        $db = config::getConnexion();

        try {
            $query = '
                SELECT cat.*, 
                       COUNT(c.id_capteur) AS nombre_capteurs
                FROM categorie cat
                LEFT JOIN capteur c ON cat.id_categorie = c.id_categorie
                GROUP BY cat.id_categorie
                ORDER BY cat.id_categorie
            ';

            $liste = $db->query($query);
            return $liste->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log('Erreur showCategorieWithDetails: ' . $e->getMessage());
            throw $e;
        }
    }

    // Rechercher des catégories par terme de recherche
    public function searchCategorie($searchTerm) {
        $db = config::getConnexion();

        try {
            $query = '
                SELECT cat.*, 
                       COUNT(c.id_capteur) AS nombre_capteurs
                FROM categorie cat
                LEFT JOIN capteur c ON cat.id_categorie = c.id_categorie
                WHERE cat.nom_categorie LIKE :searchTerm 
                   OR cat.id_categorie LIKE :searchTerm
                GROUP BY cat.id_categorie
                ORDER BY cat.id_categorie
            ';

            $req = $db->prepare($query);
            $req->execute(['searchTerm' => '%' . $searchTerm . '%']);
            return $req->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log('Erreur searchCategorie: ' . $e->getMessage());
            throw $e;
        }
    }

    // Filtrer et rechercher des catégories avec options avancées
    public function filterCategories($searchTerm = '', $filterCapteurs = 'tous', $orderBy = 'id', $orderDir = 'ASC') {
        $db = config::getConnexion();

        try {
            $query = '
                SELECT cat.*, 
                       COUNT(c.id_capteur) AS nombre_capteurs
                FROM categorie cat
                LEFT JOIN capteur c ON cat.id_categorie = c.id_categorie
            ';

            $conditions = [];
            $params = [];

            // Filtre de recherche
            if (!empty($searchTerm)) {
                $conditions[] = '(cat.nom_categorie LIKE :searchTerm OR cat.id_categorie LIKE :searchTerm)';
                $params['searchTerm'] = '%' . $searchTerm . '%';
            }

            // Ajout des conditions WHERE
            if (!empty($conditions)) {
                $query .= ' WHERE ' . implode(' AND ', $conditions);
            }

            $query .= ' GROUP BY cat.id_categorie';

            // Filtre par nombre de capteurs (après GROUP BY)
            if ($filterCapteurs === 'avec') {
                $query .= ' HAVING COUNT(c.id_capteur) > 0';
            } elseif ($filterCapteurs === 'sans') {
                $query .= ' HAVING COUNT(c.id_capteur) = 0';
            }

            // Ordre de tri
            $validOrders = ['id' => 'cat.id_categorie', 'nom' => 'cat.nom_categorie', 'capteurs' => 'nombre_capteurs'];
            $orderColumn = $validOrders[$orderBy] ?? 'cat.id_categorie';
            $orderDirection = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
            $query .= " ORDER BY $orderColumn $orderDirection";

            $req = $db->prepare($query);
            $req->execute($params);
            return $req->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log('Erreur filterCategories: ' . $e->getMessage());
            throw $e;
        }
    }

    // Supprimer une catégorie
    public function deleteCategorie($id) {
        $db = config::getConnexion();

        try {
            $req = $db->prepare('DELETE FROM categorie WHERE id_categorie = :id');
            $req->execute(['id' => $id]);

            return $req->rowCount();

        } catch (Exception $e) {
            error_log('Erreur deleteCategorie: ' . $e->getMessage());
            throw $e;
        }
    }

    // Récupérer une catégorie par ID
    public function getCategorie($id) {
        $db = config::getConnexion();

        try {
            $req = $db->prepare('SELECT * FROM categorie WHERE id_categorie = :id');
            $req->execute(['id' => $id]);

            return $req->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log('Erreur getCategorie: ' . $e->getMessage());
            throw $e;
        }
    }

    // Modifier une catégorie
    public function updateCategorie($categorie, $id) {
        $db = config::getConnexion();

        try {
            $req = $db->prepare('
                UPDATE categorie 
                SET nom_categorie = :nom
                WHERE id_categorie = :id
            ');

            $req->execute([
                'id' => $id,
                'nom' => $categorie->getNomCategorie()
            ]);

            return $req->rowCount();

        } catch (Exception $e) {
            error_log('Erreur updateCategorie: ' . $e->getMessage());
            throw $e;
        }
    }
}

?>