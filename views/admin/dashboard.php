<?php
if (!defined('BASE_PATH')) {
    header('Location: /index.php');
    exit;
}
/**
 * Admin Dashboard - Tổng quan hệ thống
 * @var int $totalUsers
 * @var int $totalSeries
 * @var int $totalChapters
 * @var int $totalPages
 * @var int $totalTasks
 * @var int $totalSubmissions
 * @var int $totalReviews
 * @var int $totalNotifications
 * @var int $totalRankings
 * @var int $activeUsers
 * @var int $inactiveUsers
 * @var int $bannedUsers
 * @var array $usersByRole
 * @var array $tasksByStatus
 * @var array $subsByStatus
 */
$pageTitle = 'Quản trị hệ thống';
$current_page = 'dashboard';

// Chuẩn bị dữ liệu cho biểu đồ
$roleLabels = [];
$roleCounts = [];
if (!empty($usersByRole)) {
    foreach ($usersByRole as $r) {
        $roleLabels[] = ucfirst($r['role_name']);
        $roleCounts[] = (int)$r['user_count'];
    }
}

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<?php require_once __DIR__ . '/../layouts/welcome_banner.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Tổng quan Hệ thống</h2>
        <p class="text-muted text-xs mb-0">Chào mừng trở lại, theo dõi các chỉ số quan trọng của toàn bộ hệ thống xuất bản.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_PATH ?>/index.php?controller=dashboard&action=backupDb" class="btn btn-outline-success shadow-sm"><i class="fas fa-download me-2"></i>Sao lưu Database</a>
        <a href="<?= BASE_PATH ?>/index.php?controller=dashboard&action=logs" class="btn btn-outline-secondary shadow-sm"><i class="fas fa-history me-2"></i>Xem Nhật ký</a>
        <a href="<?= BASE_PATH ?>/index.php?controller=user&action=create" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-2"></i>Thêm người dùng</a>
    </div>
</div>

<h5 class="fw-bold mb-3 text-slate-700"><i class="fas fa-layer-group text-primary me-2"></i>Thống kê Tổng quan</h5>
<!-- Row 1: Thống kê chính (4 cards) -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=user&action=index" class="stat-card-link">
            <div class="card stat-card primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Tổng User</div>
                            <div class="h3 mb-0 fw-bold text-white"><?= $totalUsers ?></div>
                        </div>
                        <div class="stat-icon primary" style="background: rgba(255,255,255,0.15); color: #ffffff;"><i class="fas fa-users"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=series&action=index" class="stat-card-link">
            <div class="card stat-card success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Tổng Series</div>
                            <div class="h3 mb-0 fw-bold text-white"><?= $totalSeries ?></div>
                        </div>
                        <div class="stat-icon success" style="background: rgba(255,255,255,0.15); color: #ffffff;"><i class="fas fa-book-open"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=series&action=index" class="stat-card-link">
            <div class="card stat-card warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Tổng Chapter</div>
                            <div class="h3 mb-0 fw-bold text-white"><?= $totalChapters ?></div>
                        </div>
                        <div class="stat-icon warning" style="background: rgba(255,255,255,0.15); color: #ffffff;"><i class="fas fa-file-alt"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=series&action=index" class="stat-card-link">
            <div class="card stat-card danger h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Tổng Page</div>
                            <div class="h3 mb-0 fw-bold text-white"><?= $totalPages ?></div>
                        </div>
                        <div class="stat-icon danger" style="background: rgba(255,255,255,0.15); color: #ffffff;"><i class="fas fa-images"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Row 2: Thống kê phụ (5 cards) -->
<div class="row g-4 mb-4">
    <div class="col-xl col-lg-4 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=series&action=index" class="stat-card-link">
            <div class="card stat-card info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Tổng Task</div>
                            <div class="h3 mb-0 fw-bold text-white"><?= $totalTasks ?></div>
                        </div>
                        <div class="stat-icon info" style="background: rgba(255,255,255,0.15); color: #ffffff;"><i class="fas fa-tasks"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl col-lg-4 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=series&action=index" class="stat-card-link">
            <div class="card stat-card primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Tổng Bản thảo</div>
                            <div class="h3 mb-0 fw-bold text-white"><?= $totalSubmissions ?></div>
                        </div>
                        <div class="stat-icon primary" style="background: rgba(255,255,255,0.15); color: #ffffff;"><i class="fas fa-file-upload"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl col-lg-4 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=seriesRanking&action=index" class="stat-card-link">
            <div class="card stat-card success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Tổng Đánh giá</div>
                            <div class="h3 mb-0 fw-bold text-white"><?= $totalReviews ?></div>
                        </div>
                        <div class="stat-icon success" style="background: rgba(255,255,255,0.15); color: #ffffff;"><i class="fas fa-comment-dots"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl col-lg-4 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=notification&action=index" class="stat-card-link">
            <div class="card stat-card warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Tổng Thông báo</div>
                            <div class="h3 mb-0 fw-bold text-white"><?= $totalNotifications ?></div>
                        </div>
                        <div class="stat-icon warning" style="background: rgba(255,255,255,0.15); color: #ffffff;"><i class="fas fa-bell"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl col-lg-4 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=seriesRanking&action=index" class="stat-card-link">
            <div class="card stat-card danger h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Tổng Xếp hạng</div>
                            <div class="h3 mb-0 fw-bold text-white"><?= $totalRankings ?></div>
                        </div>
                        <div class="stat-icon danger" style="background: rgba(255,255,255,0.15); color: #ffffff;"><i class="fas fa-trophy"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<h5 class="fw-bold mb-3 text-slate-700 mt-4"><i class="fas fa-users-cog text-primary me-2"></i>Trạng thái Tài khoản</h5>
