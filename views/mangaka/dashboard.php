<?php 
require_once __DIR__ . '/../../core/Auth.php';
requireRole('mangaka');
$pageTitle = 'Không gian sáng tác (Mangaka)';
$current_page = 'dashboard';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Không gian Sáng tác</h2>
        <p class="text-muted text-xs mb-0">Quản lý tác phẩm, nộp bản thảo và theo dõi phản hồi từ Biên tập viên.</p>
    </div>
    <button class="btn btn-primary shadow-sm"><i class="fas fa-upload me-2"></i>Nộp Bản Thảo Mới</button>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card info h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Series đang quản lý</div>
                        <div class="h3 mb-0 fw-bold"><?= isset($totalSeries) ? $totalSeries : 0 ?></div>
                    </div>
                    <div class="stat-icon info">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Chapter đang thực hiện</div>
                        <div class="h3 mb-0 fw-bold"><?= isset($totalChapters) ? $totalChapters : 0 ?></div>
                    </div>
                    <div class="stat-icon primary">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Page đang thực hiện</div>
                        <div class="h3 mb-0 fw-bold"><?= isset($totalPages) ? $totalPages : 0 ?></div>
                    </div>
                    <div class="stat-icon success">
                        <i class="fas fa-images"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Task đang giao</div>
                        <div class="h3 mb-0 fw-bold"><?= isset($totalTasks) ? $totalTasks : 0 ?></div>
                    </div>
                    <div class="stat-icon warning">
                        <i class="fas fa-tasks"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h6 class="m-0 fw-bold"><i class="fas fa-history text-primary me-2"></i>Hoạt động Gần đây</h6>
            </div>
            <div class="card-body text-center py-5">
                <div class="text-muted mb-2"><i class="fas fa-inbox fs-1 opacity-25"></i></div>
                <p class="text-muted mb-0">Chưa có hoạt động nào được ghi nhận</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <?php require_once __DIR__ . '/../shared/dashboard_notifications.php'; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
