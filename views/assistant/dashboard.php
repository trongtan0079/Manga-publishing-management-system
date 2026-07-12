<?php 
/**
 * View: Giao diện bảng điều khiển dành cho Trợ lý (dashboard.php)
 * Vai trò: Assistant (Trợ lý)
 * Chức năng: Hiển thị thống kê tổng quan về số lượng công việc được giao, công việc đang thực hiện, hoàn thành và các lối tắt hành động nhanh.
 * 
 * @var int $assignedTasks Tổng số công việc đã được giao
 * @var int $inProgressTasks Số công việc đang trong quá trình thực hiện
 * @var int $completedTasks Số công việc đã hoàn thành xuất sắc
 */
$pageTitle = 'Bảng theo dõi Trợ lý (Assistant)';
$current_page = 'dashboard';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<?php require_once __DIR__ . '/../layouts/welcome_banner.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Công việc Trợ lý</h2>
        <p class="text-muted text-xs mb-0">Quản lý và cập nhật tiến độ các trang vẽ được giao.</p>
    </div>
</div>

<style>
    .stat-card-link {
        text-decoration: none !important;
        display: flex;
        flex-direction: column;
        height: 100%;
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .stat-card-link:hover {
        transform: translateY(-5px);
    }
    .stat-card-link .card {
        border: none !important;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02), 0 10px 15px rgba(0,0,0,0.03) !important;
    }
    .stat-card-link:hover .card {
        box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important;
    }
</style>

<div class="row g-4 mb-4">
    <div class="col-xl-4 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=task&action=index" class="stat-card-link">
            <div class="card stat-card primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Task được giao</div>
                            <div class="h3 mb-0 fw-bold text-white"><?= isset($assignedTasks) ? $assignedTasks : 0 ?></div>
                        </div>
                        <div class="stat-icon primary" style="background: rgba(255,255,255,0.15); color: #ffffff;">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-4 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=task&action=index&status=in_progress" class="stat-card-link">
            <div class="card stat-card warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Task đang xử lý</div>
                            <div class="h3 mb-0 fw-bold text-white"><?= isset($inProgressTasks) ? $inProgressTasks : 0 ?></div>
                        </div>
                        <div class="stat-icon warning" style="background: rgba(255,255,255,0.15); color: #ffffff;">
                            <i class="fas fa-spinner"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-4 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=task&action=index&status=completed" class="stat-card-link">
            <div class="card stat-card success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Task hoàn thành</div>
                            <div class="h3 mb-0 fw-bold text-white"><?= isset($completedTasks) ? $completedTasks : 0 ?></div>
                        </div>
                        <div class="stat-icon success" style="background: rgba(255,255,255,0.15); color: #ffffff;">
                            <i class="fas fa-check-double"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Cột bên trái: Danh sách Task cần xử lý -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4 h-100">
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

    <!-- Cột bên phải: Thống kê Thù lao & Trang đã duyệt -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4 h-100">
            <div class="card-header">
                <h6 class="m-0"><i class="fas fa-hand-holding-usd text-success me-2"></i>Thù lao & Trang đã duyệt</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Tháng</th>
                                <th class="text-center">Số trang</th>
                                <th class="text-end">Thù lao</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($monthlyIncomeStats)): ?>
                                <?php foreach ($monthlyIncomeStats as $stat): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($stat['period']) ?></strong></td>
                                        <td class="text-center"><?= htmlspecialchars($stat['approved_pages_count']) ?> trang</td>
                                        <td class="text-end text-success fw-semibold"><?= number_format($stat['estimated_income'], 0, ',', '.') ?> đ</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Chưa phát sinh thù lao trong kỳ</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-light border mt-3 text-xs mb-0 d-flex align-items-start gap-2">
                    <i class="fas fa-info-circle text-primary mt-1"></i>
                    <span>Định mức thanh toán: <strong>300.000 đ</strong> / công việc trên mỗi trang vẽ được tác giả duyệt hoàn tất.</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
