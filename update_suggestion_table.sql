-- Mettre à jour la table suggestionplante pour avoir les mêmes champs que plante
ALTER TABLE suggestionplante 
ADD COLUMN IF NOT EXISTS niveau_humidite INT DEFAULT 50,
ADD COLUMN IF NOT EXISTS besoin_eau INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS etat_sante VARCHAR(50) DEFAULT 'Bon état',
MODIFY id_utilisateur INT NOT NULL;

-- Créer un index sur id_utilisateur pour la clé étrangère
ALTER TABLE suggestionplante 
ADD CONSTRAINT fk_suggestion_utilisateur 
FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(idUtilisateur) ON DELETE CASCADE;
