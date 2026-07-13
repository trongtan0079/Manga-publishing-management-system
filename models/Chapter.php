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

    /**
     * Lấy danh sách chapter theo series ID kèm thống kê số lượng task trợ lý
     */
    public function findBySeriesIdWithStats($seriesId) {
        $sql = "SELECT c.*,
                (SELECT COUNT(*) FROM tasks t JOIN pages p ON t.page_id = p.page_id WHERE p.chapter_id = c.chapter_id) as total_tasks,
                (SELECT COUNT(*) FROM tasks t JOIN pages p ON t.page_id = p.page_id WHERE p.chapter_id = c.chapter_id AND t.status = 'completed') as completed_tasks
                FROM {$this->table} c
                WHERE c.series_id = :series_id
                ORDER BY c.chapter_number ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':series_id', $seriesId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách chapter của Mangaka kèm thống kê số lượng task trợ lý
     */
    public function findByMangakaIdWithStats($mangakaId) {
        $sql = "SELECT c.*, s.title as series_title,
                (SELECT COUNT(*) FROM tasks t JOIN pages p ON t.page_id = p.page_id WHERE p.chapter_id = c.chapter_id) as total_tasks,
                (SELECT COUNT(*) FROM tasks t JOIN pages p ON t.page_id = p.page_id WHERE p.chapter_id = c.chapter_id AND t.status = 'completed') as completed_tasks
                FROM {$this->table} c 
                JOIN series s ON c.series_id = s.series_id 
                WHERE s.mangaka_id = :mangaka_id 
                ORDER BY s.title ASC, c.chapter_number ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':mangaka_id', $mangakaId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Kiểm tra truyện đã có chapter cuối cùng hay chưa
     */
    public function hasFinalChapter($seriesId, $excludeChapterId = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE series_id = :series_id AND is_final = 1";
        if ($excludeChapterId) {
            $sql .= " AND chapter_id != :exclude_id";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':series_id', $seriesId);
        if ($excludeChapterId) {
            $stmt->bindParam(':exclude_id', $excludeChapterId);
        }
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Đếm số lượng chapter chưa hoàn thành của một truyện
     */
    public function countUnfinishedChapters($seriesId) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE series_id = :series_id AND status IN ('drafting', 'drawing', 'reviewing', 'reviewing_draft', 'reviewing_final')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':series_id', $seriesId);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /**
     * Kiểm tra truyện đã có chapter cuối và chapter đó đã được approved/published chưa
     */
    public function hasFinalApprovedChapter($seriesId) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE series_id = :series_id AND is_final = 1 AND status IN ('approved', 'published')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':series_id', $seriesId);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Lấy danh sách chapter đã được Editor duyệt (approved) nhưng chưa xuất bản
     */
    public function findApprovedChapters() {
        $sql = "SELECT c.*, s.title as series_title, u.full_name as mangaka_name
                FROM {$this->table} c
                JOIN series s ON c.series_id = s.series_id
                JOIN users u ON s.mangaka_id = u.user_id
                WHERE c.status = 'approved' AND s.status IN ('ongoing', 'planning')
                ORDER BY s.title ASC, c.chapter_number ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách các chapter đã được xuất bản (published)
     */
    public function findPublishedChapters() {
        $sql = "SELECT c.*, s.title as series_title, u.full_name as mangaka_name
                FROM {$this->table} c
                JOIN series s ON c.series_id = s.series_id
                JOIN users u ON s.mangaka_id = u.user_id
                WHERE c.status = 'published'
                ORDER BY c.updated_at DESC, s.title ASC, c.chapter_number ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countByMangakaId($mangakaId) {
        $sql = "SELECT COUNT(*) as total FROM chapters c JOIN series s ON c.series_id = s.series_id WHERE s.mangaka_id = :mangaka_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['mangaka_id' => $mangakaId]);
        return (int)$stmt->fetchColumn();
    }

    public function getUpcomingDeadlinesByEditor($editorId) {
        $sql = "SELECT c.chapter_number, c.title as chapter_title, c.due_date, s.title as series_title, u.full_name as mangaka_name
                FROM chapters c
                JOIN series s ON c.series_id = s.series_id
                JOIN users u ON s.mangaka_id = u.user_id
                WHERE c.status NOT IN ('approved', 'published') 
                  AND c.due_date IS NOT NULL 
                  AND s.editor_id = :editor_id
                  AND s.status != 'planning'
                ORDER BY c.due_date ASC
                LIMIT 5";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['editor_id' => $editorId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
