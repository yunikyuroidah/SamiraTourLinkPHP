<?php
require_once __DIR__ . '/../config/database.php';

class Paket
{
    private $conn;
    private $table = 'paket';

    public $id;
    public $nama_paket;
    public $deskripsi;
    public $fasilitas; // JSON string
    public $features;  // JSON string

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

    // Return PDOStatement for compatibility with admin pages
    public function read()
    {
        if (!$this->conn) return null;
        try {
            $query = "SELECT id, nama_paket, deskripsi, fasilitas, features FROM " . $this->table . " ORDER BY id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return null;
        }
    }

    // Fetch all as array
    public function getAll()
    {
        if (!$this->conn) return [];
        try {
            $stmt = $this->read();
            if (!$stmt) return [];
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getById($id)
    {
        if (!$this->conn) return false;
        $intId = is_numeric($id) ? (int)$id : null;
        if ($intId === null) return false;
        try {
            $query = "SELECT id, nama_paket, deskripsi, fasilitas, features FROM " . $this->table . " WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $intId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function create()
    {
        if (!$this->conn) return false;
        try {
            $query = "INSERT INTO " . $this->table . " (nama_paket, deskripsi, fasilitas, features) VALUES (:nama_paket, :deskripsi, :fasilitas, :features)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nama_paket', $this->nama_paket);
            $stmt->bindParam(':deskripsi', $this->deskripsi);
            $stmt->bindParam(':fasilitas', $this->fasilitas);
            $stmt->bindParam(':features', $this->features);
            $executed = $stmt->execute();
            if ($executed) {
                $this->id = (int)$this->conn->lastInsertId();
            }
            return $executed;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function update()
    {
        if (!$this->conn) return false;
        $intId = is_numeric($this->id) ? (int)$this->id : null;
        if ($intId === null) return false;
        try {
            $query = "UPDATE " . $this->table . " SET nama_paket = :nama_paket, deskripsi = :deskripsi, fasilitas = :fasilitas, features = :features WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $intId, PDO::PARAM_INT);
            $stmt->bindParam(':nama_paket', $this->nama_paket);
            $stmt->bindParam(':deskripsi', $this->deskripsi);
            $stmt->bindParam(':fasilitas', $this->fasilitas);
            $stmt->bindParam(':features', $this->features);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete()
    {
        if (!$this->conn) return false;
        $intId = is_numeric($this->id) ? (int)$this->id : null;
        if ($intId === null) return false;
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $intId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function count()
    {
        if (!$this->conn) return 0;
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return intval($result['total'] ?? 0);
        } catch (PDOException $e) {
            return 0;
        }
    }
}
