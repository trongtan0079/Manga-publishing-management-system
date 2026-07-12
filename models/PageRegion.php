<?php
require_once __DIR__ . '/../core/Model.php';

class PageRegion extends Model {
    public function __construct() {
        parent::__construct();
        $this->table = 'page_regions';
        $this->primaryKey = 'region_id';
    }

    /**
     * Lấy tất cả các vùng của 1 trang truyện
     */
    public function findByPageId($pageId) {
        $sql = "SELECT * FROM {$this->table} WHERE page_id = :page_id ORDER BY region_id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':page_id', $pageId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
