<?php 
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
    <div class="col-xl-4 col-md-6">
        <div class="card stat-card info h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Truyện Đang Tiến Hành</div>
                        <div class="h3 mb-0 fw-bold">2</div>
                    </div>
                    <div class="stat-icon info">
                        <i class="fas fa-pen-nib"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="card stat-card success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Chương Đã Xuất Bản</div>
                        <div class="h3 mb-0 fw-bold">128</div>
                    </div>
                    <div class="stat-icon success">
                        <i class="fas fa-check-circle"></i>
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
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Phản Hồi Mới</div>
                        <div class="h3 mb-0 fw-bold">5</div>
                    </div>
                    <div class="stat-icon warning">
                        <i class="fas fa-comment-dots"></i>
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
                <h6 class="m-0"><i class="fas fa-history text-primary me-2"></i>Lịch sử Bản thảo Gần Đây</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="text-muted text-xs text-uppercase bg-light">
                            <tr>
                                <th>Tên Truyện</th>
                                <th>Chương</th>
                                <th>Ngày nộp</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold">Hành Trình Vô Tận</td>
                                <td>Chương 42</td>
                                <td>10/10/2023</td>
                                <td><span class="badge bg-warning text-dark px-2 py-1 rounded-pill">Đang chờ duyệt</span></td>
                                <td><button class="btn btn-sm btn-light border-0"><i class="fas fa-eye"></i> Xem</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
