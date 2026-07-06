<?php
if (!defined('BASE_PATH')) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $pos = strpos($scriptName, '/views/');
    $projectUrl = ($pos !== false) ? substr($scriptName, 0, $pos) : '';
    header('Location: ' . $projectUrl . '/index.php');
    exit;
}
/**
 * @var array $review
 * @var array $submission
 */
$pageTitle = 'Chi tiết Đánh giá';
$current_page = 'reviews';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="mb-4">
    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm mb-3 shadow-sm">
        <i class="fas fa-arrow-left me-1"></i> Quay lại
    </a>
    <h2 class="h3 mb-1">Chi tiết Đánh giá #<?= $review['review_id'] ?></h2>
    <p class="text-muted">Bản thảo ID: <span class="fw-bold">#<?= $submission['submission_id'] ?></span> - Người gửi: <span class="fw-bold"><?= htmlspecialchars((string)($submission['sender_name'] ?? '')) ?></span></p>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white text-dark py-3 border-bottom border-light d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="fas fa-comment-dots me-2 text-primary"></i>Kết quả Đánh giá</h5>
                <span class="badge bg-light text-dark shadow-sm"><?= date('d/m/Y H:i', strtotime($review['created_at'])) ?></span>
            </div>
            <div class="card-body p-4">
                <!-- Phần 1: Hiển thị trạng thái quyết định và điểm số -->
                <div class="row mb-4 bg-light p-3 rounded">
                    <div class="col-md-6 border-end">
                        <h6 class="fw-bold text-muted mb-2 text-uppercase" style="font-size: 0.8rem;">Trạng thái quyết định</h6>
                        <?php if ($submission['status'] === 'approved'): ?>
                            <div class="fs-5 text-success fw-bold"><i class="fas fa-check-circle me-2"></i>Đã phê duyệt (Approved)</div>
                        <?php elseif ($submission['status'] === 'rejected'): ?>
                            <div class="fs-5 text-danger fw-bold"><i class="fas fa-times-circle me-2"></i>Từ chối (Rejected)</div>
                        <?php else: ?>
                            <?php 
                                $statusText = $submission['status'];
                                if ($submission['status'] === 'pending') $statusText = 'Chờ duyệt';
                                elseif ($submission['status'] === 'reviewed') $statusText = 'Đang đánh giá';
                            ?>
                            <div class="fs-5 text-secondary fw-bold"><?= htmlspecialchars($statusText) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 ps-4">
                        <h6 class="fw-bold text-muted mb-2 text-uppercase" style="font-size: 0.8rem;">Điểm số</h6>
                        <?php if ($review['rating']): ?>
                            <div class="fs-3 fw-bold text-warning"><i class="fas fa-star me-2"></i><?= $review['rating'] ?><span class="text-muted fs-5">/10</span></div>
                        <?php else: ?>
                            <span class="text-muted fst-italic">Không đánh giá điểm</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Phần 2: Nội dung nhận xét chi tiết (Comments) -->
                <div class="mt-4">
                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2"><i class="fas fa-quote-left me-2 text-primary"></i>Nhận xét từ Editor/Mangaka:</h6>
                    <div class="p-4 bg-white rounded border-start border-4 border-primary shadow-sm" style="font-size: 1.05rem; line-height: 1.6;">
                        <?= nl2br(htmlspecialchars((string)($review['comments'] ?? ''))) ?>
                    </div>
                </div>
                
                <div class="mt-5 text-center">
                    <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=show&id=<?= $submission['submission_id'] ?>" class="btn btn-outline-primary shadow-sm">
                        <i class="fas fa-eye me-2"></i>Xem lại Bản thảo gốc
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
