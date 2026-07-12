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
    <h2 class="h3 mb-1 d-flex align-items-center gap-2">
        Chi tiết Đánh giá #<?= $review['review_id'] ?>
        <?php if (!empty($submission['task_id'])): ?>
            <span class="badge bg-info-subtle text-info border border-info-subtle fs-6">Task Drawing</span>
        <?php else: ?>
            <?php if (isset($submission['chapter_status']) && $submission['chapter_status'] === 'reviewing_final'): ?>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-6">Bản Hoàn Chỉnh</span>
            <?php elseif (isset($submission['chapter_status']) && $submission['chapter_status'] === 'reviewing_draft'): ?>
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle fs-6">Bản Nháp</span>
            <?php else: ?>
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-6">Chapter Submission</span>
            <?php endif; ?>
        <?php endif; ?>
    </h2>
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
                    <div class="p-4 bg-white rounded border border-slate-200 shadow-sm" style="font-size: 1.05rem; line-height: 1.6;">
                        <?= nl2br(htmlspecialchars((string)($review['comments'] ?? ''))) ?>
                    </div>
                </div>
                
                
                <?php if (!empty($annotatedPages)): ?>
                <!-- Phần 3: Các trang bị đánh dấu lỗi -->
                <div class="mt-5">
                    <h6 class="fw-bold text-dark mb-4 border-bottom pb-2"><i class="fas fa-images me-2 text-danger"></i>Các trang có lỗi cần sửa:</h6>
                    
                    <div class="row g-4">
                        <?php foreach ($annotatedPages as $ap): 
                            $page = $ap['page'];
                            $annotations = $ap['annotations'];
                            
                            $imageUrl = $page['image_url'] ?? '';
                            $resolvedImage = (strpos($imageUrl, 'http') === 0) ? $imageUrl : BASE_PATH . '/' . ltrim($imageUrl, '/');
                        ?>
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="card h-100 shadow-sm border-0 bg-light">
                                <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                                    <h6 class="fw-bold text-dark mb-0">Trang <?= htmlspecialchars($page['page_number']) ?></h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="position-relative d-inline-block shadow-sm mb-3" style="max-width: 100%; border: 1px solid #ddd; background-color: #fff;">
                                        <img src="<?= htmlspecialchars($resolvedImage) ?>" class="img-fluid" style="max-height: 400px; display: block;" alt="Annotated Page <?= $page['page_number'] ?>">
                                        
                                        <?php foreach ($annotations as $index => $ann): 
                                            // Chuyển đổi tọa độ gốc (theo khung chuẩn 800x1000) sang phần trăm
                                            $l = ($ann['x'] / 800) * 100;
                                            $t = ($ann['y'] / 1000) * 100;
                                            $w = ($ann['width'] / 800) * 100;
                                            $h = ($ann['height'] / 1000) * 100;
                                        ?>
                                            <div style="position: absolute; left: <?= $l ?>%; top: <?= $t ?>%; width: <?= $w ?>%; height: <?= $h ?>%; border: 3px solid #dc3545; background-color: rgba(220, 53, 69, 0.2); pointer-events: none;">
                                                <span class="badge bg-danger position-absolute top-0 start-0 translate-middle" style="font-size: 0.7rem; pointer-events: auto; z-index: 10;"><?= $index + 1 ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <div class="text-start bg-white p-3 rounded border border-slate-200">
                                        <p class="text-muted text-xs fw-bold mb-2 text-uppercase">Chi tiết lỗi:</p>
                                        <ul class="list-unstyled mb-0 text-sm">
                                            <?php foreach ($annotations as $index => $ann): ?>
                                                <li class="mb-2 pb-2 <?= $index < count($annotations) - 1 ? 'border-bottom border-slate-100' : '' ?>">
                                                    <span class="badge bg-danger me-2"><?= $index + 1 ?></span>
                                                    <span class="text-dark"><?= htmlspecialchars($ann['comments']) ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="mt-5 text-center">
                    <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=show&id=<?= $submission['submission_id'] ?>" class="btn btn-outline-primary shadow-sm px-4">
                        <i class="fas fa-eye me-2"></i>Xem lại Bản thảo gốc
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
