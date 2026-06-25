<?php
require_once __DIR__ . '/../config/database.php';

class Model {
    protected $conn;
    protected static $sharedConn = null; // Thêm biến static để chia sẻ kết nối
    protected $table;
    protected $primaryKey = 'id'; // Sẽ được override ở class con

    public function __construct() {
        // Chỉ khởi tạo kết nối 1 lần duy nhất cho tất cả các model
        if (self::$sharedConn === null) {
            $database = new Database();
            self::$sharedConn = $database->connect();
        }
        $this->conn = self::$sharedConn;

        if ($this->conn === null) {
            throw new Exception("Database connection failed.");
        }
    }

    /**
     * Lấy kết nối CSDL (PDO instance)
     */
    public function getConnection() {
        return $this->conn;
    }

    /**
     * Lấy tất cả bản ghi
     */
    public function findAll() {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy 1 bản ghi theo ID
     */
    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Thêm mới 1 bản ghi
     * @param array $data Mảng dữ liệu ['column' => 'value']
     */
    public function insert(array $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->conn->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Cập nhật bản ghi theo ID
     * @param int $id ID của bản ghi
     * @param array $data Mảng dữ liệu cập nhật ['column' => 'value']
     */
    public function update($id, array $data) {
        $setParts = [];
        foreach ($data as $key => $value) {
            $setParts[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $setParts);

        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = :id";
        $stmt = $this->conn->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    /**
     * Xóa bản ghi theo ID
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Đếm tổng số bản ghi
     */
    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Đếm số bản ghi theo điều kiện đơn giản (AND)
     * @param array $conditions Mảng ['column' => 'value']
     */
    public function countByCondition(array $conditions) {
        $whereParts = [];
        foreach ($conditions as $key => $value) {
            $whereParts[] = "{$key} = :{$key}";
        }
        $whereClause = implode(' AND ', $whereParts);
        
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        if (!empty($whereClause)) {
            $sql .= " WHERE {$whereClause}";
        }
        
        $stmt = $this->conn->prepare($sql);
        foreach ($conditions as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? (int)$result['total'] : 0;
    }
}
