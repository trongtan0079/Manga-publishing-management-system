<?php
require_once __DIR__ . '/../core/Model.php';

class Chapter extends Model {
    public function __construct() {
        parent::__construct();
        $this->table = 'chapters';
        $this->primaryKey = 'chapter_id';
    }

    /**
     * Lấy danh sách chapter theo series ID, sắp xếp theo số chương
     */
    public function findBySeriesId($seriesId) {
        $sql = "SELECT * FROM {$this->table} WHERE series_id = :series_id ORDER BY chapter_number ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':series_id', $seriesId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
