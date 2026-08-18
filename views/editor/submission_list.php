<?php
if (!defined('BASE_PATH')) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $pos = strpos($scriptName, '/views/');
    $projectUrl = ($pos !== false) ? substr($scriptName, 0, $pos) : '';
    header('Location: ' . $projectUrl . '/index.php');
    exit;
}
/**
 * View: Giao diện danh sách lịch sử nộp bản thảo và phê duyệt (submission_list.php)
 * Vai trò: Editor (Biên tập viên) / Mangaka (Họa sĩ chính) / Assistant (Trợ lý)
 * Chức năng: Hiển thị danh sách các bản thảo đã nộp trong dự án cùng với trạng thái phê duyệt và liên kết xem chi tiết/kiểm duyệt.
 * 
 * @var array $submissions Danh sách các bản thảo được truyền từ SubmissionController
 */
$pageTitle = 'Quản lý Bản thảo & Phê duyệt';
$current_page = 'submissions';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';

$role = $_SESSION['role_name'] ?? '';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">
            <?php if ($role === 'editor'): ?>
                Danh sách Bản thảo chờ duyệt
            <?php elseif ($role === 'assistant'): ?>
                Lịch sử nộp bản vẽ của tôi
            <?php else: ?>
                Lịch sử nộp Bản thảo của tôi
            <?php endif; ?>
        </h2>
        <p class="text-muted text-xs mb-0">
            <?php if ($role === 'editor'): ?>
                Xem và kiểm duyệt các chương truyện & bản vẽ cần đánh giá.
            <?php elseif ($role === 'assistant'): ?>
                Theo dõi tiến độ và trạng thái phê duyệt các bản vẽ công việc đã nộp.
            <?php else: ?>
                Theo dõi tiến độ, trạng thái phê duyệt các bản thảo đã nộp.
            <?php endif; ?>
        </p>
    </div>
    
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= BASE_PATH ?>/index.php?controller=dashboard&action=<?= htmlspecialchars($role) ?>" class="btn btn-outline-secondary shadow-sm" style="border-radius: 8px;">
            <i class="fas fa-arrow-left me-2"></i>Quay lại Bảng điều khiển
        </a>
        <?php if ($role === 'assistant' || $role === 'mangaka'): ?>
            <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=create" class="btn btn-primary shadow-sm" style="border-radius: 8px;">
                <i class="fas fa-upload me-2"></i><?= $role === 'assistant' ? 'Nộp bản vẽ mới' : 'Nộp Bản Thảo Mới' ?>
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Thông báo thành công / lỗi -->
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

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white text-dark py-3 border-bottom border-light d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2 text-primary"></i>
            <?= $role === 'assistant' ? 'Danh sách bản vẽ đã nộp' : 'Danh sách bản thảo' ?>
        </h5>
        <span class="badge bg-primary">
            <?= count($submissions) ?> <?= $role === 'assistant' ? 'Bản vẽ' : 'Bản ghi' ?>
        </span>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($submissions)): ?>
            <!-- Bảng danh sách bản thảo -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <?php if ($role !== 'assistant'): ?>
                                <th class="ps-4">Người gửi</th>
                                <th>Loại Submission</th>
                            <?php endif; ?>
                            <th class="<?= $role === 'assistant' ? 'ps-4' : '' ?>">Mục tiêu (Task / Chapter)</th>
                            <th>Series</th>
                            <th>Ngày nộp</th>
                            <th>Trạng thái</th>
                            <th class="text-end pe-4" style="width: 180px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $sub): ?>
                            <tr>
                                <?php if ($role !== 'assistant'): ?>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light text-dark rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-weight: bold;">
                                                <?= strtoupper(substr($sub['sender_name'] ?? 'U', 0, 1)) ?>
                                            </div>
                                            <div>
                                                <span class="fw-bold text-dark"><?= htmlspecialchars($sub['sender_name'] ?? 'Không rõ') ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($sub['task_id'])): ?>
                                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">Task Drawing</span>
                                        <?php else: ?>
                                            <?php if (isset($sub['chapter_status']) && ($sub['chapter_status'] === 'reviewing_final' || $sub['chapter_status'] === 'reviewing')): ?>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">Bản vẽ hoàn thiện (Manuscript)</span>
                                            <?php elseif (isset($sub['chapter_status']) && $sub['chapter_status'] === 'reviewing_draft'): ?>
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">Kịch bản thô (Storyboard)</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">Chương truyện</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                                <td class="<?= $role === 'assistant' ? 'ps-4' : '' ?>">
                                    <?php if (!empty($sub['task_id'])): ?>
                                        <div class="text-dark">
                                            <i class="fas fa-tasks text-muted me-1"></i>
                                            <?= htmlspecialchars((string)($sub['task_title'] ?? ('Task #' . ($sub['task_id'] ?? '')))) ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-dark">
                                            <i class="fas fa-layer-group text-muted me-1"></i>
                                            Ch.<?= htmlspecialchars((string)($sub['chapter_number'] ?? '')) ?> - <?= htmlspecialchars((string)($sub['chapter_title'] ?? 'Chưa đặt tên')) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="text-muted"><?= htmlspecialchars($sub['series_title'] ?? 'N/A') ?></span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= htmlspecialchars(date('d/m/Y H:i', strtotime($sub['submitted_at']))) ?>
                                    </small>
                                </td>
                                <td>
                                    <?php 
                                    $statusClass = 'bg-secondary';
                                    $statusLabel = 'Chờ duyệt';
                                    if ($sub['status'] === 'reviewed') {
                                        $statusClass = 'bg-info';
                                        $statusLabel = 'Đang đánh giá';
                                    } elseif ($sub['status'] === 'approved') {
                                        $statusClass = 'bg-success';
                                        $statusLabel = 'Đã duyệt';
                                    } elseif ($sub['status'] === 'rejected') {
                                        $statusClass = 'bg-danger';
                                        $statusLabel = 'Từ chối';
                                    } elseif ($sub['status'] === 'pending') {
                                        $statusClass = 'bg-warning text-dark';
                                        $statusLabel = 'Chờ duyệt';
                                    }
                                    ?>
                                    <span class="badge <?= $statusClass ?> px-2 py-1 status-badge" data-status="<?= htmlspecialchars($sub['status']) ?>"><?= $statusLabel ?></span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <!-- Các nút hành động: Xem chi tiết và Xóa (nếu thỏa điều kiện) -->
                                        <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=show&id=<?= $sub['submission_id'] ?>" class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </a>
                                        
                                        <?php if (($role === 'assistant' || $role === 'mangaka') && $sub['status'] === 'pending' && $sub['user_id'] == $_SESSION['user_id']): ?>
                                            <form action="<?= BASE_PATH ?>/index.php?controller=submission&action=delete&id=<?= $sub['submission_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bản thảo này?');">
                                                <?= Csrf::field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa bản thảo">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <div class="mb-3 text-muted">
                    <i class="fas fa-inbox fa-3x"></i>
                </div>
                <p class="text-muted mb-0">
                    <?= $role === 'assistant' ? 'Chưa có bản vẽ nào được ghi nhận.' : 'Chưa có bản thảo nào được ghi nhận.' ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const filterStatus = urlParams.get('status');
    if (filterStatus) {
        const rows = document.querySelectorAll("tbody tr");
        let foundCount = 0;
        rows.forEach(row => {
            const badge = row.querySelector("td span.status-badge");
            if (badge) {
                const statusAttr = badge.getAttribute("data-status");
                let isMatch = false;
                if (filterStatus.toLowerCase() === 'reviewed') {
                    isMatch = (statusAttr && (statusAttr.toLowerCase() === 'approved' || statusAttr.toLowerCase() === 'rejected'));
                } else {
                    isMatch = (statusAttr && statusAttr.toLowerCase() === filterStatus.toLowerCase());
                }
                if (isMatch) {
                    row.style.display = "";
                    foundCount++;
                } else {
                    row.style.display = "none";
                }
            }
        });
        
        const headerBadge = document.querySelector(".card-header span.badge");
        if (headerBadge) {
            headerBadge.textContent = foundCount + " Bản ghi (đã lọc)";
            headerBadge.className = "badge bg-info";
        }
        
        const cardHeader = document.querySelector(".card-header h5");
        if (cardHeader) {
            const clearBtn = document.createElement("a");
            clearBtn.href = window.location.pathname + "?controller=submission&action=index";
            clearBtn.className = "btn btn-sm btn-outline-secondary ms-3 py-1 px-2";
            clearBtn.style.fontSize = "0.75rem";
            clearBtn.innerHTML = "<i class='fas fa-times me-1'></i>Xóa bộ lọc";
            cardHeader.appendChild(clearBtn);
        }
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
