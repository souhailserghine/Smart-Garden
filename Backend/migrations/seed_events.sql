-- Seed example events linked to categories (will only insert if the category exists)

INSERT INTO evenement (type_event, date_event, description, etat, id_categorie)
SELECT 'Concert Jazz', '2025-12-01', 'Soirée jazz au théâtre municipal', 'active', id_categorie
FROM categorie WHERE nom = 'culturelles';

INSERT INTO evenement (type_event, date_event, description, etat, id_categorie)
SELECT 'Match local', '2025-11-30', 'Rencontre amicale entre équipes locales', 'active', id_categorie
FROM categorie WHERE nom = 'sportif';

INSERT INTO evenement (type_event, date_event, description, etat, id_categorie)
SELECT 'Atelier qualité', '2025-12-05', 'Atelier qualité et bonnes pratiques', 'active', id_categorie
FROM categorie WHERE nom = 'qualitatif';

-- Event without category
INSERT INTO evenement (type_event, date_event, description, etat)
SELECT 'Événement libre', '2025-12-10', 'Événement sans catégorie assignée', 'active'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM evenement WHERE type_event = 'Événement libre' AND date_event = '2025-12-10');
