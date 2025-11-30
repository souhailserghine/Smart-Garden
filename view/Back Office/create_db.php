<?php
/**
 * Script d'aide pour créer la base `smartgarden` et la table `evenement`.
 * Usage: ouvrez dans le navigateur: http://localhost/view/view/Back%20Office/create_db.php
 * ATTENTION: utilise l'utilisateur MySQL 'root' sans mot de passe par défaut (XAMPP).
 */
header('Content-Type: application/json');
try {
    $host = '127.0.0.1';
    $user = 'root';
    $pass = '';
    // connexion sans base
    $pdo = new PDO("mysql:host=$host;charset=utf8", $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

    // create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `smartgarden` CHARACTER SET utf8 COLLATE utf8_general_ci;");
    // create table if not exists
    $pdo->exec("USE `smartgarden`;");
    $pdo->exec("CREATE TABLE IF NOT EXISTS `evenement` (
        `id_event` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `type_event` VARCHAR(255) NOT NULL,
        `date_event` DATE NOT NULL,
        `etat` ENUM('active','inactive') NOT NULL DEFAULT 'active'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    echo json_encode(['status'=>'success','message'=>'Database and table ensured (smartgarden.evenement)']);
} catch (Exception $e) {
    // log server side
    error_log('create_db.php error: ' . $e->getMessage());
    echo json_encode(['status'=>'error','message'=>'Could not create database/table. See server logs.']);
}

?>
