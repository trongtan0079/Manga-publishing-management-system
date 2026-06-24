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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy các đánh giá của một reviewer
     */
    public function findByReviewerId($reviewerId) {
        $sql = "SELECT r.*, s.task_id, s.chapter_id, s.user_id as submitter_id 
                FROM {$this->table} r
                JOIN submissions s ON r.submission_id = s.submission_id
                WHERE r.reviewer_id = :reviewer_id ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':reviewer_id', $reviewerId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
