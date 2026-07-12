<?php

namespace App\Models;

class Submission
{
    public $id;
    public $task_id;
    public $file_url;
    public $submitted_by;
    public $submitted_at;

    public function __construct() {
        parent::__construct();
        $this->table = 'submissions';
        $this->primaryKey = 'submission_id';
    }

    /**
     * Lấy các lần nộp bài theo Task ID
     */
    public function findByTaskId($taskId) {
        $sql = "SELECT * FROM {$this->table} WHERE task_id = :task_id ORDER BY submitted_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':task_id', $taskId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy các lần nộp bài (toàn bộ chương) theo Chapter ID
     */
    public function findByChapterId($chapterId) {
        $sql = "SELECT * FROM {$this->table} WHERE chapter_id = :chapter_id ORDER BY submitted_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':chapter_id', $chapterId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy các lần nộp bài của một User (gồm cả Task và Chapter), kèm metadata
     */
    public function findByUserId($userId) {
        $sql = "SELECT s.*, 
                       u.full_name as sender_name,
                       t.title as task_title,
                       COALESCE(c.chapter_number, c_task.chapter_number) as chapter_number,
                       COALESCE(c.title, c_task.title) as chapter_title,
                       COALESCE(ser_chap.title, ser_task.title) as series_title,
                       c.status as chapter_status
                FROM {$this->table} s
                LEFT JOIN users u ON s.user_id = u.user_id
                LEFT JOIN tasks t ON s.task_id = t.task_id
                LEFT JOIN pages p_task ON t.page_id = p_task.page_id
                LEFT JOIN chapters c_task ON p_task.chapter_id = c_task.chapter_id
                LEFT JOIN series ser_task ON c_task.series_id = ser_task.series_id
                LEFT JOIN chapters c ON s.chapter_id = c.chapter_id
                LEFT JOIN series ser_chap ON c.series_id = ser_chap.series_id
                WHERE s.user_id = :user_id
                ORDER BY s.submitted_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách submissions có status = 'pending' cho Editor review, kèm metadata
     */
    public function findPendingSubmissions() {
        $sql = "SELECT s.*, 
                       u.full_name as sender_name,
                       t.title as task_title,
                       COALESCE(c.chapter_number, c_task.chapter_number) as chapter_number,
                       COALESCE(c.title, c_task.title) as chapter_title,
                       COALESCE(ser_chap.title, ser_task.title) as series_title,
                       c.status as chapter_status
                FROM {$this->table} s
                LEFT JOIN users u ON s.user_id = u.user_id
                LEFT JOIN tasks t ON s.task_id = t.task_id
                LEFT JOIN pages p_task ON t.page_id = p_task.page_id
                LEFT JOIN chapters c_task ON p_task.chapter_id = c_task.chapter_id
                LEFT JOIN series ser_task ON c_task.series_id = ser_task.series_id
                LEFT JOIN chapters c ON s.chapter_id = c.chapter_id
                LEFT JOIN series ser_chap ON c.series_id = ser_chap.series_id
                WHERE s.status = 'pending' AND s.chapter_id IS NOT NULL
                ORDER BY s.submitted_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy toàn bộ submissions (cả pending và đã duyệt) cho Editor theo dõi lịch sử
     */
    public function findAllChapterSubmissions() {
        $sql = "SELECT s.*, 
                       u.full_name as sender_name,
                       t.title as task_title,
                       COALESCE(c.chapter_number, c_task.chapter_number) as chapter_number,
                       COALESCE(c.title, c_task.title) as chapter_title,
                       COALESCE(ser_chap.title, ser_task.title) as series_title,
                       c.status as chapter_status
                FROM {$this->table} s
                LEFT JOIN users u ON s.user_id = u.user_id
                LEFT JOIN tasks t ON s.task_id = t.task_id
                LEFT JOIN pages p_task ON t.page_id = p_task.page_id
                LEFT JOIN chapters c_task ON p_task.chapter_id = c_task.chapter_id
                LEFT JOIN series ser_task ON c_task.series_id = ser_task.series_id
                LEFT JOIN chapters c ON s.chapter_id = c.chapter_id
                LEFT JOIN series ser_chap ON c.series_id = ser_chap.series_id
                WHERE s.chapter_id IS NOT NULL
                ORDER BY CASE WHEN s.status = 'pending' THEN 0 ELSE 1 END, s.submitted_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy toàn bộ submissions thuộc về các task của 1 mangaka cụ thể (cả pending và đã duyệt)
     */
    public function findAllTaskSubmissionsByMangakaId($mangakaId) {
        $sql = "SELECT s.*, 
                       u.full_name as sender_name,
                       t.title as task_title,
                       COALESCE(c.chapter_number, c_task.chapter_number) as chapter_number,
                       COALESCE(c.title, c_task.title) as chapter_title,
                       COALESCE(ser_chap.title, ser_task.title) as series_title,
                       c.status as chapter_status
                FROM {$this->table} s
                LEFT JOIN users u ON s.user_id = u.user_id
                LEFT JOIN tasks t ON s.task_id = t.task_id
                LEFT JOIN pages p_task ON t.page_id = p_task.page_id
                LEFT JOIN chapters c_task ON p_task.chapter_id = c_task.chapter_id
                LEFT JOIN series ser_task ON c_task.series_id = ser_task.series_id
                LEFT JOIN chapters c ON s.chapter_id = c.chapter_id
                LEFT JOIN series ser_chap ON c.series_id = ser_chap.series_id
                WHERE t.mangaka_id = :mangaka_id
                ORDER BY CASE WHEN s.status = 'pending' THEN 0 ELSE 1 END, s.submitted_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':mangaka_id', $mangakaId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy chi tiết một submission kèm metadata đầy đủ
     */
    public function findWithMetadataById($id) {
        $sql = "SELECT s.*, 
                       u.full_name as sender_name,
                       t.title as task_title,
                       t.description as task_description,
                       t.task_type as task_type,
                       t.priority as task_priority,
                       t.due_date as task_due_date,
                       COALESCE(c.chapter_number, c_task.chapter_number) as chapter_number,
                       COALESCE(c.title, c_task.title) as chapter_title,
                       COALESCE(ser_chap.title, ser_task.title) as series_title,
                       COALESCE(ser_chap.series_id, ser_task.series_id) as series_id,
                       COALESCE(ser_chap.mangaka_id, t.mangaka_id) as mangaka_id,
                       c.status as chapter_status,
                       p_task.image_url as page_image_url,
                       pr.x as region_x, pr.y as region_y, pr.width as region_width, pr.height as region_height, pr.region_type as region_type
                FROM {$this->table} s
                LEFT JOIN users u ON s.user_id = u.user_id
                LEFT JOIN tasks t ON s.task_id = t.task_id
                LEFT JOIN page_regions pr ON t.page_region_id = pr.region_id
                LEFT JOIN pages p_task ON t.page_id = p_task.page_id
                LEFT JOIN chapters c_task ON p_task.chapter_id = c_task.chapter_id
                LEFT JOIN series ser_task ON c_task.series_id = ser_task.series_id
                LEFT JOIN chapters c ON s.chapter_id = c.chapter_id
                LEFT JOIN series ser_chap ON c.series_id = ser_chap.series_id
                WHERE s.submission_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy toàn bộ submissions (cả pending và đã duyệt) của Editor phụ trách
     */
    public function findAllChapterSubmissionsByEditorId($editorId) {
        $sql = "SELECT s.*, 
                       u.full_name as sender_name,
                       t.title as task_title,
                       COALESCE(c.chapter_number, c_task.chapter_number) as chapter_number,
                       COALESCE(c.title, c_task.title) as chapter_title,
                       COALESCE(ser_chap.title, ser_task.title) as series_title,
                       c.status as chapter_status
                FROM {$this->table} s
                LEFT JOIN users u ON s.user_id = u.user_id
                LEFT JOIN tasks t ON s.task_id = t.task_id
                LEFT JOIN pages p_task ON t.page_id = p_task.page_id
                LEFT JOIN chapters c_task ON p_task.chapter_id = c_task.chapter_id
                LEFT JOIN series ser_task ON c_task.series_id = ser_task.series_id
                LEFT JOIN chapters c ON s.chapter_id = c.chapter_id
                LEFT JOIN series ser_chap ON c.series_id = ser_chap.series_id
                WHERE s.chapter_id IS NOT NULL AND ser_chap.editor_id = :editor_id AND ser_chap.status != 'planning'
                ORDER BY CASE WHEN s.status = 'pending' THEN 0 ELSE 1 END, s.submitted_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':editor_id', $editorId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách submissions có status = 'pending' cho Editor review, kèm metadata (chỉ các bộ truyện được gán)
     */
    public function findPendingSubmissionsByEditorId($editorId) {
        $sql = "SELECT s.*, 
                       u.full_name as sender_name,
                       t.title as task_title,
                       COALESCE(c.chapter_number, c_task.chapter_number) as chapter_number,
                       COALESCE(c.title, c_task.title) as chapter_title,
                       COALESCE(ser_chap.title, ser_task.title) as series_title,
                       c.status as chapter_status
                FROM {$this->table} s
                LEFT JOIN users u ON s.user_id = u.user_id
                LEFT JOIN tasks t ON s.task_id = t.task_id
                LEFT JOIN pages p_task ON t.page_id = p_task.page_id
                LEFT JOIN chapters c_task ON p_task.chapter_id = c_task.chapter_id
                LEFT JOIN series ser_task ON c_task.series_id = ser_task.series_id
                LEFT JOIN chapters c ON s.chapter_id = c.chapter_id
                LEFT JOIN series ser_chap ON c.series_id = ser_chap.series_id
                WHERE s.status = 'pending' AND s.chapter_id IS NOT NULL AND ser_chap.editor_id = :editor_id AND ser_chap.status != 'planning'
                ORDER BY s.submitted_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':editor_id', $editorId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStatusStatistics() {
        $stmt = $this->conn->prepare("SELECT status, COUNT(*) as sub_count FROM submissions GROUP BY status");
        $stmt->execute();
        $raw = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $result = [];
        foreach ($raw as $row) {
            $result[$row['status']] = (int)$row['sub_count'];
        }
        return $result;
    }

    public function countByMangakaId($mangakaId) {
        $sql = "SELECT COUNT(s.submission_id) as total 
                FROM submissions s
                LEFT JOIN tasks t ON s.task_id = t.task_id
                LEFT JOIN chapters c ON s.chapter_id = c.chapter_id
                LEFT JOIN series ser_chap ON c.series_id = ser_chap.series_id
                WHERE t.mangaka_id = :mangaka_id1 OR ser_chap.mangaka_id = :mangaka_id2";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['mangaka_id1' => $mangakaId, 'mangaka_id2' => $mangakaId]);
        return (int)$stmt->fetchColumn();
    }

    public function countPendingByMangakaId($mangakaId) {
        $sql = "SELECT COUNT(s.submission_id) as total 
                FROM submissions s
                JOIN tasks t ON s.task_id = t.task_id
                WHERE s.status = 'pending' AND t.mangaka_id = :mangaka_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['mangaka_id' => $mangakaId]);
        return (int)$stmt->fetchColumn();
    }

    public function countPendingChapterSubmissionsByEditor($editorId) {
        $sql = "SELECT COUNT(sub.submission_id) as total 
                FROM submissions sub
                JOIN chapters c ON sub.chapter_id = c.chapter_id
                JOIN series s ON c.series_id = s.series_id
                WHERE sub.status = 'pending' 
                  AND sub.chapter_id IS NOT NULL 
                  AND s.editor_id = :editor_id
                  AND s.status != 'planning'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['editor_id' => $editorId]);
        return (int)$stmt->fetchColumn();
    }
}
