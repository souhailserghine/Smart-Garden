-- Seed default categories (insert only if not exists)

INSERT INTO categorie (nom, description)
SELECT 'culturelles', 'Événements liés à la culture (concerts, expos, théâtre)'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM categorie WHERE nom = 'culturelles');

;

INSERT INTO categorie (nom, description)
SELECT 'sportif', 'Événements sportifs (compétitions, entraînements)'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM categorie WHERE nom = 'sportif');

;

INSERT INTO categorie (nom, description)
SELECT 'qualitatif', 'Événements qualitatifs (ateliers, formations, conférences)'
FROM DUAL

WHERE NOT EXISTS (SELECT 1 FROM categorie WHERE nom = 'qualitatif');

;
