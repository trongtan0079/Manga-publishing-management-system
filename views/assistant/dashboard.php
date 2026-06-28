<?php 
/**
 * @var int $assignedTasks
 * @var int $inProgressTasks
 * @var int $completedTasks
 */
$pageTitle = 'Bảng theo dõi Trợ lý (Assistant)';
$current_page = 'dashboard';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Công việc Trợ lý</h2>
        <p class="text-muted text-xs mb-0">Quản lý và cập nhật tiến độ các trang vẽ được giao.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-4 col-md-6">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Task được giao</div>
                        <div class="h3 mb-0 fw-bold"><?= isset($assignedTasks) ? $assignedTasks : 0 ?></div>
                    </div>
                    <div class="stat-icon primary">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="card stat-card warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Task đang xử lý</div>
                        <div class="h3 mb-0 fw-bold"><?= isset($inProgressTasks) ? $inProgressTasks : 0 ?></div>
                    </div>
                    <div class="stat-icon warning">
                        <i class="fas fa-spinner"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="card stat-card success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Task hoàn thành</div>
                        <div class="h3 mb-0 fw-bold"><?= isset($completedTasks) ? $completedTasks : 0 ?></div>
                    </div>
                    <div class="stat-icon success">
                        <i class="fas fa-check-double"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="m-0"><i class="fas fa-tasks text-primary me-2"></i>Nhiệm vụ Cần xử lý</h6>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($activeTasks)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Công việc</th>
                                    <th>Ngữ cảnh</th>
                                    <th>Hạn chót</th>
                                    <th class="pe-3 text-end">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($activeTasks as $task): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <strong><?= htmlspecialchars($task['title']) ?></strong>
                                            <?php if ($task['priority'] === 'high'): ?>
                                                <span class="badge bg-danger ms-1">High</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="text-xs">
                                                <span class="text-dark fw-bold"><?= htmlspecialchars($task['series_title']) ?></span><br>
                                                <span class="text-muted">Ch.<?= htmlspecialchars($task['chapter_number']) ?> - Tr.<?= htmlspecialchars($task['page_number']) ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($task['due_date']): ?>
                                                <small class="<?= strtotime($task['due_date']) < time() ? 'text-danger fw-bold' : 'text-muted' ?>">
                                                    <?= date('d/m/Y H:i', strtotime($task['due_date'])) ?>
                                                </small>
                                            <?php else: ?>
                                                <small class="text-muted">Không có</small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-3 text-end">
                                            <a href="<?= BASE_PATH ?>/index.php?controller=task&action=index" class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <p class="text-muted mb-0">Chưa có dữ liệu</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
