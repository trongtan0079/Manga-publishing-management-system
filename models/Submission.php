<?php
require_once __DIR__ . '/../core/Model.php';

class Submission extends Model {
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
                       COALESCE(ser_chap.title, ser_task.title) as series_title
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
                       COALESCE(ser_chap.title, ser_task.title) as series_title
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
     * Lấy danh sách submissions pending thuộc về các task của 1 mangaka cụ thể
     */
    public function findPendingSubmissionsByMangakaId($mangakaId) {
        $sql = "SELECT s.*, 
                       u.full_name as sender_name,
                       t.title as task_title,
                       COALESCE(c.chapter_number, c_task.chapter_number) as chapter_number,
                       COALESCE(c.title, c_task.title) as chapter_title,
                       COALESCE(ser_chap.title, ser_task.title) as series_title
                FROM {$this->table} s
                LEFT JOIN users u ON s.user_id = u.user_id
                LEFT JOIN tasks t ON s.task_id = t.task_id
                LEFT JOIN pages p_task ON t.page_id = p_task.page_id
                LEFT JOIN chapters c_task ON p_task.chapter_id = c_task.chapter_id
                LEFT JOIN series ser_task ON c_task.series_id = ser_task.series_id
                LEFT JOIN chapters c ON s.chapter_id = c.chapter_id
                LEFT JOIN series ser_chap ON c.series_id = ser_chap.series_id
                WHERE s.status = 'pending' AND t.mangaka_id = :mangaka_id
                ORDER BY s.submitted_at DESC";
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
                       COALESCE(c.chapter_number, c_task.chapter_number) as chapter_number,
                       COALESCE(c.title, c_task.title) as chapter_title,
                       COALESCE(ser_chap.title, ser_task.title) as series_title,
                       COALESCE(ser_chap.mangaka_id, t.mangaka_id) as mangaka_id
                FROM {$this->table} s
                LEFT JOIN users u ON s.user_id = u.user_id
                LEFT JOIN tasks t ON s.task_id = t.task_id
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
}
