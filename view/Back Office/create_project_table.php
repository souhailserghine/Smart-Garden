<?php
// Helper: create `evenement` table inside the `project` database (local XAMPP)
try {
    $pdo = new PDO('mysql:host=localhost;dbname=project;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "CREATE TABLE IF NOT EXISTS `evenement` (
      `id_event` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `type_event` VARCHAR(255) NOT NULL,
      `date_event` DATE NOT NULL,
      `description` TEXT DEFAULT NULL,
      `etat` ENUM('active','inactive') NOT NULL DEFAULT 'active',
      `idUtilisateur` INT DEFAULT NULL,
      PRIMARY KEY (`id_event`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($sql);
    echo json_encode(["status"=>"success","message"=>"Table `evenement` ensured in database 'project'."]);
} catch (Exception $e) {
    error_log('create_project_table error: ' . $e->getMessage());
    echo json_encode(["status"=>"error","message"=>"Could not create table. Check error log."]); 
}

?>