-- Table pour les suggestions de tâches
CREATE TABLE IF NOT EXISTS suggestiontache (
    id_suggestion INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    type_dosage VARCHAR(100),
    quantite DECIMAL(10,2),
    mode_dosage VARCHAR(100),
    date_dosage DATE,
    derniereExecution DATE,
    prochaineExecution DATE,
    estComplete TINYINT(1) DEFAULT 0,
    priorite VARCHAR(50),
    id_plante INT,
    date_suggestion DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(50) DEFAULT 'En attente',
    date_traitement DATETIME,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(idUtilisateur) ON DELETE CASCADE,
    FOREIGN KEY (id_plante) REFERENCES plante(id_plante) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
