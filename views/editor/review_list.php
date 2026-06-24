<?php
$pageTitle = 'Quản lý Đánh giá Bản thảo';
$current_page = 'reviews';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Danh sách Bản thảo chờ duyệt</h2>
        <p class="text-muted text-xs mb-0">Xem và đánh giá các bản thảo được nộp.</p>
    </div>
</div>

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
    <div class="card-body p-0">
        <?php if (!empty($submissions)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Submission ID</th>
                            <th>Người gửi</th>
                            <th>Loại</th>
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
                                <td>
                                    <?php if ($sub['task_id'] !== null): ?>
                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">Task</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">Chapter</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($sub['series_title'] ?? 'N/A') ?></td>
                                <td>
                                    <?php if ($sub['task_id'] !== null): ?>
                                        <?= htmlspecialchars($sub['task_title'] ?? 'Task #' . $sub['task_id']) ?>
                                    <?php else: ?>
                                        Ch.<?= htmlspecialchars($sub['chapter_number']) ?> - <?= htmlspecialchars($sub['chapter_title'] ?? '') ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($sub['submitted_at'])) ?></td>
                                <td><span class="badge bg-secondary px-2 py-1">Pending</span></td>
                                <td class="text-end pe-4">
                                    <a href="<?= BASE_PATH ?>/index.php?controller=review&action=create&submission_id=<?= $sub['submission_id'] ?>" class="btn btn-sm btn-primary shadow-sm">
                                        <i class="fas fa-edit me-1"></i> Review
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-check-double fa-3x mb-3 text-success"></i>
                <p class="mb-0 fs-5">Tất cả bản thảo đã được duyệt!</p>
                <p class="small">Không có bản thảo nào đang chờ duyệt.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
