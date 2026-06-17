<?php
require_once __DIR__ . '/../core/Model.php';

class Task extends Model {
    public function __construct() {
        parent::__construct();
        $this->table = 'tasks';
        $this->primaryKey = 'task_id';
    }

    /**
     * Lấy danh sách task được giao cho một assistant
     */
    public function findByAssistantId($assistantId) {
        $sql = "SELECT * FROM {$this->table} WHERE assistant_id = :assistant_id ORDER BY due_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':assistant_id', $assistantId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách task do một mangaka tạo
     */
    public function findByMangakaId($mangakaId) {
        $sql = "SELECT * FROM {$this->table} WHERE mangaka_id = :mangaka_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':mangaka_id', $mangakaId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
