<?php

namespace App\Models;

class Page
{
    public $id;
    public $chapter_id;
    public $page_number;
    public $image_url;
    public $created_at;

    public function __construct() {
    }

    /**
     * Lấy danh sách tất cả các trang vẽ thuộc các bộ truyện của Mangaka
     */
    public function findByMangakaId($mangakaId) {
        $sql = "SELECT p.*, c.chapter_number, c.status as chapter_status, c.title as chapter_title, s.title as series_title 
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
        $sql = "SELECT p.*, c.status AS chapter_status, COUNT(ea.annotation_id) AS annotation_count, MAX(ea.created_at) AS latest_annotation_time
                FROM {$this->table} p 
                JOIN chapters c ON p.chapter_id = c.chapter_id
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
