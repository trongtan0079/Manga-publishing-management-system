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
        return $stmt->fetchAll();
    }

    /**
     * Lấy các lần nộp bài (toàn bộ chương) theo Chapter ID
     */
    public function findByChapterId($chapterId) {
        $sql = "SELECT * FROM {$this->table} WHERE chapter_id = :chapter_id ORDER BY submitted_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':chapter_id', $chapterId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
