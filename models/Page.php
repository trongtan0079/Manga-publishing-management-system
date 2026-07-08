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

    /**
     * Lấy danh sách tất cả các trang vẽ thuộc các bộ truyện của Mangaka
     */
    public function findByMangakaId($mangakaId) {
        $sql = "SELECT p.*, c.chapter_number, c.title as chapter_title, s.title as series_title 
                FROM {$this->table} p 
                JOIN chapters c ON p.chapter_id = c.chapter_id
                JOIN series s ON c.series_id = s.series_id 
                WHERE s.mangaka_id = :mangaka_id 
                ORDER BY s.title ASC, c.chapter_number ASC, p.page_number ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':mangaka_id', $mangakaId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách trang theo chapter ID, kèm theo số lượng annotation và mốc thời gian annotation gần nhất
     */
    public function findByChapterIdWithAnnotationCount($chapterId) {
        $sql = "SELECT p.*, COUNT(ea.annotation_id) AS annotation_count, MAX(ea.created_at) AS latest_annotation_time
                FROM {$this->table} p 
                LEFT JOIN editor_annotations ea ON p.page_id = ea.page_id 
                WHERE p.chapter_id = :chapter_id 
                GROUP BY p.page_id 
                ORDER BY p.page_number ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':chapter_id', $chapterId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countByMangakaId($mangakaId) {
        $sql = "SELECT COUNT(*) as total FROM pages p JOIN chapters c ON p.chapter_id = c.chapter_id JOIN series s ON c.series_id = s.series_id WHERE s.mangaka_id = :mangaka_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['mangaka_id' => $mangakaId]);
        return (int)$stmt->fetchColumn();
    }

    public function getPageNumbersByChapterId($chapterId) {
        $sql = "SELECT page_number FROM pages WHERE chapter_id = :chapter_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':chapter_id' => $chapterId]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function getOtherPageNumbers($chapterId, $pageId) {
        $sql = "SELECT page_number FROM pages WHERE chapter_id = :chapter_id AND page_id != :page_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':chapter_id' => $chapterId, ':page_id' => $pageId]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Cập nhật trạng thái cho tất cả các trang thuộc chapter
     */
    public function updateStatusByChapterId($chapterId, $status) {
        $sql = "UPDATE {$this->table} SET status = :status WHERE chapter_id = :chapter_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':chapter_id', $chapterId);
        return $stmt->execute();
    }
}
