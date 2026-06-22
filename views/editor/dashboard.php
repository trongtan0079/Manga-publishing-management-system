<?php 
$pageTitle = 'Góc Biên tập (Editor)';
$current_page = 'dashboard';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Kiểm duyệt Bản thảo</h2>
        <p class="text-muted text-xs mb-0">Theo dõi, phản hồi và duyệt các chương truyện được nộp từ tác giả.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-4 col-md-6">
        <div class="card stat-card warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Chờ duyệt</div>
                        <div class="h3 mb-0 fw-bold text-dark">7</div>
                    </div>
                    <div class="stat-icon warning">
                        <i class="fas fa-inbox"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Đã phản hồi (Tuần này)</div>
                        <div class="h3 mb-0 fw-bold text-dark">24</div>
                    </div>
                    <div class="stat-icon primary">
                        <i class="fas fa-eye"></i>
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
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Đã chấp thuận</div>
                        <div class="h3 mb-0 fw-bold text-dark">18</div>
                    </div>
                    <div class="stat-icon success">
                        <i class="fas fa-thumbs-up"></i>
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
                <h6 class="m-0"><i class="fas fa-clipboard-check text-primary me-2"></i>Danh sách chờ Duyệt gấp</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted text-xs text-uppercase">
                            <tr>
                                <th class="ps-4">Tác phẩm</th>
                                <th>Tác giả</th>
                                <th>Thời gian nộp</th>
                                <th class="text-end pe-4">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-4 fw-bold">Chương 105 - One Piece</td>
                                <td>mangaka_user</td>
                                <td>2 giờ trước</td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-primary rounded-pill px-3">Bắt đầu Duyệt</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
