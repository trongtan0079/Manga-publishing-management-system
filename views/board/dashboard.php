<?php 
require_once __DIR__ . '/../../core/Auth.php';
requireRole('board');
$pageTitle = 'Bảng Giám đốc (Board)';
$current_page = 'dashboard';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Báo cáo Ban Giám Đốc</h2>
        <p class="text-muted text-xs mb-0">Theo dõi doanh thu, bảng xếp hạng và toàn cảnh hoạt động xuất bản.</p>
    </div>
    <button class="btn btn-success shadow-sm"><i class="fas fa-file-invoice-dollar me-2"></i>Tải Báo cáo Tài chính</button>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-4 col-md-6">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Series đánh giá</div>
                        <div class="h3 mb-0 fw-bold"><?= isset($evaluatedSeries) ? $evaluatedSeries : 0 ?></div>
                    </div>
                    <div class="stat-icon primary">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="card stat-card info h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Lượt Voting</div>
                        <div class="h3 mb-0 fw-bold"><?= isset($totalVoting) ? $totalVoting : 0 ?></div>
                    </div>
                    <div class="stat-icon info">
                        <i class="fas fa-vote-yea"></i>
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
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Top Ranking</div>
                        <div class="h5 mb-0 fw-bold"><?= isset($topRankingSeries) ? $topRankingSeries : "Chưa có dữ liệu" ?></div>
                    </div>
                    <div class="stat-icon warning">
                        <i class="fas fa-trophy"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="m-0"><i class="fas fa-list-ol text-primary me-2"></i>Bảng xếp hạng (Ranking)</h6>
            </div>
            <div class="card-body text-center py-5">
                <p class="text-muted mb-0">Chưa có dữ liệu</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="m-0"><i class="fas fa-poll text-primary me-2"></i>Kết quả Voting</h6>
            </div>
            <div class="card-body text-center py-5">
                <p class="text-muted mb-0">Chưa có dữ liệu</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="m-0"><i class="fas fa-star text-primary me-2"></i>Series đang đánh giá</h6>
            </div>
            <div class="card-body text-center py-5">
                <p class="text-muted mb-0">Chưa có dữ liệu</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <?php require_once __DIR__ . '/../shared/dashboard_notifications.php'; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
