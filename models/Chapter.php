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

    /**
     * Kiểm tra xem chapter_number đã tồn tại trong series chưa
     */
    public function isChapterNumberExists($seriesId, $chapterNumber, $excludeChapterId = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE series_id = :series_id AND chapter_number = :chapter_number";
        if ($excludeChapterId) {
            $sql .= " AND chapter_id != :exclude_id";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':series_id', $seriesId);
        $stmt->bindParam(':chapter_number', $chapterNumber);
        if ($excludeChapterId) {
            $stmt->bindParam(':exclude_id', $excludeChapterId);
        }
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Lấy danh sách chapter thuộc các series của Mangaka
     */
    public function findByMangakaId($mangakaId) {
        $sql = "SELECT c.*, s.title as series_title 
                FROM {$this->table} c 
                JOIN series s ON c.series_id = s.series_id 
                WHERE s.mangaka_id = :mangaka_id 
                ORDER BY s.title ASC, c.chapter_number ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':mangaka_id', $mangakaId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

