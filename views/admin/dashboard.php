<?php 
require_once __DIR__ . '/../../core/Auth.php';
requireRole('admin');
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
    <button class="btn btn-primary shadow-sm"><i class="fas fa-plus me-2"></i>Thêm người dùng mới</button>
</div>

<div class="row g-4 mb-4">
    <!-- Stat Card 1 -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Tổng User</div>
                        <div class="h3 mb-0 fw-bold"><?= isset($totalUsers) ? $totalUsers : 0 ?></div>
                    </div>
                    <div class="stat-icon primary">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Card 2 -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Tổng Series</div>
                        <div class="h3 mb-0 fw-bold"><?= isset($totalSeries) ? $totalSeries : 0 ?></div>
                    </div>
                    <div class="stat-icon success">
                        <i class="fas fa-book-open"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Card 3 -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Tổng Chapter</div>
                        <div class="h3 mb-0 fw-bold"><?= isset($totalChapters) ? $totalChapters : 0 ?></div>
                    </div>
                    <div class="stat-icon warning">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Card 4 -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card danger h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Tổng Page</div>
                        <div class="h3 mb-0 fw-bold"><?= isset($totalPages) ? $totalPages : 0 ?></div>
                    </div>
                    <div class="stat-icon danger">
                        <i class="fas fa-images"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0"><i class="fas fa-chart-area text-primary me-2"></i>Biểu đồ Thống kê Toàn hệ thống</h6>
            </div>
            <div class="card-body text-center py-5" style="background-color: #f8fafc; border-radius: 8px;">
                <p class="text-muted mb-0">Chưa có dữ liệu</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <?php require_once __DIR__ . '/../shared/dashboard_notifications.php'; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
