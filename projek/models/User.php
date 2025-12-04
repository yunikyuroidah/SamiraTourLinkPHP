<?php
// User model - expects a PDO connection to be injected. If none provided,
// it will attempt to create one using Database class.
require_once __DIR__ . '/../config/database.php';

class User
{
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $password;

    public function __construct($db = null)
    {
        if ($db instanceof PDO) {
            $this->conn = $db;
        } else {
            if (class_exists('Database')) {
                $database = new Database();
                $this->conn = $database->getConnection();
            } else {
                $this->conn = null;
            }
        }
    }

    // Check if user is blocked
    public function isBlocked()
    {
        if (!$this->username || !$this->conn) return false;

        $query = "SELECT blocked_until FROM " . $this->table_name . " 
                WHERE username = :username AND blocked_until > NOW()";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":username", $this->username);
            $stmt->execute();

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Update login attempts
    private function updateLoginAttempts($success)
    {
        if (!$this->conn || !$this->username) return false;

        if ($success) {
            // Reset attempts on successful login
            $query = "UPDATE " . $this->table_name . " 
                    SET login_attempts = 0, 
                        last_attempt = NOW(), 
                        blocked_until = NULL 
                    WHERE username = :username";
        } else {
            // Increment attempts and block if needed
            $query = "UPDATE " . $this->table_name . " 
                    SET login_attempts = login_attempts + 1,
                        last_attempt = NOW(),
                        blocked_until = CASE 
                            WHEN login_attempts + 1 >= 3 THEN DATE_ADD(NOW(), INTERVAL 1 WEEK)
                            ELSE blocked_until 
                        END
                    WHERE username = :username";
        }

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":username", $this->username);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Login
    function login()
    {
        if (!$this->conn) return false;

        $query = "SELECT id, username FROM " . $this->table_name . " 
                WHERE username = :username AND password = SHA2(:password, 256) LIMIT 1";

        try {
            $stmt = $this->conn->prepare($query);

            // sanitize
            $this->username = htmlspecialchars(strip_tags($this->username));
            $this->password = htmlspecialchars(strip_tags($this->password));

            // bind values
            $stmt->bindParam(":username", $this->username);
            $stmt->bindParam(":password", $this->password);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->id = $row['id'];
                $this->username = $row['username'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Get remaining attempts
    public function getRemainingAttempts()
    {
        if (!$this->conn || !$this->username) return 3;

        $query = "SELECT login_attempts FROM " . $this->table_name . " 
                WHERE username = :username";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":username", $this->username);
            $stmt->execute();

            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return max(0, 3 - $row['login_attempts']);
            }
            return 3;
        } catch (PDOException $e) {
            return 3;
        }
    }

    // Get block time remaining
    public function getBlockTimeRemaining()
    {
        if (!$this->conn || !$this->username) return 0;

        $query = "SELECT blocked_until FROM " . $this->table_name . " 
                WHERE username = :username AND blocked_until > NOW()";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":username", $this->username);
            $stmt->execute();

            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $blocked_until = strtotime($row['blocked_until']);
                $now = time();
                return max(0, $blocked_until - $now);
            }
            return 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    // Baca semua users
    function read()
    {
        if (!$this->conn) return false;

        $query = "SELECT id, username FROM " . $this->table_name . " ORDER BY id";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Tambah user
    function create()
    {
        if (!$this->conn || !$this->username || !$this->password) return false;

        $query = "INSERT INTO " . $this->table_name . " 
                SET username=:username, password=SHA2(:password, 256)";

        try {
            $stmt = $this->conn->prepare($query);

            // sanitize
            $this->username = htmlspecialchars(strip_tags($this->username));
            $this->password = htmlspecialchars(strip_tags($this->password));

            // bind values
            $stmt->bindParam(":username", $this->username);
            $stmt->bindParam(":password", $this->password);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
