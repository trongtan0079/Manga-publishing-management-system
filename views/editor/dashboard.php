<?php 
require_once __DIR__ . '/../../core/Auth.php';
requireRole('editor');
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
    <div class="col-xl-6 col-md-6">
        <div class="card stat-card warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Submissions chờ review</div>
                        <div class="h3 mb-0 fw-bold text-dark"><?= isset($pendingSubmissions) ? $pendingSubmissions : 0 ?></div>
                    </div>
                    <div class="stat-icon warning">
                        <i class="fas fa-inbox"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-md-6">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Reviews gần đây</div>
                        <div class="h3 mb-0 fw-bold text-dark"><?= isset($recentReviews) ? $recentReviews : 0 ?></div>
                    </div>
                    <div class="stat-icon primary">
                        <i class="fas fa-eye"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0"><i class="fas fa-clipboard-check text-primary me-2"></i>Danh sách Submissions chờ review</h6>
            </div>
            <div class="card-body text-center py-5">
                <p class="text-muted mb-0">Chưa có dữ liệu</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0"><i class="fas fa-history text-primary me-2"></i>Danh sách Reviews gần đây</h6>
            </div>
            <div class="card-body text-center py-5">
                <p class="text-muted mb-0">Chưa có dữ liệu</p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
