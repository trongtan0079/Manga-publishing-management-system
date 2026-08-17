<?php

class Database
{
    private $host;
    private $port;
    private $dbname;
    private $username;
    private $password;

    private $conn;

    public function connect()
    {
        $this->conn = null;

        // Read database configuration from environment variables.
        // Fallback values allow the application to continue working with XAMPP.
        $this->host = getenv('DB_HOST') ?: '127.0.0.1';
        $this->port = getenv('DB_PORT') ?: '3307';
        $this->dbname = getenv('DB_NAME') ?: 'manga_workflow';
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: '';

        try {
            $dsn = "mysql:host=" . $this->host
                 . ";port=" . $this->port
                 . ";dbname=" . $this->dbname
                 . ";charset=utf8mb4";

            $this->conn = new PDO(
                $dsn,
                $this->username,
                $this->password
            );

            $this->conn->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $this->conn->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC
            );

        } catch (PDOException $e) {
            echo "Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage();
        }

        return $this->conn;
    }
}