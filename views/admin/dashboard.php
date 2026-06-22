<?php 
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
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Tổng số Người dùng</div>
                        <div class="h3 mb-0 fw-bold">150</div>
                    </div>
                    <div class="stat-icon primary">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="mt-3 text-xs">
                    <span class="text-success fw-bold"><i class="fas fa-arrow-up me-1"></i>12%</span>
                    <span class="text-muted ms-1">so với tháng trước</span>
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
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Truyện đang xuất bản</div>
                        <div class="h3 mb-0 fw-bold">45</div>
                    </div>
                    <div class="stat-icon success">
                        <i class="fas fa-book-open"></i>
                    </div>
                </div>
                <div class="mt-3 text-xs">
                    <span class="text-success fw-bold"><i class="fas fa-arrow-up me-1"></i>3</span>
                    <span class="text-muted ms-1">bộ truyện mới</span>
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
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Chờ duyệt (Review)</div>
                        <div class="h3 mb-0 fw-bold">18</div>
                    </div>
                    <div class="stat-icon warning">
                        <i class="fas fa-comments"></i>
                    </div>
                </div>
                <div class="mt-3 text-xs">
                    <span class="text-danger fw-bold"><i class="fas fa-exclamation-circle me-1"></i>Cần xử lý</span>
                    <span class="text-muted ms-1">trong 24h tới</span>
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
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Cảnh báo hệ thống</div>
                        <div class="h3 mb-0 fw-bold">3</div>
                    </div>
                    <div class="stat-icon danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
                <div class="mt-3 text-xs">
                    <span class="text-muted">Kiểm tra log lỗi ngay</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0"><i class="fas fa-chart-area text-primary me-2"></i>Biểu đồ Lượt xem Toàn hệ thống</h6>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light border-0 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        7 ngày qua
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item" href="#">7 ngày qua</a></li>
                        <li><a class="dropdown-item" href="#">30 ngày qua</a></li>
                        <li><a class="dropdown-item" href="#">Năm nay</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body" style="height: 300px; display: flex; align-items: center; justify-content: center; background-color: #f8fafc; border-radius: 8px;">
                <p class="text-muted"><i class="fas fa-chart-line me-2"></i>Biểu đồ sẽ hiển thị ở đây (Cần thư viện Chart.js)</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4 h-100">
            <div class="card-header">
                <h6 class="m-0"><i class="fas fa-list text-primary me-2"></i>Hoạt động Gần đây</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush border-0">
                    <div class="list-group-item px-4 py-3 border-bottom-0">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-upload"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-dark" style="font-size: 0.9rem;">Chương 105 - One Piece</h6>
                                <p class="mb-0 text-muted text-xs">Đã được nộp bởi <strong>mangaka_user</strong></p>
                            </div>
                            <small class="text-muted text-xs">10 p trước</small>
                        </div>
                    </div>
                    <!-- Additional List Items would go here -->
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
