<?php
require_once __DIR__ . '/../core/Model.php';

class Page extends Model {
    public function __construct() {
        parent::__construct();
        $this->table = 'pages';
        $this->primaryKey = 'page_id';
    }

    /**
     * Lấy danh sách trang theo chapter ID, sắp xếp theo số trang
     */
    public function findByChapterId($chapterId) {
        $sql = "SELECT * FROM {$this->table} WHERE chapter_id = :chapter_id ORDER BY page_number ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':chapter_id', $chapterId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Kiểm tra xem số trang (page_number) đã tồn tại trong một chapter chưa
     * Dùng khi thêm mới (excludeId = null) hoặc cập nhật (excludeId != null)
     */
    public function isPageNumberExists($chapterId, $pageNumber, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE chapter_id = :chapter_id AND page_number = :page_number";
        if ($excludeId !== null) {
            $sql .= " AND {$this->primaryKey} != :exclude_id";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':chapter_id', $chapterId);
        $stmt->bindParam(':page_number', $pageNumber);
        if ($excludeId !== null) {
            $stmt->bindParam(':exclude_id', $excludeId);
        }
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}
