<?php
require_once __DIR__ . '/../core/Model.php';

/**
 * Class EditorAnnotation
 * 
 * Model quản lý các ghi chú báo lỗi trực quan của Editor trên các trang truyện.
 */
class EditorAnnotation extends Model {
    /**
     * Khởi tạo Model, thiết lập bảng đích trong CSDL.
     */
    public function __construct() {
        parent::__construct();
        $this->table = 'editor_annotations';
        $this->primaryKey = 'annotation_id';
    }

    /**
     * Lấy danh sách ghi chú lỗi của Editor trên 1 trang truyện kèm tên đầy đủ của Editor.
     * 
     * @param int $pageId ID của trang truyện
     * @return array Danh sách các ghi chú lỗi
     */
    public function findByPageId($pageId) {
        $sql = "SELECT ea.*, u.full_name as editor_name 
                FROM {$this->table} ea
                JOIN users u ON ea.editor_id = u.user_id
                WHERE ea.page_id = :page_id 
                ORDER BY ea.annotation_id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':page_id', $pageId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Xóa tất cả các ghi chú lỗi của 1 trang truyện.
     * 
     * @param int $pageId ID của trang truyện
     * @return bool Trả về true nếu thành công
     */
    public function deleteByPageId($pageId) {
        $sql = "DELETE FROM {$this->table} WHERE page_id = :page_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':page_id', $pageId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
