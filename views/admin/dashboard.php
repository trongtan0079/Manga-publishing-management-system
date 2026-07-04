<?php
if (!defined('BASE_PATH')) {
    header('Location: /index.php');
    exit;
}
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
    .soft-bg-info { background: rgba(6, 182, 212, 0.1); color: #06b6d4; }
    
    .avatar-initial {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
    }
</style>

<!-- Banner Chào Mừng -->
<div class="welcome-banner p-4 mb-4 shadow-sm">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <h1 class="h3 fw-bold mb-2">Hệ thống Quản trị Xuất bản MangaPMS</h1>
            <p class="text-slate-300 mb-0 opacity-80" style="font-size: 14px;">Giám sát hiệu suất hoạt động, phân bổ vai trò và bảo mật toàn vẹn dữ liệu xuất bản.</p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <a href="<?= BASE_PATH ?>/index.php?controller=user&action=create" class="btn btn-light btn-sm fw-bold px-3 py-2" style="border-radius: 8px;">
                <i class="fas fa-plus me-2 text-primary"></i>Thêm người dùng mới
            </a>
        </div>
    </div>
</div>

<!-- 4 Chỉ số KPI chính -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card-glow border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-bold text-muted text-uppercase d-block mb-1">Tổng Thành Viên</span>
                    <h3 class="fw-bold mb-0 text-slate-800"><?= $totalUsers ?></h3>
                    <small class="text-success text-xs"><i class="fas fa-circle me-1"></i><?= $activeUsers ?> Active</small>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center soft-bg-primary" style="width: 48px; height: 48px;">
                    <i class="fas fa-users fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card-glow border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-bold text-muted text-uppercase d-block mb-1">Dự án Manga</span>
                    <h3 class="fw-bold mb-0 text-slate-800"><?= $totalSeries ?></h3>
                    <small class="text-muted text-xs">Series tác phẩm</small>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center soft-bg-success" style="width: 48px; height: 48px;">
                    <i class="fas fa-book-open fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card-glow border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-bold text-muted text-uppercase d-block mb-1">Chương xuất bản</span>
                    <h3 class="fw-bold mb-0 text-slate-800"><?= $totalChapters ?></h3>
                    <small class="text-muted text-xs">Chapters biên tập</small>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center soft-bg-warning" style="width: 48px; height: 48px;">
                    <i class="fas fa-file-alt fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card-glow border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-bold text-muted text-uppercase d-block mb-1">Tổng Số Trang vẽ</span>
                    <h3 class="fw-bold mb-0 text-slate-800"><?= $totalPages ?></h3>
                    <small class="text-muted text-xs">Manga pages</small>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center soft-bg-danger" style="width: 48px; height: 48px;">
                    <i class="fas fa-images fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 5 KPI Phụ hàng ngang -->
<div class="row g-3 mb-4">
    <div class="col-xl col-md-4">
        <div class="card stat-card-glow border-0 shadow-sm py-2">
            <div class="card-body py-2 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-bold text-muted d-block" style="font-size: 11px;">Tổng Task</span>
                    <span class="h5 fw-bold text-slate-800 mb-0"><?= $totalTasks ?></span>
                </div>
                <i class="fas fa-tasks text-muted opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-4">
        <div class="card stat-card-glow border-0 shadow-sm py-2">
            <div class="card-body py-2 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-bold text-muted d-block" style="font-size: 11px;">Bản thảo nộp</span>
                    <span class="h5 fw-bold text-slate-800 mb-0"><?= $totalSubmissions ?></span>
                </div>
                <i class="fas fa-file-upload text-muted opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-4">
        <div class="card stat-card-glow border-0 shadow-sm py-2">
            <div class="card-body py-2 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-bold text-muted d-block" style="font-size: 11px;">Đánh giá</span>
                    <span class="h5 fw-bold text-slate-800 mb-0"><?= $totalReviews ?></span>
                </div>
                <i class="fas fa-comment-dots text-muted opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-6">
        <div class="card stat-card-glow border-0 shadow-sm py-2">
            <div class="card-body py-2 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-bold text-muted d-block" style="font-size: 11px;">Thông báo</span>
                    <span class="h5 fw-bold text-slate-800 mb-0"><?= $totalNotifications ?></span>
                </div>
                <i class="fas fa-bell text-muted opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-6">
        <div class="card stat-card-glow border-0 shadow-sm py-2">
            <div class="card-body py-2 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-bold text-muted d-block" style="font-size: 11px;">Kỳ xếp hạng</span>
                    <span class="h5 fw-bold text-slate-800 mb-0"><?= $totalRankings ?></span>
                </div>
                <i class="fas fa-trophy text-muted opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<!-- Biểu đồ phân tích -->
<div class="row g-4 mb-4">
    <!-- Chart 1: User theo Role -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="m-0 fw-bold text-slate-700"><i class="fas fa-chart-bar text-primary me-2"></i>User theo Vai trò</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center py-4">
                <canvas id="chartUsersByRole" 
                        data-labels="<?= htmlspecialchars(json_encode($roleLabels), ENT_QUOTES, 'UTF-8') ?>" 
                        data-values="<?= htmlspecialchars(json_encode($roleCounts), ENT_QUOTES, 'UTF-8') ?>" 
                        style="max-height: 220px;"></canvas>
            </div>
        </div>
    </div>
    <!-- Chart 2: Task theo Status -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="m-0 fw-bold text-slate-700"><i class="fas fa-chart-pie text-warning me-2"></i>Nhiệm vụ (Tasks)</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center py-4">
                <canvas id="chartTasksByStatus" 
                        data-values="<?= htmlspecialchars(json_encode([
                            $tasksByStatus['pending'] ?? 0,
                            $tasksByStatus['in_progress'] ?? 0,
                            $tasksByStatus['completed'] ?? 0
                        ]), ENT_QUOTES, 'UTF-8') ?>" 
                        style="max-height: 220px;"></canvas>
            </div>
        </div>
    </div>
    <!-- Chart 3: Submission theo Status -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="m-0 fw-bold text-slate-700"><i class="fas fa-chart-pie text-info me-2"></i>Trạng thái Bản thảo</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center py-4">
                <canvas id="chartSubsByStatus" 
                        data-values="<?= htmlspecialchars(json_encode([
                            $subsByStatus['pending'] ?? 0,
                            $subsByStatus['reviewed'] ?? 0,
                            $subsByStatus['approved'] ?? 0,
                            $subsByStatus['rejected'] ?? 0
                        ]), ENT_QUOTES, 'UTF-8') ?>" 
                        style="max-height: 220px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Thành viên mới & Nhật ký hoạt động truy cập hệ thống -->
