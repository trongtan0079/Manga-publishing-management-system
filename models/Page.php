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
}
