<?php
require_once __DIR__ . '/../core/Model.php';

/**
 * Class BoardVote
 * 
 * Model quản lý bỏ phiếu duyệt Series của Hội đồng Biên tập (Editorial Board).
 */
class BoardVote extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'board_votes';
        $this->primaryKey = 'vote_id';
    }

    /**
     * Thực hiện bỏ phiếu hoặc đổi phiếu
     * 
     * @param int $seriesId ID bộ truyện
     * @param int $memberId ID thành viên hội đồng
     * @param string $vote Lựa chọn 'approve' hoặc 'reject'
     * @return bool Thành công hay thất bại
     */
    public function castVote($seriesId, $memberId, $vote) {
        if (!in_array($vote, ['approve', 'reject'])) {
            return false;
        }

        // Kiểm tra xem đã bỏ phiếu chưa
        $sql = "SELECT vote_id FROM {$this->table} WHERE series_id = :series_id AND board_member_id = :board_member_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':series_id' => $seriesId,
            ':board_member_id' => $memberId
        ]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Cập nhật phiếu cũ
            $sqlUp = "UPDATE {$this->table} SET vote = :vote, created_at = CURRENT_TIMESTAMP WHERE vote_id = :vote_id";
            $stmtUp = $this->conn->prepare($sqlUp);
            return $stmtUp->execute([
                ':vote' => $vote,
                ':vote_id' => $existing['vote_id']
            ]);
        } else {
            // Tạo phiếu mới
            return $this->insert([
                'series_id' => $seriesId,
                'board_member_id' => $memberId,
                'vote' => $vote
            ]);
        }
    }

    /**
     * Lấy lựa chọn bỏ phiếu hiện tại của thành viên
     * 
     * @param int $seriesId ID bộ truyện
     * @param int $memberId ID thành viên
     * @return string|null 'approve', 'reject' hoặc null nếu chưa bỏ phiếu
     */
    public function getMemberVote($seriesId, $memberId) {
        $sql = "SELECT vote FROM {$this->table} WHERE series_id = :series_id AND board_member_id = :board_member_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':series_id' => $seriesId,
            ':board_member_id' => $memberId
        ]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ? $res['vote'] : null;
    }

    /**
     * Tính toán số liệu thống kê bỏ phiếu động cho bộ truyện
     * 
     * @param int $seriesId ID bộ truyện
     * @return array Thống kê bỏ phiếu
     */
    public function getApprovalStats($seriesId) {
        // 1. Đếm tổng số thành viên hội đồng có trạng thái active trong hệ thống
        $sqlMembers = "SELECT COUNT(*) as total FROM users u 
                       JOIN roles r ON u.role_id = r.role_id 
                       WHERE r.role_name = 'board' AND u.status = 'active'";
        $stmt = $this->conn->prepare($sqlMembers);
        $stmt->execute();
        $resMembers = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalMembers = $resMembers ? (int)$resMembers['total'] : 1; // Tránh chia cho 0
        if ($totalMembers === 0) {
            $totalMembers = 1;
        }

        // 2. Đếm số phiếu đồng ý (approve)
        $sqlApprove = "SELECT COUNT(*) as total FROM {$this->table} 
                       WHERE series_id = :series_id AND vote = 'approve'";
        $stmt = $this->conn->prepare($sqlApprove);
        $stmt->execute([':series_id' => $seriesId]);
        $resApprove = $stmt->fetch(PDO::FETCH_ASSOC);
        $approveCount = $resApprove ? (int)$resApprove['total'] : 0;

        // 3. Đếm số phiếu từ chối (reject)
        $sqlReject = "SELECT COUNT(*) as total FROM {$this->table} 
                       WHERE series_id = :series_id AND vote = 'reject'";
        $stmt = $this->conn->prepare($sqlReject);
        $stmt->execute([':series_id' => $seriesId]);
        $resReject = $stmt->fetch(PDO::FETCH_ASSOC);
        $rejectCount = $resReject ? (int)$resReject['total'] : 0;

        // 4. Tính toán phần trăm
        $percentage = round(($approveCount / $totalMembers) * 100);

        return [
            'approve_count' => $approveCount,
            'reject_count' => $rejectCount,
            'total_members' => $totalMembers,
            'percentage' => $percentage
        ];
    }
}
