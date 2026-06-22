<?php 
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
    <div class="col-xl-6 col-md-6">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Công việc Đang Giao</div>
                        <div class="h3 mb-0 fw-bold">12</div>
                    </div>
                    <div class="stat-icon primary">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-md-6">
        <div class="card stat-card success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Đã Hoàn Thành (Tháng này)</div>
                        <div class="h3 mb-0 fw-bold">8</div>
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
        <div class="card">
            <div class="card-header">
                <h6 class="m-0"><i class="fas fa-tasks text-primary me-2"></i>Nhiệm vụ Cần xử lý</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush border-0 rounded-bottom">
                    <div class="list-group-item p-4">
                        <div class="d-flex w-100 justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 fw-bold">Vẽ bối cảnh - Chương 45 (Hành Trình Vô Tận)</h6>
                            <span class="badge bg-danger rounded-pill">Hạn chót: Hôm nay</span>
                        </div>
                        <p class="mb-2 text-muted text-sm">Vẽ bối cảnh thành phố hoang tàn từ trang 10 đến trang 15. Chú ý đổ bóng.</p>
                        <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-sm btn-primary px-3 rounded-pill">Cập nhật tiến độ</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
