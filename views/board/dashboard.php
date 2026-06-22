<?php 
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
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Tổng Doanh Thu (Tháng)</div>
                        <div class="h3 mb-0 fw-bold">$2.5M</div>
                    </div>
                    <div class="stat-icon primary">
                        <i class="fas fa-dollar-sign"></i>
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
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Truyện đang Bán</div>
                        <div class="h3 mb-0 fw-bold">45</div>
                    </div>
                    <div class="stat-icon success">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card info h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Truyện Top 1</div>
                        <div class="h5 mb-0 fw-bold">One Piece</div>
                    </div>
                    <div class="stat-icon info">
                        <i class="fas fa-crown"></i>
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
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Cần Duyệt Ngân Sách</div>
                        <div class="h3 mb-0 fw-bold">2</div>
                    </div>
                    <div class="stat-icon warning">
                        <i class="fas fa-file-signature"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0"><i class="fas fa-chart-bar text-primary me-2"></i>Báo Cáo Tăng Trưởng Doanh Số</h6>
            </div>
            <div class="card-body" style="height: 300px; display: flex; align-items: center; justify-content: center; background-color: #f8fafc; border-radius: 8px;">
                <p class="text-muted"><i class="fas fa-chart-bar me-2"></i>Khu vực tích hợp biểu đồ (E.g. Chart.js, ApexCharts)</p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
