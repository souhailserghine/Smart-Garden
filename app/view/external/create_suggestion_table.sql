-- Table pour les suggestions de plantes
CREATE TABLE IF NOT EXISTS suggestionplante (
    id_suggestion INT PRIMARY KEY AUTO_INCREMENT,
    id_utilisateur INT NOT NULL,
    nom_plante VARCHAR(255) NOT NULL,
    type_plante VARCHAR(100),
    description LONGTEXT,
    image VARCHAR(500),
    date_suggestion DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_traitement DATETIME,
    statut ENUM('En attente', 'Acceptée', 'Rejetée') DEFAULT 'En attente',
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(idUtilisateur) ON DELETE CASCADE,
    INDEX(statut),
    INDEX(date_suggestion)
);
