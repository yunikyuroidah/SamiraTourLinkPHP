<?php
require_once __DIR__ . '/../config/database.php';

class TourLeader
{
    private $conn;
    private $table_name = "tour_leader";

    public $id;
    public $nama;
    public $telepon;
    public $gambar_base64;

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

    // Baca tour leader
    function read()
    {
        if (!$this->conn) return null;
        // Ambil record tour leader yang paling relevan secara deterministik.
        // Jika hanya ada satu record, ini sama seperti sebelumnya.
        // Jika ada banyak record, ambil baris dengan id terbesar (terakhir diinsert).
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Update tour leader
    function update()
    {
        if (!$this->conn) return false;
        // build dynamic SET clause so we don't overwrite image with empty value
        $setParts = ["nama=:nama", "telepon=:telepon"];
        if (!empty($this->gambar_base64)) {
            $setParts[] = "gambar_base64=:gambar_base64";
        }

        $setSql = implode(', ', $setParts);

        $query = "UPDATE " . $this->table_name . " SET " . $setSql . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->nama = htmlspecialchars(strip_tags($this->nama));
        $this->telepon = htmlspecialchars(strip_tags($this->telepon));
        $this->id = htmlspecialchars(strip_tags($this->id));

        try {
            // bind values
            $stmt->bindValue(":nama", $this->nama, PDO::PARAM_STR);
            $stmt->bindValue(":telepon", $this->telepon, PDO::PARAM_STR);
            if (!empty($this->gambar_base64)) {
                // store as large text/blob
                $stmt->bindValue(":gambar_base64", $this->gambar_base64, PDO::PARAM_LOB);
            }
            $stmt->bindValue(":id", $this->id, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('TourLeader::update error: ' . $e->getMessage());
            return false;
        }
    }
}
