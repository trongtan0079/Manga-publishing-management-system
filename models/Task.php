<?php
require_once __DIR__ . '/../core/Model.php';

class Task extends Model {
    
    /**
     * Khởi tạo Model Task
     * Thiết lập tên bảng 'tasks' và khóa chính 'task_id' để kế thừa các hàm CRUD cơ bản từ Model cha
     */
    public function __construct() {
        parent::__construct();
        $this->table = 'tasks';
        $this->primaryKey = 'task_id';
    }

    /**
     * Lấy toàn bộ danh sách task đang được giao trên một trang truyện cụ thể (Page).
     * Hàm này được sử dụng chủ yếu bởi Mangaka trong trang chi tiết Page.
     * Sử dụng LEFT JOIN với bảng users để lấy ra tên của Assistant (full_name) thay vì chỉ lấy ID.
     * 
     * @param int $pageId ID của trang truyện
     * @return array Danh sách các công việc thuộc về trang đó, sắp xếp mới nhất lên đầu
     */
    public function findByPageId($pageId) {
        $sql = "SELECT t.*, u.full_name as assistant_name
                FROM {$this->table} t
                LEFT JOIN users u ON t.assistant_id = u.user_id
                WHERE t.page_id = :page_id 
                ORDER BY t.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':page_id', $pageId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy tất cả task đang được giao cho một Assistant cụ thể.
     * Hàm này phục vụ cho trang "My Tasks Dashboard" của Assistant.
     * 
     * Nó thực hiện JOIN qua nhiều bảng liên kết:
     * - pages: Lấy số trang (page_number)
     * - chapters: Lấy số chương (chapter_number)
     * - series: Lấy tên bộ truyện (series_title)
     * - users: Lấy tên người giao việc (mangaka_name)
     * Qua đó cung cấp đầy đủ ngữ cảnh (Context) để Assistant biết mình đang làm việc ở đâu.
     * 
     * @param int $assistantId ID của người dùng có role Assistant
     * @return array Danh sách công việc, ưu tiên sắp xếp theo hạn chót (due_date) tăng dần (sắp đến hạn thì hiện trước)
     */
    public function findByAssistantId($assistantId) {
        $sql = "SELECT t.*, c.chapter_number, s.title as series_title, u.full_name as mangaka_name
                FROM {$this->table} t
                JOIN chapters c ON t.chapter_id = c.chapter_id
                JOIN series s ON c.series_id = s.series_id
                JOIN users u ON t.mangaka_id = u.user_id
                WHERE t.assistant_id = :assistant_id 
                ORDER BY t.due_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':assistant_id', $assistantId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy các task chưa hoàn thành của Assistant
     */
    public function findActiveByAssistantId($assistantId) {
        $sql = "SELECT t.*, c.chapter_number, s.title as series_title, u.full_name as mangaka_name
                FROM {$this->table} t
                JOIN chapters c ON t.chapter_id = c.chapter_id
                JOIN series s ON c.series_id = s.series_id
                JOIN users u ON t.mangaka_id = u.user_id
                WHERE t.assistant_id = :assistant_id AND t.status != 'completed'
                ORDER BY t.due_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':assistant_id', $assistantId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
