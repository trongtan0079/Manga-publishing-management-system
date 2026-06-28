<?php 
/**
 * Admin Dashboard - Tổng quan hệ thống
 * @var int $totalUsers, $totalSeries, $totalChapters, $totalPages
 * @var int $totalTasks, $totalSubmissions, $totalReviews, $totalNotifications, $totalRankings
 * @var int $activeUsers, $inactiveUsers, $bannedUsers
 * @var array $usersByRole, $tasksByStatus, $subsByStatus
 */
$pageTitle = 'Quản trị hệ thống';
$current_page = 'dashboard';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Tổng quan Hệ thống</h2>
        <p class="text-muted text-xs mb-0">Chào mừng trở lại, theo dõi các chỉ số quan trọng của toàn bộ hệ thống xuất bản.</p>
    </div>
    <a href="<?= BASE_PATH ?>/index.php?controller=user&action=create" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-2"></i>Thêm người dùng mới</a>
</div>

<!-- Row 1: Thống kê chính (4 cards) -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Tổng User</div>
                        <div class="h3 mb-0 fw-bold"><?= $totalUsers ?></div>
                    </div>
                    <div class="stat-icon primary"><i class="fas fa-users"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Tổng Series</div>
                        <div class="h3 mb-0 fw-bold"><?= $totalSeries ?></div>
                    </div>
                    <div class="stat-icon success"><i class="fas fa-book-open"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Tổng Chapter</div>
                        <div class="h3 mb-0 fw-bold"><?= $totalChapters ?></div>
                    </div>
                    <div class="stat-icon warning"><i class="fas fa-file-alt"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card danger h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Tổng Page</div>
                        <div class="h3 mb-0 fw-bold"><?= $totalPages ?></div>
                    </div>
                    <div class="stat-icon danger"><i class="fas fa-images"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Thống kê phụ (5 cards) -->
<div class="row g-4 mb-4">
    <div class="col-xl col-md-6">
        <div class="card stat-card info h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Tổng Task</div>
                        <div class="h3 mb-0 fw-bold"><?= $totalTasks ?></div>
                    </div>
                    <div class="stat-icon info"><i class="fas fa-tasks"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-6">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Tổng Bản thảo</div>
                        <div class="h3 mb-0 fw-bold"><?= $totalSubmissions ?></div>
                    </div>
                    <div class="stat-icon primary"><i class="fas fa-file-upload"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-6">
        <div class="card stat-card success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Tổng Đánh giá</div>
                        <div class="h3 mb-0 fw-bold"><?= $totalReviews ?></div>
                    </div>
                    <div class="stat-icon success"><i class="fas fa-comment-dots"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-6">
        <div class="card stat-card warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Tổng Thông báo</div>
                        <div class="h3 mb-0 fw-bold"><?= $totalNotifications ?></div>
                    </div>
                    <div class="stat-icon warning"><i class="fas fa-bell"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-6">
        <div class="card stat-card danger h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Tổng Xếp hạng</div>
                        <div class="h3 mb-0 fw-bold"><?= $totalRankings ?></div>
                    </div>
                    <div class="stat-icon danger"><i class="fas fa-trophy"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 3: User Status Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-4 col-md-6">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #198754 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-uppercase mb-2" style="color: #198754;">Active Users</div>
                        <div class="h3 mb-0 fw-bold"><?= $activeUsers ?></div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(25,135,84,0.1);">
                        <i class="fas fa-user-check" style="color: #198754;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #6c757d !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-uppercase mb-2" style="color: #6c757d;">Inactive Users</div>
                        <div class="h3 mb-0 fw-bold"><?= $inactiveUsers ?></div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(108,117,125,0.1);">
                        <i class="fas fa-user-clock" style="color: #6c757d;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-uppercase mb-2" style="color: #dc3545;">Banned Users</div>
                        <div class="h3 mb-0 fw-bold"><?= $bannedUsers ?></div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(220,53,69,0.1);">
                        <i class="fas fa-user-slash" style="color: #dc3545;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 4: Charts -->
<div class="row g-4 mb-4">
    <!-- Chart 1: User theo Role -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom pt-3 pb-3">
                <h6 class="m-0 fw-bold"><i class="fas fa-chart-bar text-primary me-2"></i>User theo Vai trò</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="chartUsersByRole" style="max-height: 260px;"></canvas>
            </div>
        </div>
    </div>
    <!-- Chart 2: Task theo Status -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom pt-3 pb-3">
                <h6 class="m-0 fw-bold"><i class="fas fa-chart-pie text-warning me-2"></i>Task theo Trạng thái</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="chartTasksByStatus" style="max-height: 260px;"></canvas>
            </div>
        </div>
    </div>
    <!-- Chart 3: Submission theo Status -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom pt-3 pb-3">
                <h6 class="m-0 fw-bold"><i class="fas fa-chart-pie text-info me-2"></i>Bản thảo theo Trạng thái</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="chartSubsByStatus" style="max-height: 260px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Row 5: Notifications -->
<div class="row">
    <div class="col-lg-8">
        <!-- Placeholder cho phần mở rộng sau này -->
    </div>
    <div class="col-lg-4">
        <?php require_once __DIR__ . '/../shared/dashboard_notifications.php'; ?>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart 1: User theo Role (Bar Chart)
    <?php
        $roleLabels = [];
        $roleCounts = [];
        if (!empty($usersByRole)) {
            foreach ($usersByRole as $r) {
                $roleLabels[] = ucfirst($r['role_name']);
                $roleCounts[] = (int)$r['user_count'];
            }
        }
    ?>
    new Chart(document.getElementById('chartUsersByRole'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($roleLabels) ?>,
            datasets: [{
                label: 'Số lượng',
                data: <?= json_encode($roleCounts) ?>,
                backgroundColor: ['#dc3545', '#6366f1', '#0dcaf0', '#ffc107', '#198754'],
                borderRadius: 6,
                maxBarThickness: 40
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    // Chart 2: Task theo Status (Doughnut)
    new Chart(document.getElementById('chartTasksByStatus'), {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'In Progress', 'Completed'],
            datasets: [{
                data: [
                    <?= $tasksByStatus['pending'] ?? 0 ?>,
                    <?= $tasksByStatus['in_progress'] ?? 0 ?>,
                    <?= $tasksByStatus['completed'] ?? 0 ?>
                ],
                backgroundColor: ['#ffc107', '#0d6efd', '#198754'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            cutout: '60%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true } }
            }
        }
    });

    // Chart 3: Submission theo Status (Doughnut)
    new Chart(document.getElementById('chartSubsByStatus'), {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Approved', 'Rejected'],
            datasets: [{
                data: [
                    <?= $subsByStatus['pending'] ?? 0 ?>,
                    <?= $subsByStatus['approved'] ?? 0 ?>,
                    <?= $subsByStatus['rejected'] ?? 0 ?>
                ],
                backgroundColor: ['#ffc107', '#198754', '#dc3545'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            cutout: '60%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true } }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
