-- Migration: create table `categorie` and link to `evenement`

CREATE TABLE IF NOT EXISTS `categorie` (
  `id_categorie` INT(11) NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  PRIMARY KEY (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add column on `evenement` (if not exists) and foreign key
ALTER TABLE `evenement`
  ADD COLUMN IF NOT EXISTS `id_categorie` INT(11) NULL;

-- Add foreign key constraint (if not exists)
ALTER TABLE `evenement`
  ADD CONSTRAINT `fk_evenement_categorie` FOREIGN KEY (`id_categorie`) REFERENCES `categorie`(`id_categorie`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Notes:
-- 1) MySQL < 8.0 does not support `ADD COLUMN IF NOT EXISTS` — adapt the statements when running.
-- 2) Run this file from phpMyAdmin or mysql CLI in your project's database.
