<?php

class Database
{
    private $host = "localhost";
    private $db_name = "samira_travel"; // diperbarui sesuai permintaan
    private $username = "root";
    private $password = "";
    private $port = "3306"; // tambahkan port

    private $conn;

    public function getConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
        } catch (PDOException $exception) {
            // Don't echo errors directly — use error_log so API JSON isn't broken by stray output
            error_log("Database connection error: " . $exception->getMessage());
        }
        return $this->conn;
    }
}
