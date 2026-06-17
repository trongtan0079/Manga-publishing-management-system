<?php
require_once __DIR__ . '/../core/Model.php';

class Review extends Model {
    public function __construct() {
        parent::__construct();
        $this->table = 'reviews';
        $this->primaryKey = 'review_id';
    }

    /**
     * Lấy các đánh giá của một submission
     */
    public function findBySubmissionId($submissionId) {
        $sql = "SELECT * FROM {$this->table} WHERE submission_id = :submission_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':submission_id', $submissionId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
