<?php
require_once __DIR__ . '/../config/database.php';

class GalleryAsset
{
    private $conn;
    private $table = 'gallery_assets';
    private $columns = null;

    public function __construct($db = null)
    {
        if ($db instanceof PDO) {
            $this->conn = $db;
        } else {
            $database = new Database();
            $this->conn = $database->getConnection();
        }

        $this->ensureContentColumns();
    }

    // helper: detect available columns in the gallery table
    private function hasColumn($name)
    {
        if (!$this->conn) return false;
        if ($this->columns === null) {
            $this->columns = [];
            try {
                $stmt = $this->conn->prepare("DESCRIBE `" . $this->table . "`");
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $r) {
                    $this->columns[] = $r['Field'];
                }
            } catch (Exception $e) {
                $this->columns = [];
            }
        }
        return in_array($name, $this->columns, true);
    }

    private function invalidateColumns()
    {
        $this->columns = null;
    }

    public function canStoreInlineContent()
    {
        return $this->hasColumn('content') || $this->hasColumn('content_base64');
    }

    private function ensureContentColumns()
    {
        if (!$this->conn) return;

        $changed = false;

        if (!$this->hasColumn('content') && !$this->hasColumn('content_base64')) {
            try {
                $this->conn->exec("ALTER TABLE `" . $this->table . "` ADD COLUMN `content` LONGTEXT NULL");
                $changed = true;
            } catch (Exception $e) {
                // ignore lack of privileges
            }
        }

        if ($changed) {
            $this->invalidateColumns();
        }
    }

    public function all()
    {
        if (!$this->conn) {
            // DB not available
            return [];
        }
        try {
            $stmt = $this->conn->prepare("SELECT * FROM `" . $this->table . "` ORDER BY uploaded_at DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function find($id)
    {
        if (!$this->conn) return false;
        $stmt = $this->conn->prepare("SELECT * FROM `" . $this->table . "` WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        if (!$this->conn) return false;

        $fields = [];
        $placeholders = [];
        $params = [];

        // Name is required and assumed to exist
        $fields[] = '`name`';
        $placeholders[] = ':name';
        $params[':name'] = $data['name'];

        if ($this->hasColumn('deskripsi')) {
            $fields[] = '`deskripsi`';
            $placeholders[] = ':deskripsi';
            $params[':deskripsi'] = $data['deskripsi'] ?? null;
        }

        if ($this->hasColumn('uploaded_by')) {
            $fields[] = '`uploaded_by`';
            $placeholders[] = ':uploaded_by';
            $params[':uploaded_by'] = $data['uploaded_by'] ?? null;
        }

        if ($this->hasColumn('uploaded_at')) {
            $fields[] = '`uploaded_at`';
            $placeholders[] = ':uploaded_at';
            $params[':uploaded_at'] = $data['uploaded_at'] ?? date('Y-m-d H:i:s');
        }

        $contentCol = null;
        if ($this->hasColumn('content')) {
            $contentCol = 'content';
        } elseif ($this->hasColumn('content_base64')) {
            $contentCol = 'content_base64';
        }

        if ($contentCol && (isset($data['content']) || isset($data[$contentCol]))) {
            $fields[] = "`$contentCol`";
            $placeholders[] = ':content';
            $params[':content'] = $data['content'] ?? $data[$contentCol];

            if ($this->hasColumn('filename') && array_key_exists('filename', $data)) {
                $fields[] = '`filename`';
                $placeholders[] = ':filename';
                $params[':filename'] = $data['filename'];
            } elseif ($this->hasColumn('filename')) {
                // Legacy schema keeps filename NOT NULL; persist empty string when storing inline
                $fields[] = '`filename`';
                $placeholders[] = ':filename';
                $params[':filename'] = '';
            }
        } elseif ($this->hasColumn('filename')) {
            $fields[] = '`filename`';
            $placeholders[] = ':filename';
            $params[':filename'] = $data['filename'] ?? null;
        }

        if (empty($fields) || empty($placeholders)) {
            return false;
        }

        $sql = "INSERT INTO `" . $this->table . "` (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->conn->prepare($sql);
        if ($stmt->execute($params)) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update($id, $data)
    {
        if (!$this->conn) return false;

        $fields = [];
        $params = [':id' => $id];

        if (isset($data['name'])) {
            $fields[] = '`name` = :name';
            $params[':name'] = $data['name'];
        }

        if ($this->hasColumn('deskripsi') && array_key_exists('deskripsi', $data)) {
            $fields[] = '`deskripsi` = :deskripsi';
            $params[':deskripsi'] = $data['deskripsi'];
        }

        if ($this->hasColumn('uploaded_by') && array_key_exists('uploaded_by', $data)) {
            $fields[] = '`uploaded_by` = :uploaded_by';
            $params[':uploaded_by'] = $data['uploaded_by'];
        }

        $contentCol = null;
        if ($this->hasColumn('content')) {
            $contentCol = 'content';
        } elseif ($this->hasColumn('content_base64')) {
            $contentCol = 'content_base64';
        }

        if ($contentCol && array_key_exists('content', $data)) {
            $fields[] = "`$contentCol` = :content";
            $params[':content'] = $data['content'];

            if ($this->hasColumn('filename') && array_key_exists('filename', $data)) {
                $fields[] = '`filename` = :filename';
                $params[':filename'] = $data['filename'];
            } elseif ($this->hasColumn('filename') && !array_key_exists('filename', $data)) {
                // When switching to inline storage ensure filename cleared out
                $fields[] = '`filename` = :filename';
                $params[':filename'] = '';
            }
        } elseif ($this->hasColumn('filename') && array_key_exists('filename', $data)) {
            $fields[] = '`filename` = :filename';
            $params[':filename'] = $data['filename'];
        } elseif (!$contentCol && $this->hasColumn('filename') && array_key_exists('content', $data)) {
            // Legacy schema without content columns: optionally stuff content into filename (truncated for safety)
            $fields[] = '`filename` = :filename';
            $params[':filename'] = substr($data['content'], 0, 255);
        }

        if (empty($fields)) return false;

        $sql = "UPDATE `" . $this->table . "` SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id)
    {
        if (!$this->conn) return false;
        $stmt = $this->conn->prepare("DELETE FROM `" . $this->table . "` WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function count()
    {
        if (!$this->conn) return 0;
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM `" . $this->table . "`");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return intval($result['total'] ?? 0);
        } catch (PDOException $e) {
            return 0;
        }
    }
}