<div class="row g-4 mb-4">
    <!-- Cột trái: Thành viên mới đăng ký gần đây -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-slate-700"><i class="fas fa-user-plus text-primary me-2"></i>Thành viên đăng ký gần đây</h6>
                <a href="<?= BASE_PATH ?>/index.php?controller=user&action=index" class="text-decoration-none small text-slate-300">Quản lý User</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="table-light text-muted" style="font-size: 11px;">
                                <th class="ps-3">Người dùng</th>
                                <th>Vai trò</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentUsers)): ?>
                                <?php foreach ($recentUsers as $ru): ?>
                                    <?php 
                                        $initial = strtoupper(substr($ru['username'], 0, 1));
                                        $statusClass = 'bg-success';
                                        if ($ru['status'] === 'inactive') $statusClass = 'bg-secondary';
                                        elseif ($ru['status'] === 'banned') $statusClass = 'bg-danger';
                                    ?>
                                    <tr>
                                        <td class="ps-3 d-flex align-items-center gap-2">
                                            <div class="avatar-initial"><?= $initial ?></div>
                                            <div>
                                                <div class="fw-bold text-slate-800" style="font-size: 13px;"><?= htmlspecialchars($ru['full_name'] ?: $ru['username']) ?></div>
                                                <div class="text-muted" style="font-size: 11px;"><?= htmlspecialchars($ru['email']) ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark text-xs border"><?= ucfirst(htmlspecialchars($ru['role_name'])) ?></span>
                                        </td>
                                        <td>
                                            <span class="badge <?= $statusClass ?> text-xs"><?= ucfirst(htmlspecialchars($ru['status'])) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted small">Không có người dùng nào.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Cột phải: Nhật ký truy cập hệ thống bảo mật -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="m-0 fw-bold text-slate-700"><i class="fas fa-shield-alt text-danger me-2"></i>Nhật ký bảo mật & Đăng nhập</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="table-light text-muted" style="font-size: 11px;">
                                <th class="ps-3">Tài khoản</th>
                                <th>Địa chỉ IP</th>
                                <th>Trình duyệt</th>
                                <th class="pe-3 text-end">Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentLogins as $login): ?>
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-bold text-slate-800" style="font-size: 13px;"><?= htmlspecialchars($login['username']) ?></div>
                                        <small class="text-muted" style="font-size: 10px;"><?= ucfirst(htmlspecialchars($login['role'])) ?></small>
                                    </td>
                                    <td><code style="font-size: 11px;"><?= htmlspecialchars($login['ip']) ?></code></td>
                                    <td style="font-size: 11px;"><?= htmlspecialchars($login['browser']) ?></td>
                                    <td class="pe-3 text-end text-muted" style="font-size: 11px;"><?= $login['time'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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
        if (canvas1) {
            const roleLabels = JSON.parse(canvas1.getAttribute('data-labels') || '[]');
            const roleCounts = JSON.parse(canvas1.getAttribute('data-values') || '[]');
            
            new Chart(canvas1, {
                type: 'bar',
                data: {
                    labels: roleLabels,
                    datasets: [{
                        label: 'Số lượng',
                        data: roleCounts,
                        backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4848', '#06b6d4'],
                        borderRadius: 6,
                        maxBarThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } }
                    }
                }
            });
        }

        // Chart 2: Task theo Status (Doughnut)
        const canvas2 = document.getElementById('chartTasksByStatus');
        if (canvas2) {
            const taskCounts = JSON.parse(canvas2.getAttribute('data-values') || '[]');
            new Chart(canvas2, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'In Progress', 'Completed'],
                    datasets: [{
                        data: taskCounts,
                        backgroundColor: ['#f59e0b', '#6366f1', '#10b981'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 10, padding: 8, usePointStyle: true, font: { size: 10 } }
                        }
                    }
                }
            });
        }

        // Chart 3: Submission theo Status (Doughnut)
        const canvas3 = document.getElementById('chartSubsByStatus');
        if (canvas3) {
            const subCounts = JSON.parse(canvas3.getAttribute('data-values') || '[]');
            new Chart(canvas3, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Reviewed', 'Approved', 'Rejected'],
                    datasets: [{
                        data: subCounts,
                        backgroundColor: ['#f59e0b', '#06b6d4', '#10b981', '#ef4848'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 10, padding: 8, usePointStyle: true, font: { size: 10 } }
                        }
                    }
                }
            });
        }
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>