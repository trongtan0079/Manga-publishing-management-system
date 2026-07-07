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
        $sql = "SELECT t.*, p.page_number, p.image_url, c.chapter_number, s.title as series_title, u.full_name as mangaka_name,
                       r.x as region_x, r.y as region_y, r.width as region_width, r.height as region_height, r.region_type
                FROM {$this->table} t
                JOIN pages p ON t.page_id = p.page_id
                JOIN chapters c ON p.chapter_id = c.chapter_id
                JOIN series s ON c.series_id = s.series_id
                JOIN users u ON t.mangaka_id = u.user_id
                LEFT JOIN page_regions r ON t.page_region_id = r.region_id
                WHERE t.assistant_id = :assistant_id AND c.status != 'drafting' AND s.status != 'planning' AND p.status != 'drafting'
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
        $sql = "SELECT t.*, p.page_number, p.image_url, c.chapter_number, s.title as series_title, u.full_name as mangaka_name,
                       r.x as region_x, r.y as region_y, r.width as region_width, r.height as region_height, r.region_type
                FROM {$this->table} t
                JOIN pages p ON t.page_id = p.page_id
                JOIN chapters c ON p.chapter_id = c.chapter_id
                JOIN series s ON c.series_id = s.series_id
                JOIN users u ON t.mangaka_id = u.user_id
                LEFT JOIN page_regions r ON t.page_region_id = r.region_id
                WHERE t.assistant_id = :assistant_id AND t.status != 'completed' AND c.status != 'drafting' AND s.status != 'planning' AND p.status != 'drafting'
                ORDER BY t.due_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':assistant_id', $assistantId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách công việc do Mangaka cụ thể giao cho trợ lý,
     * bao gồm thông tin chi tiết về trợ lý, trang, chương và bộ truyện.
     */
    public function findByMangakaId($mangakaId, $limit = null) {
        $sql = "SELECT t.*, u.full_name as assistant_name, p.page_number, c.chapter_number, s.title as series_title, s.series_id
                FROM {$this->table} t
                LEFT JOIN users u ON t.assistant_id = u.user_id
                JOIN pages p ON t.page_id = p.page_id
                JOIN chapters c ON p.chapter_id = c.chapter_id
                JOIN series s ON c.series_id = s.series_id
                WHERE t.mangaka_id = :mangaka_id
                ORDER BY t.created_at DESC";
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':mangaka_id', $mangakaId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy toàn bộ danh sách task thuộc về một Chapter cụ thể.
     * Dùng để gửi thông báo hàng loạt khi chapter chuyển từ drafting sang drawing.
     */
    public function findTasksByChapterId($chapterId) {
        $sql = "SELECT t.*, c.chapter_number, s.title as series_title, p.status as page_status
                FROM {$this->table} t
                JOIN pages p ON t.page_id = p.page_id
                JOIN chapters c ON p.chapter_id = c.chapter_id
                JOIN series s ON c.series_id = s.series_id
                WHERE c.chapter_id = :chapter_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':chapter_id', $chapterId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStatusStatistics() {
        $stmt = $this->conn->prepare("SELECT status, COUNT(*) as task_count FROM tasks GROUP BY status");
        $stmt->execute();
        $raw = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $result = [];
        foreach ($raw as $row) {
            $result[$row['status']] = (int)$row['task_count'];
        }
        return $result;
    }

    public function countByPageAndAssistant($pageId, $assistantId) {
        $sql = "SELECT COUNT(*) FROM tasks WHERE page_id = :page_id AND assistant_id = :assistant_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['page_id' => $pageId, 'assistant_id' => $assistantId]);
        return (int)$stmt->fetchColumn();
    }

    public function getMonthlyIncomeStats($assistantId) {
        $sql = "SELECT 
                    DATE_FORMAT(updated_at, '%m/%Y') as period,
                    COUNT(DISTINCT page_id) as approved_pages_count,
                    COUNT(task_id) as completed_tasks_count,
                    COUNT(task_id) * 300000 as estimated_income
                FROM tasks
                WHERE assistant_id = :assistant_id AND status = 'completed'
                GROUP BY DATE_FORMAT(updated_at, '%m/%Y')
                ORDER BY MIN(updated_at) DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['assistant_id' => $assistantId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
