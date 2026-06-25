<?php
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
            <?php else: ?>
                Lịch sử nộp Bản thảo của tôi
            <?php endif; ?>
        </h2>
        <p class="text-muted text-xs mb-0">
            <?php if ($role === 'editor'): ?>
                Xem và kiểm duyệt các chương truyện & bản vẽ cần đánh giá.
            <?php else: ?>
                Theo dõi tiến độ, trạng thái phê duyệt các bản thảo đã nộp.
            <?php endif; ?>
        </p>
    </div>
    
    <?php if ($role === 'assistant' || $role === 'mangaka'): ?>
        <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=create" class="btn btn-primary shadow-sm">
            <i class="fas fa-upload me-2"></i>Nộp Bản Thảo Mới
        </a>
    <?php endif; ?>
</div>

<!-- Thông báo thành công / lỗi -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white text-dark py-3 border-bottom border-light d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0"><i class="fas fa-list me-2 text-primary"></i>Danh sách bản thảo</h5>
        <span class="badge bg-primary"><?= count($submissions) ?> Bản ghi</span>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($submissions)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Người gửi</th>
                            <th>Loại Submission</th>
                            <th>Mục tiêu (Task / Chapter)</th>
                            <th>Series</th>
                            <th>Ngày nộp</th>
                            <th>Trạng thái</th>
                            <th class="text-end pe-4" style="width: 180px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $sub): ?>
                            <tr>
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
                                    <?php if ($sub['task_id'] !== null): ?>
                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1">Task Drawing</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1">Full Chapter</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($sub['task_id'] !== null): ?>
                                        <div class="text-dark">
                                            <i class="fas fa-tasks text-muted me-1"></i>
                                            <?= htmlspecialchars($sub['task_title'] ?? 'Task #' . $sub['task_id']) ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-dark">
                                            <i class="fas fa-layer-group text-muted me-1"></i>
                                            Ch.<?= htmlspecialchars($sub['chapter_number']) ?> - <?= htmlspecialchars($sub['chapter_title'] ?? 'Chưa đặt tên') ?>
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
                                    $statusLabel = 'Pending';
                                    if ($sub['status'] === 'reviewed') {
                                        $statusClass = 'bg-info';
                                        $statusLabel = 'Reviewed';
                                    } elseif ($sub['status'] === 'approved') {
                                        $statusClass = 'bg-success';
                                        $statusLabel = 'Approved';
                                    } elseif ($sub['status'] === 'rejected') {
                                        $statusClass = 'bg-danger';
                                        $statusLabel = 'Rejected';
                                    }
                                    ?>
                                    <span class="badge <?= $statusClass ?> px-2 py-1"><?= $statusLabel ?></span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=show&id=<?= $sub['submission_id'] ?>" class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </a>
                                        
                                        <?php if (($role === 'assistant' || $role === 'mangaka') && $sub['status'] === 'pending' && $sub['user_id'] == $_SESSION['user_id']): ?>
                                            <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=delete&id=<?= $sub['submission_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa bản thảo này?');" title="Xóa bản thảo">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
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
                <p class="text-muted mb-0">Chưa có bản thảo nào được ghi nhận.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
