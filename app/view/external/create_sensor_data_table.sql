-- Création de la table sensor_data pour stocker les données ESP32
CREATE TABLE IF NOT EXISTS `sensor_data` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_capteur` INT(11) NOT NULL,
  `temperature` DECIMAL(5,2) NOT NULL,
  `humidite` DECIMAL(5,2) NOT NULL,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_capteur_timestamp` (`id_capteur`, `timestamp`),
  CONSTRAINT `fk_sensor_capteur` FOREIGN KEY (`id_capteur`) REFERENCES `capteur` (`id_capteur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index pour optimiser les requêtes de dernière lecture
CREATE INDEX IF NOT EXISTS `idx_timestamp_desc` ON `sensor_data` (`timestamp` DESC);
