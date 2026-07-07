<?php
if (!defined('BASE_PATH')) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $pos = strpos($scriptName, '/views/');
    $projectUrl = ($pos !== false) ? substr($scriptName, 0, $pos) : '';
    header('Location: ' . $projectUrl . '/index.php');
    exit;
}
/**
 * View: Giao diện danh sách bản thảo chờ đánh giá và lịch sử đánh giá (review_list.php)
 * Vai trò: Editor (Biên tập viên) / Mangaka (Họa sĩ chính)
 * Chức năng: Hiển thị các chương truyện hoặc sản phẩm nhiệm vụ của trợ lý đang chờ đánh giá kèm theo lịch sử.
 * 
 * @var array $pendingSubmissions Danh sách các bản thảo chờ duyệt
 * @var array $reviewedSubmissions Danh sách các bản thảo đã duyệt
 */
$pageTitle = 'Quản lý Đánh giá Bản thảo';
$current_page = 'reviews';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<?php
$role = $_SESSION['role_name'] ?? '';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">
            <?= $role === 'mangaka' ? 'Duyệt sản phẩm vẽ của Trợ lý' : 'Danh sách Bản thảo chờ duyệt' ?>
        </h2>
        <p class="text-muted text-xs mb-0">
            <?= $role === 'mangaka' ? 'Xem và đánh giá các sản phẩm hoàn thành từ trợ lý của bạn.' : 'Xem và đánh giá các bản thảo được nộp.' ?>
        </p>
    </div>
    <div>
        <a href="<?= BASE_PATH ?>/index.php?controller=dashboard&action=<?= htmlspecialchars($role) ?>" class="btn btn-outline-secondary shadow-sm" style="border-radius: 8px;">
            <i class="fas fa-arrow-left me-2"></i>Quay lại Bảng điều khiển
        </a>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php
$renderTable = function($list, $role, $emptyMsg) {
    if (empty($list)) {
        return '<div class="text-center py-5 text-muted">
                    <i class="fas fa-check-double fa-3x mb-3 text-success" style="opacity: 0.5;"></i>
                    <p class="mb-0 fs-5">' . $emptyMsg . '</p>
                </div>';
    }
    
    $html = '<div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr>';
    $html .= '<th class="ps-4">Submission ID</th><th>Người gửi</th>';
    if ($role !== 'mangaka') {
        $html .= '<th>Loại</th>';
    }
    $html .= '<th>Series</th><th>Mục tiêu (Task/Chapter)</th><th>Ngày nộp</th><th>Trạng thái</th><th class="text-end pe-4">Hành động</th></tr></thead><tbody>';
    
    foreach ($list as $sub) {
        $senderInit = strtoupper(substr($sub['sender_name'] ?? 'U', 0, 1));
        $senderName = htmlspecialchars($sub['sender_name'] ?? 'Không rõ');
        $seriesTitle = htmlspecialchars($sub['series_title'] ?? 'N/A');
        
        $typeHtml = '';
        if ($role !== 'mangaka') {
            if ($sub['task_id'] !== null) {
                $typeHtml = '<td><span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">Task</span></td>';
            } else {
                $typeHtml = '<td><span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">Chapter</span></td>';
            }
        }
        
        $targetHtml = '';
        if ($sub['task_id'] !== null) {
            $targetHtml = htmlspecialchars((string)($sub['task_title'] ?? ('Task #' . $sub['task_id'])));
        } else {
            $targetHtml = 'Ch.' . htmlspecialchars((string)($sub['chapter_number'] ?? '')) . ' - ' . htmlspecialchars((string)($sub['chapter_title'] ?? ''));
        }
        
        $dateHtml = date('d/m/Y H:i', strtotime($sub['submitted_at']));
        
        $statusClass = 'secondary';
        $statusText = 'Chờ duyệt';
        if ($sub['status'] === 'approved') { $statusClass = 'success'; $statusText = 'Đã duyệt'; }
        elseif ($sub['status'] === 'rejected') { $statusClass = 'danger'; $statusText = 'Từ chối'; }
        elseif ($sub['status'] === 'reviewed') { $statusClass = 'primary'; $statusText = 'Đang đánh giá'; }
        elseif ($sub['status'] === 'pending') { $statusClass = 'warning text-dark'; $statusText = 'Chờ duyệt'; }
        
        $actionHtml = '';
        if ($sub['status'] === 'pending' || $sub['status'] === 'reviewed') {
            $actionHtml = '<a href="' . BASE_PATH . '/index.php?controller=review&action=create&submission_id=' . $sub['submission_id'] . '" class="btn btn-sm btn-primary shadow-sm" style="border-radius: 6px;"><i class="fas fa-edit me-1"></i> Review</a>';
        } else {
            $actionHtml = '<a href="' . BASE_PATH . '/index.php?controller=submission&action=show&id=' . $sub['submission_id'] . '" class="btn btn-sm btn-outline-secondary shadow-sm" style="border-radius: 6px;"><i class="fas fa-eye me-1"></i> Xem chi tiết</a>';
        }
        
        $html .= '<tr>';
        $html .= '<td class="ps-4 fw-bold">#' . $sub['submission_id'] . '</td>';
        $html .= '<td><div class="d-flex align-items-center"><div class="avatar-sm bg-light text-dark rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-weight: bold;">' . $senderInit . '</div><div><span class="fw-bold text-dark">' . $senderName . '</span></div></div></td>';
        $html .= $typeHtml;
        $html .= '<td>' . $seriesTitle . '</td>';
        $html .= '<td>' . $targetHtml . '</td>';
        $html .= '<td>' . $dateHtml . '</td>';
        $html .= '<td><span class="badge bg-' . $statusClass . ' px-2 py-1">' . $statusText . '</span></td>';
        $html .= '<td class="text-end pe-4">' . $actionHtml . '</td>';
        $html .= '</tr>';
    }
    $html .= '</tbody></table></div>';
    return $html;
};
?>

<h5 class="fw-bold text-primary mb-3"><i class="fas fa-clock me-2"></i>Đang chờ duyệt</h5>
<div class="card shadow-sm border-0 rounded-3 mb-5 border-top border-primary border-3">
    <div class="card-body p-0">
        <?= $renderTable($pendingSubmissions ?? [], $role, $role === 'mangaka' ? 'Hiện tại không có bản vẽ nào từ trợ lý đang chờ bạn đánh giá.' : 'Không có bản thảo nào đang chờ duyệt.') ?>
    </div>
</div>

<h5 class="fw-bold text-success mb-3"><i class="fas fa-history me-2"></i>Lịch sử đã duyệt</h5>
<div class="card shadow-sm border-0 rounded-3 mb-5 border-top border-success border-3">
    <div class="card-body p-0">
        <?= $renderTable($reviewedSubmissions ?? [], $role, 'Chưa có bản thảo nào được đánh giá.') ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
