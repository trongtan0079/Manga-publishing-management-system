<?php
require_once __DIR__ . '/../core/Model.php';

/**
 * Class SubmissionAnnotation
 * 
 * Model quản lý các ghi chú sửa đổi trực quan của Tác giả (Mangaka) trên bản nộp của Trợ lý.
 */
class SubmissionAnnotation extends Model {
    /**
     * Khởi tạo Model, thiết lập bảng đích trong CSDL.
     */
    public function __construct() {
        parent::__construct();
        $this->table = 'submission_annotations';
        $this->primaryKey = 'annotation_id';
    }

    /**
     * Lấy danh sách ghi chú lỗi trên 1 bản thảo nộp kèm tên đầy đủ của người đánh giá.
     * 
     * @param int $submissionId ID của bản thảo nộp
     * @return array Danh sách các ghi chú lỗi
     */
    public function findBySubmissionId($submissionId) {
        $sql = "SELECT sa.*, u.full_name as user_name 
                FROM {$this->table} sa
                JOIN users u ON sa.user_id = u.user_id
                WHERE sa.submission_id = :submission_id 
                ORDER BY sa.annotation_id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':submission_id', $submissionId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
