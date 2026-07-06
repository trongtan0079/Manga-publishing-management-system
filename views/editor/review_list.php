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
 * @var array $submissions Danh sách các bản thảo chờ duyệt hoặc đã đánh giá liên quan đến người dùng hiện tại
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

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">
        <!-- Bảng danh sách các bản thảo đang chờ duyệt -->
        <?php if (!empty($submissions)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Submission ID</th>
                            <th>Người gửi</th>
                            <?php if ($role !== 'mangaka'): ?>
                                <th>Loại</th>
                            <?php endif; ?>
                            <th>Series</th>
                            <th>Mục tiêu (Task/Chapter)</th>
                            <th>Ngày nộp</th>
                            <th>Trạng thái</th>
                            <th class="text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $sub): ?>
                            <tr>
                                <td class="ps-4 fw-bold">#<?= $sub['submission_id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light text-dark rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-weight: bold;">
                                            <?= strtoupper(substr($sub['sender_name'] ?? 'U', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark"><?= htmlspecialchars($sub['sender_name'] ?? 'Không rõ') ?></span>
                                        </div>
                                    </div>
                                </td>
                                <?php if ($role !== 'mangaka'): ?>
                                    <td>
                                        <?php if ($sub['task_id'] !== null): ?>
                                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">Task</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">Chapter</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                                <td><?= htmlspecialchars($sub['series_title'] ?? 'N/A') ?></td>
                                <td>
                                    <?php if ($sub['task_id'] !== null): ?>
                                        <?= htmlspecialchars((string)($sub['task_title'] ?? ('Task #' . $sub['task_id']))) ?>
                                    <?php else: ?>
                                        Ch.<?= htmlspecialchars((string)($sub['chapter_number'] ?? '')) ?> - <?= htmlspecialchars((string)($sub['chapter_title'] ?? '')) ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($sub['submitted_at'])) ?></td>
                                <td>
                                     <?php 
                                         $statusClass = 'secondary';
                                         $statusText = 'Chờ duyệt';
                                         if ($sub['status'] === 'approved') { $statusClass = 'success'; $statusText = 'Đã duyệt'; }
                                         elseif ($sub['status'] === 'rejected') { $statusClass = 'danger'; $statusText = 'Từ chối'; }
                                         elseif ($sub['status'] === 'reviewed') { $statusClass = 'primary'; $statusText = 'Đang đánh giá'; }
                                         elseif ($sub['status'] === 'pending') { $statusClass = 'warning text-dark'; $statusText = 'Chờ duyệt'; }
                                     ?>
                                     <span class="badge bg-<?= $statusClass ?> px-2 py-1 status-badge" data-status="<?= htmlspecialchars($sub['status']) ?>"><?= $statusText ?></span>
                                </td>
                                <td class="text-end pe-4">
                                    <?php if ($sub['status'] === 'pending' || $sub['status'] === 'reviewed'): ?>
                                        <a href="<?= BASE_PATH ?>/index.php?controller=review&action=create&submission_id=<?= $sub['submission_id'] ?>" class="btn btn-sm btn-primary shadow-sm" style="border-radius: 6px;">
                                            <i class="fas fa-edit me-1"></i> Review
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=show&id=<?= $sub['submission_id'] ?>" class="btn btn-sm btn-outline-secondary shadow-sm" style="border-radius: 6px;">
                                            <i class="fas fa-eye me-1"></i> Xem chi tiết
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-check-double fa-3x mb-3 text-success"></i>
                <p class="mb-0 fs-5">
                    <?= $role === 'mangaka' ? 'Tất cả bản vẽ của Trợ lý đã được duyệt!' : 'Tất cả bản thảo đã được duyệt!' ?>
                </p>
                <p class="small text-muted mt-1">
                    <?= $role === 'mangaka' ? 'Hiện tại không có bản vẽ nào từ trợ lý đang chờ bạn đánh giá.' : 'Không có bản thảo nào đang chờ duyệt.' ?>
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
        
        const heading = document.querySelector("h2.h3");
        if (heading) {
            const clearBtn = document.createElement("a");
            clearBtn.href = window.location.pathname + "?controller=review&action=index";
            clearBtn.className = "btn btn-sm btn-outline-secondary ms-3 py-1 px-2";
            clearBtn.style.fontSize = "0.75rem";
            clearBtn.style.verticalAlign = "middle";
            clearBtn.innerHTML = "<i class='fas fa-times me-1'></i>Xóa bộ lọc";
            heading.appendChild(clearBtn);
        }
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
