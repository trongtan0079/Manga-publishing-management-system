<?php
require_once __DIR__ . '/../core/Model.php';

class Series extends Model {
    public function __construct() {
        parent::__construct();
        $this->table = 'series';
        $this->primaryKey = 'series_id';
    }

    /**
     * Lấy danh sách series theo ID của mangaka
     */
    public function findByMangakaId($mangakaId) {
        $sql = "SELECT * FROM {$this->table} WHERE mangaka_id = :mangaka_id ORDER BY series_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':mangaka_id', $mangakaId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách series theo trạng thái
     */
    public function findByStatus($status) {
        $sql = "SELECT * FROM {$this->table} WHERE status = :status ORDER BY series_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách series cho Editor (bộ truyện đang theo dõi)
     */
    public function getSeriesByEditorId($editorId) {
        $sql = "SELECT * FROM {$this->table} WHERE editor_id = :editor_id AND status != 'planning' ORDER BY series_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':editor_id' => $editorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách series cho Editorial Board (để publish) kèm theo số liệu thống kê
     */
    public function getSeriesForPublishing() {
        $sql = "SELECT s.*, u.full_name as mangaka_name, ed.full_name as editor_name,
                (SELECT COUNT(*) FROM chapters WHERE series_id = s.series_id) as total_chapters,
                (SELECT COUNT(*) FROM chapters WHERE series_id = s.series_id AND status IN ('approved', 'published')) as finished_chapters,
                (SELECT COUNT(*) FROM chapters WHERE series_id = s.series_id AND is_final = 1 AND status IN ('approved', 'published')) as has_final_approved,
                (SELECT rank_position FROM series_rankings WHERE series_id = s.series_id ORDER BY period_start_date DESC, ranking_id DESC LIMIT 1) as latest_rank,
                (SELECT score FROM series_rankings WHERE series_id = s.series_id ORDER BY period_start_date DESC, ranking_id DESC LIMIT 1) as latest_score
                FROM {$this->table} s 
                JOIN users u ON s.mangaka_id = u.user_id 
                LEFT JOIN users ed ON s.editor_id = ed.user_id
                WHERE s.publish_type != 'draft'
                ORDER BY s.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách series (hồ sơ) theo Editor kèm theo số liệu
     */
    public function getDossiersByEditorId($editorId) {
        $sql = "SELECT s.*, u.full_name as mangaka_name,
                (SELECT COUNT(*) FROM chapters WHERE series_id = s.series_id) as total_chapters,
                (SELECT COUNT(*) FROM chapters WHERE series_id = s.series_id AND status IN ('approved', 'published')) as finished_chapters
                FROM {$this->table} s
                JOIN users u ON s.mangaka_id = u.user_id
                WHERE s.editor_id = :editor_id AND s.status != 'planning'
                ORDER BY s.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':editor_id' => $editorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countActiveSeries() {
        $sql = "SELECT COUNT(*) FROM series WHERE status IN ('ongoing', 'completed', 'suspended')";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /**
     * Kiểm tra xem Tiêu đề bộ truyện đã tồn tại chưa (không phân biệt hoa thường)
     */
    public function isTitleExists($title, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE LOWER(title) = LOWER(:title)";
        if ($excludeId !== null) {
            $sql .= " AND series_id != :exclude_id";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':title', $title);
        if ($excludeId !== null) {
            $stmt->bindParam(':exclude_id', $excludeId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Lấy danh sách series phân trang, tìm kiếm và lọc theo trạng thái theo phân quyền vai trò
     */
    public function getPaginatedSeries($role, $userId, $search = '', $status = '', $limit = 10, $offset = 0) {
        $whereClauses = [];
        $params = [];

        // 1. Phân quyền dữ liệu theo vai trò (Role-based access)
        if ($role === 'mangaka') {
            $whereClauses[] = "s.mangaka_id = :role_user_id";
            $params['role_user_id'] = $userId;
        } elseif ($role === 'editor') {
            $whereClauses[] = "s.editor_id = :role_user_id AND s.status != 'planning'";
            $params['role_user_id'] = $userId;
        } elseif ($role === 'board' || $role === 'admin') {
            $whereClauses[] = "s.publish_type != 'draft'";
        } else {
            return ['series' => [], 'total' => 0];
        }

        // 2. Lọc theo từ khóa tìm kiếm (title, description)
        if (!empty($search)) {
            $whereClauses[] = "(s.title LIKE :search OR s.description LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        // 3. Lọc theo trạng thái hợp lệ
        $allowedStatuses = ['planning', 'ongoing', 'completed', 'canceled', 'suspended'];
        if (!empty($status) && in_array($status, $allowedStatuses)) {
            $whereClauses[] = "s.status = :status";
            $params['status'] = $status;
        }

        $whereSql = "";
        if (!empty($whereClauses)) {
            $whereSql = "WHERE " . implode(" AND ", $whereClauses);
        }

        // 4. Đếm tổng số bản ghi khớp điều kiện
        $countSql = "SELECT COUNT(*) FROM {$this->table} s {$whereSql}";
        $countStmt = $this->conn->prepare($countSql);
        foreach ($params as $key => $val) {
            $countStmt->bindValue(':' . $key, $val);
        }
        $countStmt->execute();
        $total = (int)$countStmt->fetchColumn();

        // 5. Lấy danh sách bản ghi theo trang
        $dataSql = "SELECT s.*, u.full_name as mangaka_name, ed.full_name as editor_name 
                    FROM {$this->table} s 
                    LEFT JOIN users u ON s.mangaka_id = u.user_id 
                    LEFT JOIN users ed ON s.editor_id = ed.user_id 
                    {$whereSql} 
                    ORDER BY s.series_id DESC 
                    LIMIT :limit OFFSET :offset";
        $dataStmt = $this->conn->prepare($dataSql);
        foreach ($params as $key => $val) {
            $dataStmt->bindValue(':' . $key, $val);
        }
        $dataStmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $dataStmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $dataStmt->execute();
        $series = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'series' => $series,
            'total'  => $total
        ];
    }
}

