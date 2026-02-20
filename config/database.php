<?php
// config/database.php

class Database
{
    private string $host = "localhost";
    private string $db_name = "inventory_system";
    private string $username = "inventory_user";
    private string $password = "securepassword";
    private string $charset = "utf8mb4";
    private ?PDO $pdo = null;

    // Singleton Pattern, damit nur eine Connection existiert.
    public function getConnection(): PDO
    {
        if ($this->pdo === null) {
            
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
            } catch (PDOException $e) {
                // professionelles Logging könnte hier ergänzt werden
                // zb. $logger->error($e->getMessage());
                throw new RuntimeException('Database connection failed: ' . $e->getMessage());
            }

        }
        return $this->pdo;
    }
}