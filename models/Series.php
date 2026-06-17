<?php
require_once __DIR__ . '/../core/Model.php';

class Series extends Model {
    public function __construct() {
        parent::__construct();
        $this->table = 'series';
        $this->primaryKey = 'series_id';
    }

    /**
     * Lấy danh sách series theo ID của mangaka
     */
    public function findByMangakaId($mangakaId) {
        $sql = "SELECT * FROM {$this->table} WHERE mangaka_id = :mangaka_id ORDER BY series_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':mangaka_id', $mangakaId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách series theo trạng thái
     */
    public function findByStatus($status) {
        $sql = "SELECT * FROM {$this->table} WHERE status = :status ORDER BY series_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