<!-- Row 3: User Status Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-4 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=user&action=index&status=active" class="stat-card-link">
            <div class="card border-0 shadow-sm h-100" style="background-color: var(--success-soft) !important; border: 1px solid var(--success-border) !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-uppercase mb-2" style="color: var(--success);">Hoạt động</div>
                            <div class="h3 mb-0 fw-bold" style="color: var(--slate-900);"><?= $activeUsers ?></div>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(25,135,84,0.15);">
                            <i class="fas fa-user-check" style="color: var(--success);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-4 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=user&action=index&status=inactive" class="stat-card-link">
            <div class="card border-0 shadow-sm h-100" style="background-color: var(--slate-100) !important; border: 1px solid var(--slate-200) !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-uppercase mb-2" style="color: var(--slate-600);">Tạm khóa</div>
                            <div class="h3 mb-0 fw-bold" style="color: var(--slate-900);"><?= $inactiveUsers ?></div>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(108,117,125,0.15);">
                            <i class="fas fa-user-clock" style="color: var(--slate-600);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-4 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=user&action=index&status=banned" class="stat-card-link">
            <div class="card border-0 shadow-sm h-100" style="background-color: var(--danger-soft) !important; border: 1px solid var(--danger-border) !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-uppercase mb-2" style="color: var(--danger);">Bị đình chỉ</div>
                            <div class="h3 mb-0 fw-bold" style="color: var(--slate-900);"><?= $bannedUsers ?></div>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(220,53,69,0.15);">
                            <i class="fas fa-user-slash" style="color: var(--danger);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<h5 class="fw-bold mb-3 text-slate-700 mt-4"><i class="fas fa-chart-bar text-primary me-2"></i>Biểu đồ Phân tích</h5>
<!-- Row 4: Chart 1 (User theo Vai trò) - Hàng riêng biệt -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom pt-3 pb-3">
                <h6 class="m-0 fw-bold"><i class="fas fa-chart-bar text-primary me-2"></i>User theo Vai trò</h6>
            </div>
            <div class="card-body">
                <canvas id="chartUsersByRole" 
                        data-labels="<?= htmlspecialchars(json_encode($roleLabels), ENT_QUOTES, 'UTF-8') ?>" 
                        data-values="<?= htmlspecialchars(json_encode($roleCounts), ENT_QUOTES, 'UTF-8') ?>" 
                        style="max-height: 260px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Row 5: Chart 2 & 3 (Task & Bản thảo) - Chung một hàng -->
<div class="row g-4 mb-4">
    <!-- Chart 2: Task theo Status -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom pt-3 pb-3">
                <h6 class="m-0 fw-bold"><i class="fas fa-chart-pie text-warning me-2"></i>Task theo Trạng thái</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="chartTasksByStatus" 
                        data-values="<?= htmlspecialchars(json_encode([
                            $tasksByStatus['pending'] ?? 0,
                            $tasksByStatus['in_progress'] ?? 0,
                            $tasksByStatus['completed'] ?? 0
                        ]), ENT_QUOTES, 'UTF-8') ?>" 
                        style="max-height: 260px;"></canvas>
            </div>
        </div>
    </div>
    <!-- Chart 3: Submission theo Status -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom pt-3 pb-3">
                <h6 class="m-0 fw-bold"><i class="fas fa-chart-pie text-info me-2"></i>Bản thảo theo Trạng thái</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="chartSubsByStatus" 
                        data-values="<?= htmlspecialchars(json_encode([
                            $subsByStatus['pending'] ?? 0,
                            $subsByStatus['reviewed'] ?? 0,
                            $subsByStatus['approved'] ?? 0,
                            $subsByStatus['rejected'] ?? 0
                        ]), ENT_QUOTES, 'UTF-8') ?>" 
                        style="max-height: 260px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart 1: User theo Role (Bar Chart)
        const canvas1 = document.getElementById('chartUsersByRole');
        const roleLabels = JSON.parse(canvas1.getAttribute('data-labels') || '[]');
        const roleCounts = JSON.parse(canvas1.getAttribute('data-values') || '[]');
        
        new Chart(canvas1, {
            type: 'bar',
            data: {
                labels: roleLabels,
                datasets: [{
                    label: 'Số lượng',
                    data: roleCounts,
                    backgroundColor: ['#dc3545', '#6366f1', '#0dcaf0', '#ffc107', '#198754'],
                    borderRadius: 6,
                    maxBarThickness: 40
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Chart 2: Task theo Status (Doughnut)
        const canvas2 = document.getElementById('chartTasksByStatus');
        const taskCounts = JSON.parse(canvas2.getAttribute('data-values') || '[]');
        
        new Chart(canvas2, {
            type: 'doughnut',
            data: {
                labels: ['Chờ thực hiện', 'Đang làm', 'Đã hoàn thành'],
                datasets: [{
                    data: taskCounts,
                    backgroundColor: ['#ffc107', '#0d6efd', '#198754'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 12,
                            usePointStyle: true
                        }
                    }
                }
            }
        });

        // Chart 3: Submission theo Status (Doughnut)
        const canvas3 = document.getElementById('chartSubsByStatus');
        const subCounts = JSON.parse(canvas3.getAttribute('data-values') || '[]');
        
        new Chart(canvas3, {
            type: 'doughnut',
            data: {
                labels: ['Chờ review', 'Đang đánh giá', 'Phê duyệt', 'Từ chối'],
                datasets: [{
                    data: subCounts,
                    backgroundColor: ['#ffc107', '#0ea5e9', '#198754', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 12,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>