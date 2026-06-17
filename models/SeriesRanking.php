<?php
require_once __DIR__ . '/../core/Model.php';

class SeriesRanking extends Model {
    public function __construct() {
        parent::__construct();
        $this->table = 'series_rankings';
        $this->primaryKey = 'ranking_id';
    }

    /**
     * Lấy lịch sử xếp hạng của một Series
     */
    public function findBySeriesId($seriesId) {
        $sql = "SELECT * FROM {$this->table} WHERE series_id = :series_id ORDER BY period_start_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':series_id', $seriesId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
