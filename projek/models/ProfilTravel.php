<?php
// ProfilTravel model
// This class expects a PDO connection injected. If none provided, it will attempt
// to create one using the project's Database class (if available). This avoids
// fatal errors when the model file is included from views.

require_once __DIR__ . '/../config/database.php';

class ProfilTravel
{
    private $conn;
    private $table_name = "profil_travel";

    public $id;
    public $alamat;
    public $email;
    public $nama;
    public $deskripsi;
    public $telepon;
    public $website;
    public $logo;
    public $image;
    public $visi;
    public $misi;


    public $lastError;

    public function __construct($db = null)
    {
        if ($db instanceof PDO) {
            $this->conn = $db;
        } else {
            // try to create a connection if Database class exists
            if (class_exists('Database')) {
                $database = new Database();
                $this->conn = $database->getConnection();
            } else {
                $this->conn = null;
            }
        }
    }

    // Baca profil (return associative array or null)
    // Default to the string primary key used in the initial seed: 'profil_id'
    public function getById($id = 'profil_id')
    {
        if (!$this->conn) return null;

        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // Update profile (updates only provided non-empty fields) - updates row id=1
    public function update()
    {
        if (!$this->conn) return false;
        $fields = [];
        $params = [];

        if (isset($this->nama)) {
            $fields[] = 'nama = :nama';
            $params[':nama'] = $this->nama;
        }
        if (isset($this->deskripsi)) {
            $fields[] = 'deskripsi = :deskripsi';
            $params[':deskripsi'] = $this->deskripsi;
        }
        if (isset($this->alamat)) {
            $fields[] = 'alamat = :alamat';
            $params[':alamat'] = $this->alamat;
        }
        if (isset($this->telepon)) {
            $fields[] = 'telepon = :telepon';
            $params[':telepon'] = $this->telepon;
        }
        if (isset($this->email)) {
            $fields[] = 'email = :email';
            $params[':email'] = $this->email;
        }
        if (isset($this->website)) {
            $fields[] = 'website = :website';
            $params[':website'] = $this->website;
        }
        if (isset($this->logo)) {
            $fields[] = 'logo = :logo';
            $params[':logo'] = $this->logo;
        }
        if (isset($this->image)) {
            $fields[] = 'image = :image';
            $params[':image'] = $this->image;
        }
        if (isset($this->visi)) {
            $fields[] = 'visi = :visi';
            $params[':visi'] = $this->visi;
        }
        if (isset($this->misi)) {
            $fields[] = 'misi = :misi';
            $params[':misi'] = $this->misi;
        }

        if (empty($fields)) return false;

        $sql = "UPDATE " . $this->table_name . " SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        // profil_travel table uses a string PK value 'profil_id'
        $params[':id'] = 'profil_id';

        try {
            return $stmt->execute($params);
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            error_log('ProfilTravel::update error: ' . $e->getMessage());
            return false;
        }
    }



    // original read method kept for compatibility
    public function read()
    {
        if (!$this->conn) return null;
        $query = "SELECT * FROM " . $this->table_name . " LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
