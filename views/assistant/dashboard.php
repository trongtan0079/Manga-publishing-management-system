<?php
if (!defined('BASE_PATH')) {
    header('Location: /index.php');
    exit;
}
$pageTitle = 'Bảng theo dõi Trợ lý';
$current_page = 'dashboard';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<style>
    .welcome-banner {
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
        background-image: radial-gradient(rgba(255,255,255,0.08) 1px, transparent 1px);
        background-size: 20px 20px;
        color: #fff;
        border-radius: 16px;
        position: relative;
        overflow: hidden;
    }
    .stat-card-glow {
        transition: all 0.3s ease;
        border-radius: 14px;
        background: #ffffff;
    }
    .stat-card-glow:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
    }
    .soft-bg-primary { background: rgba(99, 102, 241, 0.1); color: #6366f1; }
    .soft-bg-success { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .soft-bg-warning { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .soft-bg-danger { background: rgba(239, 68, 68, 0.1); color: #ef4848; }
</style>

<!-- Banner Chào Mừng -->
<div class="welcome-banner p-4 mb-4 shadow-sm">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <h1 class="h3 fw-bold mb-2">Xin chào Trợ lý, <?= htmlspecialchars($_SESSION['full_name'] ?? 'Trợ lý') ?>!</h1>
            <p class="text-slate-300 mb-0 opacity-80" style="font-size: 14px;">Xem các nhiệm vụ vẽ được tác giả phân công và tải lên bản thảo trang vẽ đúng hạn nhé.</p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=mySubmissions" class="btn btn-light btn-sm fw-bold px-3 py-2" style="border-radius: 8px;">
                Nhật ký nộp bài
            </a>
        </div>
    </div>
</div>

<!-- 3 KPI Lớn -->
<div class="row g-3 mb-4">
    <div class="col-xl-4 col-md-6">
        <div class="card stat-card-glow border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-bold text-muted text-uppercase d-block mb-1">Nhiệm vụ được giao</span>
                    <h3 class="fw-bold mb-0 text-slate-800"><?= $assignedTasks ?></h3>
                    <small class="text-muted text-xs">Tổng số việc</small>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center soft-bg-primary" style="width: 48px; height: 48px;">
                    <i class="fas fa-clipboard-list fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="card stat-card-glow border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-bold text-muted text-uppercase d-block mb-1">Nhiệm vụ đang vẽ</span>
                    <h3 class="fw-bold mb-0 text-slate-800"><?= $inProgressTasks ?></h3>
                    <small class="text-warning text-xs"><i class="fas fa-spinner fa-spin me-1"></i>Đang thực hiện</small>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center soft-bg-warning" style="width: 48px; height: 48px;">
                    <i class="fas fa-tasks fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="card stat-card-glow border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-bold text-muted text-uppercase d-block mb-1">Công việc hoàn thành</span>
                    <h3 class="fw-bold mb-0 text-slate-800"><?= $completedTasks ?></h3>
                    <small class="text-success text-xs"><i class="fas fa-check-circle me-1"></i>Đã phê duyệt</small>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center soft-bg-success" style="width: 48px; height: 48px;">
                    <i class="fas fa-check-double fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Trái: Nhiệm vụ cần xử lý -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-slate-700"><i class="fas fa-tasks text-primary me-2"></i>Nhiệm vụ cần xử lý</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="table-light text-muted" style="font-size: 11px;">
                                <th class="ps-3">Công việc</th>
                                <th>Ngữ cảnh</th>
                                <th>Độ ưu tiên</th>
                                <th>Hạn chót</th>
                                <th class="pe-3 text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($activeTasks)): ?>
                                <?php foreach ($activeTasks as $task): ?>
                                    <?php 
                                        $priorityClass = 'bg-secondary';
                                        if ($task['priority'] === 'high') $priorityClass = 'bg-danger';
                                        elseif ($task['priority'] === 'medium') $priorityClass = 'bg-warning text-dark';
                                        
                                        $typeLabel = 'Khác';
                                        if ($task['task_type'] === 'background') $typeLabel = 'Vẽ nền';
                                        elseif ($task['task_type'] === 'inking') $typeLabel = 'Đi nét';
                                        elseif ($task['task_type'] === 'coloring') $typeLabel = 'Lên màu';
                                    ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-bold text-slate-800" style="font-size: 13px;"><?= htmlspecialchars($task['title']) ?></div>
                                            <small class="text-muted" style="font-size: 11px;">Loại vẽ: <?= $typeLabel ?></small>
                                        </td>
                                        <td>
                                            <div class="text-slate-800" style="font-size: 12px;">Ch.<?= htmlspecialchars($task['chapter_number']) ?> - Trang <?= htmlspecialchars($task['page_number']) ?></div>
                                            <small class="text-muted" style="font-size: 11px;"><?= htmlspecialchars($task['series_title']) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge <?= $priorityClass ?> text-xs"><?= ucfirst(htmlspecialchars($task['priority'])) ?></span>
                                        </td>
                                        <td>
                                            <span class="text-slate-700" style="font-size: 12px;"><?= date('d/m/Y', strtotime($task['due_date'])) ?></span>
                                        </td>
                                        <td class="pe-3 text-end">
                                            <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=submit&task_id=<?= $task['task_id'] ?>" class="btn btn-outline-primary btn-xs fw-bold">
                                                <i class="fas fa-upload me-1"></i>Nộp bài
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted small">Bạn không có nhiệm vụ nào đang xử lý.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Phải: Tài nguyên dùng chung & Thù lao khoán sản phẩm -->
    <div class="col-lg-4">
        <!-- Widget 1: Hộp tài nguyên Studio -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="m-0 fw-bold text-slate-700"><i class="fas fa-folder-open text-warning me-2"></i>Tài nguyên Studio dùng chung</h6>
            </div>
            <div class="card-body py-2">
                <div class="list-group list-group-flush">
                    <?php foreach ($quickResources as $res): ?>
                        <div class="list-group-item px-0 py-2 border-0 bg-transparent d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 32px; height: 32px;">
                                    <i class="fas <?= $res['icon'] ?>"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-slate-800 text-truncate" style="font-size: 12px; max-width: 150px;" title="<?= htmlspecialchars($res['title']) ?>"><?= htmlspecialchars($res['title']) ?></div>
                                    <small class="text-muted" style="font-size: 10px;"><?= $res['size'] ?></small>
                                </div>
                            </div>
                            <button class="btn btn-link btn-xs text-muted" title="Tải xuống" disabled><i class="fas fa-download"></i></button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Widget 2: Thu nhập sản phẩm hàng tháng -->
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="m-0 fw-bold text-slate-700"><i class="fas fa-wallet text-success me-2"></i>Ước tính Thù lao sản phẩm</h6>
            </div>
            <div class="card-body py-2">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0" style="font-size: 12px;">
                        <thead>
                            <tr class="text-muted">
                                <th>Tháng</th>
                                <th class="text-center">Số trang</th>
                                <th class="text-end">Tạm tính (đ)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($monthlyIncomeStats)): ?>
                                <?php foreach ($monthlyIncomeStats as $stat): ?>
                                    <tr>
                                        <td><strong>Tháng <?= htmlspecialchars($stat['period']) ?></strong></td>
                                        <td class="text-center"><?= $stat['completed_tasks_count'] ?> trang</td>
                                        <td class="text-end text-success fw-bold"><?= number_format($stat['estimated_income']) ?> đ</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted small">Chưa hoàn thành công việc để tính lương.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
