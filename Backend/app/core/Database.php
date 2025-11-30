<?php

class Database
{
    private $host = 'localhost';
    private $dbname = 'project';   
    private $username = 'root';
    private $password = '';

    public function connect()
    {
        try {
            $pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            return $pdo;
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            die(json_encode([
                "status" => "error",
                "message" => "Connexion à la base de données échouée",
                "detail" => $e->getMessage()
            ]));
        }
    }
}