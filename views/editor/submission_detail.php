<?php
if (!defined('BASE_PATH')) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $pos = strpos($scriptName, '/views/');
    $projectUrl = ($pos !== false) ? substr($scriptName, 0, $pos) : '';
    header('Location: ' . $projectUrl . '/index.php');
    exit;
}
/**
 * View: Giao diện chi tiết thông tin bản thảo đã nộp (submission_detail.php)
 * Vai trò: Các vai trò liên quan (Editor, Mangaka, Assistant)
 * Chức năng: Hiển thị chi tiết file bản vẽ nộp lên, ghi chú, người gửi, thông tin kiểm duyệt và các đánh giá (Review) đi kèm.
 * 
 * @var array $submission Thông tin chi tiết của bản thảo đang xem
 */
$pageTitle = 'Chi tiết bản thảo nộp';
$current_page = 'submissions';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';

$role = $_SESSION['role_name'] ?? '';

// Nếu nộp bản thảo Chapter, truy vấn các trang của Chapter đó để ghi chú lỗi/xem lỗi
$pages = [];
if (!empty($submission['chapter_id'])) {
    require_once __DIR__ . '/../../models/Page.php';
    $pageModel = new Page();
    $pages = $pageModel->findByChapterIdWithAnnotationCount($submission['chapter_id']);
}

// Xác định URL quay lại thông minh (Danh sách bản thảo hay Danh sách chờ đánh giá)
$backUrl = BASE_PATH . '/index.php?controller=submission&action=index';
if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'controller=review') !== false) {
    $backUrl = BASE_PATH . '/index.php?controller=review&action=index';
} elseif ($role === 'editor') {
    // Dự phòng nếu không có HTTP_REFERER (Editor mặc định quay lại danh sách review chờ xử lý)
    $backUrl = BASE_PATH . '/index.php?controller=review&action=index';
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <?php if (!empty($submission['series_title'])): ?>
            <div class="text-xs text-primary fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px;">
                <a href="<?= BASE_PATH ?>/index.php?controller=series&action=show&id=<?= htmlspecialchars($submission['series_id']) ?>" class="text-primary text-decoration-none hover-underline">
                    <i class="fas fa-book me-1"></i><?= htmlspecialchars((string)$submission['series_title']) ?> 
                </a>
                <?php if (!empty($submission['chapter_number'])): ?>
                    <span class="text-slate-300 mx-1.5">&middot;</span> Chapter <?= htmlspecialchars((string)$submission['chapter_number']) ?>
                    <?php if (!empty($submission['chapter_title'])): ?>
                        <span class="text-slate-400 fw-normal"> (<?= htmlspecialchars((string)$submission['chapter_title']) ?>)</span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <h2 class="h3 mb-1">
            <?= $role === 'assistant' ? 'Chi tiết Sản phẩm đã nộp' : 'Chi tiết Bản thảo nộp' ?>
        </h2>
        <p class="text-muted text-xs mb-0">Xem thông tin tệp gửi lên, ghi chú, người thực hiện và trạng thái phê duyệt.</p>
    </div>
    <a href="<?= $backUrl ?>" class="btn btn-outline-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
    </a>
</div>

<div class="row">
    <!-- Cột bên trái: Hiển thị Preview File / File info, Ghi chú, Phản hồi -->
    <div class="col-lg-8 mb-4">
        <!-- Card Xem trước sản phẩm -->
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-white text-dark py-3 border-bottom border-light d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="fas fa-file-image me-2 text-primary"></i>Xem trước sản phẩm</h5>
                <?php 
                $fileUrl = (strpos((string)($submission['file_url'] ?? ''), 'http') === 0) ? $submission['file_url'] : BASE_PATH . '/' . ltrim((string)($submission['file_url'] ?? ''), '/');
                $ext = strtolower(pathinfo($submission['file_url'], PATHINFO_EXTENSION));
                $isImage = in_array($ext, ['png', 'jpg', 'jpeg']);
                ?>
                <?php if ($isImage || $ext === 'pdf'): ?>
                    <a href="<?= htmlspecialchars((string)($fileUrl ?? '')) ?>" target="_blank" class="btn btn-xs btn-outline-primary py-1 px-2.5" style="border-radius: 6px; font-size: 11px;">
                        <i class="fas fa-external-link-alt me-1"></i>Mở rộng/Tab mới
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body p-4 bg-light text-center" style="min-height: 400px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <?php if ($isImage): ?>
                    <div class="w-100 bg-white p-3 rounded border shadow-sm d-flex flex-column align-items-center justify-content-center">
                        <div id="subAnnoWrapper" class="position-relative d-inline-block text-start shadow-sm" style="border: 1px solid #cbd5e1; user-select: none; max-width: 100%;">
                            <img id="subAnnoImage" src="<?= htmlspecialchars((string)($fileUrl ?? '')) ?>" 
                                 onerror="this.onerror=null; this.src='uploads/submissions/<?= htmlspecialchars(basename($submission['file_url'])) ?>';"
                                 alt="Bản thảo" 
                                 class="img-fluid rounded" 
                                 style="max-height: 650px; width: auto; object-fit: contain; display: block;">
                            <!-- Overlay vẽ ghi chú lỗi -->
                            <div id="subAnnoOverlayContainer" class="position-absolute top-0 start-0 w-100 h-100" style="pointer-events: auto; cursor: default;"></div>
                        </div>
                    </div>
                <?php elseif ($ext === 'pdf'): ?>
                    <div class="text-center py-5 bg-white rounded border shadow-sm w-100">
                        <i class="fas fa-file-pdf text-danger fa-5x mb-3 animate__animated animate__pulse animate__infinite"></i>
                        <h5 class="fw-bold">Tài liệu PDF hoàn chỉnh</h5>
                        <p class="text-muted text-sm px-3">Bạn có thể tải về hoặc xem trực tiếp bằng trình đọc PDF của trình duyệt.</p>
                        <a href="<?= htmlspecialchars((string)($fileUrl ?? '')) ?>" target="_blank" class="btn btn-danger px-4 mt-2 shadow-sm fw-bold btn-sm">
                            <i class="fas fa-external-link-alt me-2"></i>Mở trong Tab Mới
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 bg-white rounded border shadow-sm w-100">
                        <i class="fas fa-file-archive text-warning fa-5x mb-3"></i>
                        <h5 class="fw-bold">Tệp tin nén (ZIP)</h5>
                        <p class="text-muted text-sm px-3">Tệp tin này chứa nhiều trang vẽ hoặc tài liệu được nén lại.</p>
                        <a href="<?= htmlspecialchars((string)($fileUrl ?? '')) ?>" download class="btn btn-warning text-dark px-4 mt-2 fw-bold btn-sm shadow-sm">
                            <i class="fas fa-download me-2"></i>Tải Tệp ZIP
                        </a>
                    </div>
                <?php endif; ?>

                <div class="mt-4 pt-3 border-top w-100 d-flex justify-content-between align-items-center text-start">
                    <div>
                        <small class="text-muted d-block text-xs">Tên file vật lý:</small>
                        <strong class="text-dark text-xs"><?= htmlspecialchars(basename($submission['file_url'] ?? '')) ?></strong>
                    </div>
                    <div>
                        <a href="<?= htmlspecialchars((string)($fileUrl ?? '')) ?>" download class="btn btn-outline-dark btn-sm shadow-sm py-1.5 px-3" style="font-size: 12px; border-radius: 6px;">
                            <i class="fas fa-download me-1.5"></i>Tải xuống bản gốc
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Ghi chú lỗi trực quan trên bản vẽ -->
        <?php if ($isImage && !empty($submission['task_id'])): ?>
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-white text-dark py-3 border-bottom border-light">
                <h5 class="card-title mb-0"><i class="fas fa-draw-polygon me-2 text-danger"></i>Ghi chú lỗi trực quan trên bản vẽ</h5>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-7 mb-3 mb-md-0">
                        <p class="text-muted text-xs mb-0">
                            <?php if ($role === 'mangaka' && $submission['status'] === 'pending'): ?>
                                <i class="fas fa-info-circle me-1 text-primary"></i><strong>Hướng dẫn:</strong> Nhấn giữ và kéo chuột vẽ khung đỏ trên hình ảnh xem trước bên trên, sau đó nhập nội dung ghi chú ở form bên phải.
                            <?php else: ?>
                                <i class="fas fa-info-circle me-1 text-primary"></i>Rê chuột vào các ô khoanh đỏ trên ảnh bản thảo hoặc xem danh sách bên dưới để xem nhận xét của Tác giả.
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-5">
                        <?php if ($role === 'mangaka' && $submission['status'] === 'pending'): ?>
                            <div id="sub-no-selection-warning" class="alert alert-warning py-2 px-3 mb-0" style="font-size: 0.8rem; border-radius: 8px;">
                                <i class="fas fa-mouse-pointer me-1"></i>Vui lòng vẽ một khung lỗi trên ảnh bên trên để nhập ghi chú.
                            </div>
                            <form id="subAnnoForm" style="display: none;" class="mb-0">
                                <input type="hidden" id="sub-anno-x">
                                <input type="hidden" id="sub-anno-y">
                                <input type="hidden" id="sub-anno-w">
                                <input type="hidden" id="sub-anno-h">
                                
                                <div class="mb-2">
                                    <textarea class="form-control form-control-sm" id="sub-anno-comment" rows="2" required placeholder="Nhập ghi chú sửa đổi cho vùng vẽ này..." style="border-radius: 8px; font-size: 0.825rem;"></textarea>
                                </div>
                                <button type="submit" class="btn btn-xs btn-danger fw-bold py-1.5 w-100" style="border-radius: 6px; font-size: 11px;">
                                    <i class="fas fa-save me-1"></i>Lưu ghi chú vùng vẽ
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="border-top pt-3 mt-3">
                    <h6 class="fw-bold text-dark mb-2.5 text-sm"><i class="fas fa-list me-1.5 text-muted"></i>Danh sách ghi chú sửa đổi:</h6>
                    <div id="sub-anno-list" class="list-group list-group-flush border rounded-3 bg-white overflow-hidden">
                        <!-- Sẽ load bằng JS -->
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Card Ghi chú kèm theo -->
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-white text-dark py-3 border-bottom border-light">
                <h5 class="card-title mb-0"><i class="fas fa-sticky-note me-2 text-primary"></i>Ghi chú kèm theo</h5>
            </div>
            <div class="card-body p-4">
                <div class="bg-light p-3 rounded text-dark text-sm border border-slate-200" style="white-space: pre-line; line-height: 1.6;">
                    <?= !empty($submission['notes']) ? htmlspecialchars((string)($submission['notes'] ?? '')) : '<em>Không có ghi chú nào đi kèm.</em>' ?>
                </div>
            </div>
        </div>

        <!-- Card Phản hồi / Đánh giá -->
        <?php if (!empty($reviews)): ?>
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-white text-dark py-3 border-bottom border-light">
                <h5 class="card-title mb-0"><i class="fas fa-comments me-2 text-primary"></i>Phản hồi / Đánh giá</h5>
            </div>
            <div class="card-body p-4">
                <?php foreach ($reviews as $review): ?>
                    <div class="border rounded p-3 mb-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark"><i class="fas fa-user-edit me-2"></i>Đánh giá từ Biên tập viên/Mangaka</span>
                            <small class="text-muted"><i class="far fa-clock me-1"></i><?= htmlspecialchars(date('d/m/Y H:i', strtotime($review['created_at']))) ?></small>
                        </div>
                        
                        <div class="mb-2">
                            <strong class="text-muted text-xs text-uppercase">Nội dung đánh giá:</strong>
                            <p class="mb-2 mt-1 bg-white p-2 border rounded" style="white-space: pre-line;"><?= htmlspecialchars($review['comments']) ?></p>
                        </div>

                        <?php if (!empty($review['rating'])): ?>
                            <div class="mb-0">
                                <strong class="text-muted text-xs text-uppercase">Chấm điểm:</strong>
                                <span class="badge bg-primary fs-6"><?= htmlspecialchars($review['rating']) ?> / 10</span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Cột bên phải: Metadata & Hành động -->
    <div class="col-lg-4 mb-4">
        <!-- Card Thông tin nộp bài -->
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-white text-dark py-3 border-bottom border-light">
                <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Thông tin nộp bài</h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-4">
                    <small class="text-muted d-block text-uppercase text-xs fw-bold mb-2">Người thực hiện</small>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2.5 shadow-sm" style="width: 38px; height: 38px; font-weight: bold; font-size: 15px;">
                            <?= strtoupper(substr($submission['sender_name'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div>
                            <span class="d-block fw-bold text-dark" style="font-size: 14.5px;"><?= htmlspecialchars((string)($submission['sender_name'] ?? 'Không rõ')) ?></span>
                            <span class="text-muted text-xs d-block"><?= htmlspecialchars($role === 'assistant' ? 'Trợ lý (Assistant)' : 'Tác giả (Mangaka)') ?></span>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <small class="text-muted d-block text-uppercase text-xs fw-bold mb-1">Loại nộp bài</small>
                        <?php if (!empty($submission['task_id'])): ?>
                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1.5 text-xs w-100 text-center">Nhiệm vụ vẽ</span>
                        <?php else: ?>
                            <?php if (isset($submission['chapter_status']) && ($submission['chapter_status'] === 'reviewing_final' || $submission['chapter_status'] === 'reviewing')): ?>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1.5 text-xs w-100 text-center">Bản hoàn thiện</span>
                            <?php elseif (isset($submission['chapter_status']) && $submission['chapter_status'] === 'reviewing_draft'): ?>
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1.5 text-xs w-100 text-center">Kịch bản thô</span>
                            <?php else: ?>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1.5 text-xs w-100 text-center">Chương truyện</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block text-uppercase text-xs fw-bold mb-1">Trạng thái</small>
                        <?php 
                        $statusClass = 'bg-secondary';
                        $statusLabel = 'Chờ duyệt';
                        if ($submission['status'] === 'reviewed') {
                            $statusClass = 'bg-info';
                            $statusLabel = 'Đang đánh giá';
                        } elseif ($submission['status'] === 'approved') {
                            $statusClass = 'bg-success';
                            $statusLabel = 'Đã duyệt';
                        } elseif ($submission['status'] === 'rejected') {
                            $statusClass = 'bg-danger';
                            $statusLabel = 'Từ chối';
                        } elseif ($submission['status'] === 'pending') {
                            $statusClass = 'bg-warning text-dark';
                            $statusLabel = 'Chờ duyệt';
                        }
                        ?>
                        <span class="badge <?= $statusClass ?> px-2 py-1.5 text-xs w-100 text-center"><?= $statusLabel ?></span>
                    </div>
                </div>

                <div class="mb-3 pt-2 border-top">
                    <small class="text-muted d-block text-uppercase text-xs fw-bold mb-1">Series tác phẩm</small>
                    <a href="<?= BASE_PATH ?>/index.php?controller=series&action=show&id=<?= htmlspecialchars($submission['series_id']) ?>" class="fw-bold text-primary text-decoration-none hover-underline text-sm">
                        <i class="fas fa-book text-muted me-1.5"></i><?= htmlspecialchars((string)($submission['series_title'] ?? 'Không xác định')) ?>
                    </a>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block text-uppercase text-xs fw-bold mb-1">Chương hoặc Task cụ thể</small>
                    <span class="text-dark text-sm">
                        <?php if (!empty($submission['task_id'])): ?>
                            <i class="fas fa-tasks text-muted me-1.5"></i><?= htmlspecialchars((string)($submission['task_title'] ?? '')) ?>
                        <?php else: ?>
                            <a href="<?= BASE_PATH ?>/index.php?controller=chapter&action=show&id=<?= htmlspecialchars($submission['chapter_id']) ?>" class="fw-bold text-primary text-decoration-none hover-underline">
                                <i class="fas fa-layer-group me-1.5"></i>Chương <?= htmlspecialchars((string)($submission['chapter_number'] ?? '')) ?> - <?= htmlspecialchars((string)($submission['chapter_title'] ?? 'Chưa đặt tên')) ?>
                            </a>
                        <?php endif; ?>
                    </span>
                </div>

                <div class="mb-0 pt-2 border-top">
                    <small class="text-muted d-block text-uppercase text-xs fw-bold mb-1">Thời gian nộp</small>
                    <span class="text-dark text-sm"><i class="far fa-calendar-alt text-muted me-1.5"></i><?= htmlspecialchars(date('d/m/Y H:i:s', strtotime($submission['submitted_at'] ?? 'now'))) ?></span>
                </div>
            </div>
        </div>

        <!-- Card Yêu cầu công việc ban đầu -->
        <?php if (!empty($submission['task_id'])): ?>
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-white text-dark py-3 border-bottom border-light">
                <h5 class="card-title mb-0"><i class="fas fa-clipboard-list me-2 text-primary"></i>Yêu cầu công việc</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <small class="text-muted d-block text-xs mb-1">Loại việc:</small>
                        <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1.5 text-xs w-100 text-center"><?= htmlspecialchars((string)($submission['task_type'] ?? 'Không rõ')) ?></span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block text-xs mb-1">Độ ưu tiên:</small>
                        <?php 
                        $priClass = 'secondary';
                        $priLabel = 'Bình thường';
                        if ($submission['task_priority'] == 'high') { $priClass = 'danger'; $priLabel = 'Cao'; }
                        elseif ($submission['task_priority'] == 'low') { $priClass = 'info'; $priLabel = 'Thấp'; }
                        ?>
                        <span class="badge bg-<?= $priClass ?>-subtle text-<?= $priClass ?> px-2.5 py-1.5 text-xs w-100 text-center"><?= $priLabel ?></span>
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block text-xs mb-1">Hạn chót:</small>
                    <span class="text-dark text-sm fw-bold"><i class="far fa-clock me-1.5 text-muted"></i><?= !empty($submission['task_due_date']) ? date('d/m/Y H:i', strtotime($submission['task_due_date'])) : 'Không có' ?></span>
                </div>

                <div class="mb-3 pt-2 border-top">
                    <small class="text-muted d-block text-xs fw-bold mb-2">Mô tả / Yêu cầu chi tiết:</small>
                    <div class="text-dark text-sm bg-white p-3 rounded border border-slate-200 quill-content-render" style="max-height: 250px; overflow-y: auto; line-height: 1.6; word-break: break-word; overflow-wrap: break-word;">
                        <?= !empty($submission['task_description']) ? $submission['task_description'] : '<em>Không có mô tả chi tiết.</em>' ?>
                    </div>
                </div>

                <?php if (!empty($submission['page_image_url']) && isset($submission['region_x'])): ?>
                <div class="mb-0 pt-2 border-top">
                    <small class="text-muted d-block text-xs fw-bold mb-2">Vị trí phân vùng trên bản nháp gốc:</small>
                    <?php 
                        $imageUrl = $submission['page_image_url'];
                        $resolvedImage = (strpos($imageUrl, 'http') === 0) ? $imageUrl : BASE_PATH . '/' . ltrim($imageUrl, '/');
                        $l = ($submission['region_x'] / 800) * 100;
                        $t = ($submission['region_y'] / 1000) * 100;
                        $w = ($submission['region_width'] / 800) * 100;
                        $h = ($submission['region_height'] / 1000) * 100;
                    ?>
                    <div class="text-center bg-white p-2 rounded border border-slate-200 overflow-hidden">
                        <div class="position-relative d-inline-block shadow-sm" style="max-width: 100%; border: 1px solid #ddd;">
                            <img src="<?= htmlspecialchars($resolvedImage) ?>" class="img-fluid" style="max-height: 350px; display: block;" alt="Page Reference">
                            <div style="position: absolute; left: <?= $l ?>%; top: <?= $t ?>%; width: <?= $w ?>%; height: <?= $h ?>%; border: 3px solid #0d6efd; background-color: rgba(13, 110, 253, 0.2); pointer-events: none; box-shadow: 0 0 0 9999px rgba(0,0,0,0.4);"></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Card Hành động khả dụng -->
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white text-dark py-3 border-bottom border-light">
                <h5 class="card-title mb-0"><i class="fas fa-cogs me-2 text-primary"></i>Hành động khả dụng</h5>
            </div>
            <div class="card-body p-4 text-center">
                <?php if ($role === 'editor' && !empty($submission['chapter_id'])): ?>
                    <p class="text-muted text-xs mb-3">Chuyển sang module đánh giá để ghi nhận xét, chấm điểm hoặc phê duyệt chương truyện này.</p>
                    <a href="<?= BASE_PATH ?>/index.php?controller=review&action=create&submission_id=<?= $submission['submission_id'] ?>" class="btn btn-success w-100 py-2.5 shadow-sm fw-bold" style="border-radius: 8px;">
                        <i class="fas fa-clipboard-check me-2"></i>Chuyển sang Review (Đánh giá)
                    </a>
                <?php elseif ($role === 'mangaka' && !empty($submission['task_id']) && $submission['mangaka_id'] == $_SESSION['user_id'] && $submission['status'] === 'pending'): ?>
                    <p class="text-muted text-xs mb-3">Đánh giá bản thảo nộp từ trợ lý (Assistant) của bạn để phê duyệt hoàn thành công việc.</p>
                    <a href="<?= BASE_PATH ?>/index.php?controller=review&action=create&submission_id=<?= $submission['submission_id'] ?>" class="btn btn-success w-100 py-2.5 shadow-sm fw-bold" style="border-radius: 8px;">
                        <i class="fas fa-clipboard-check me-2"></i>Đánh giá & Phê duyệt
                    </a>
                <?php elseif (($role === 'assistant' || $role === 'mangaka') && $submission['status'] === 'pending' && $submission['user_id'] == $_SESSION['user_id']): ?>
                    <p class="text-muted text-xs mb-3">Bạn có thể xóa sản phẩm đã nộp nếu nó chưa được đánh giá.</p>
                    <form action="<?= BASE_PATH ?>/index.php?controller=submission&action=delete&id=<?= $submission['submission_id'] ?>" method="POST" class="d-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');">
                        <button type="submit" class="btn btn-danger w-100 py-2.5 shadow-sm fw-bold" style="border-radius: 8px;">
                            <i class="fas fa-trash-alt me-2"></i>Xóa sản phẩm nộp này
                        </button>
                    </form>
                <?php else: ?>
                    <p class="text-muted mb-0"><?= $role === 'assistant' ? 'Sản phẩm' : 'Bản thảo' ?> này hiện ở trạng thái <strong><?= htmlspecialchars(strtoupper($submission['status'] ?? '')) ?></strong>.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($pages)): 
    $isDraftReview = (isset($submission['chapter_status']) && $submission['chapter_status'] === 'reviewing_draft');
    $pagePhaseText = $isDraftReview ? 'bản phác thảo kịch bản (Storyboard)' : 'bản vẽ hoàn chỉnh (Genko)';
?>
<!-- Danh sách các trang để Editor vẽ ghi chú sửa lỗi trực quan -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-header bg-white text-dark py-3 border-bottom border-light">
        <h5 class="card-title mb-0">
            <i class="fas fa-images me-2 text-primary"></i>
            <?= $role === 'editor' ? "Các trang {$pagePhaseText} (Đánh dấu lỗi trực quan)" : "Các trang {$pagePhaseText} (Xem ghi chú lỗi)" ?>
        </h5>
    </div>
    <div class="card-body p-4">
        <p class="text-muted text-xs mb-3">
            <?= $role === 'editor' 
                ? 'Nhấp vào nút <strong>"Đánh dấu lỗi"</strong> trên từng trang để mở bản vẽ lỗi trực quan.' 
                : 'Nhấp vào nút <strong>"Xem ghi chú lỗi"</strong> trên từng trang để xem chi tiết các nhận xét sửa đổi từ Editor.' ?>
        </p>
        <div class="row g-3">
            <?php foreach ($pages as $p): 
                $pageImg = $this->resolvePageImageUrl($p['image_url']);
                $oldPageImg = $this->resolvePageImageUrl($p['old_image_url']);
                $isUpdatedAfterAnnotation = $this->isPageUpdatedAfterAnnotation($p);
            ?>
                <div class="col-md-3 col-sm-6">
                    <div class="card h-100 border shadow-sm rounded-3 overflow-hidden">
                        <div class="position-relative text-center bg-light p-2" style="height: 180px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                            <img src="<?= htmlspecialchars($pageImg) ?>" class="img-fluid" style="max-height: 100%; object-fit: contain;">
                            
                            <?php if ($isUpdatedAfterAnnotation): ?>
                                <span class="position-absolute top-0 start-0 badge rounded-pill bg-warning text-dark m-2 shadow-sm" style="font-size: 0.8rem; padding: 0.35em 0.6em; z-index: 10; border: 1px solid rgba(0,0,0,0.15);">
                                    <i class="fas fa-sync-alt me-1"></i>Bản mới
                                </span>
                            <?php endif; ?>

                            <?php if (!empty($p['annotation_count']) && $p['annotation_count'] > 0): ?>
                                <span id="badge-page-<?= $p['page_id'] ?>" class="position-absolute top-0 end-0 badge rounded-pill bg-danger m-2" style="font-size: 0.85rem; padding: 0.35em 0.6em; z-index: 10;">
                                    <i class="fas fa-exclamation-triangle me-1"></i><?= $p['annotation_count'] ?> lỗi
                                </span>
                            <?php else: ?>
                                <span id="badge-page-<?= $p['page_id'] ?>" class="position-absolute top-0 end-0 badge rounded-pill bg-success m-2" style="font-size: 0.85rem; padding: 0.35em 0.6em; z-index: 10;">
                                    <i class="fas fa-check-circle me-1"></i>Không có lỗi
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-3 text-center border-top">
                            <span class="fw-bold d-block mb-2 text-dark">Trang <?= htmlspecialchars($p['page_number']) ?></span>
                            <?php if ($role === 'editor'): ?>
                                <button type="button" class="btn btn-sm btn-outline-danger w-100 btn-annotate fw-bold" 
                                        data-page-id="<?= $p['page_id'] ?>" 
                                        data-page-number="<?= $p['page_number'] ?>" 
                                        data-image-url="<?= htmlspecialchars($pageImg) ?>"
                                        data-old-image-url="<?= htmlspecialchars($oldPageImg) ?>"
                                        style="border-radius: 6px;">
                                    <i class="fas fa-edit me-1"></i>Đánh dấu lỗi
                                </button>
                            <?php else: ?>
                                <button type="button" class="btn btn-sm btn-outline-primary w-100 btn-annotate fw-bold" 
                                        data-page-id="<?= $p['page_id'] ?>" 
                                        data-page-number="<?= $p['page_number'] ?>" 
                                        data-image-url="<?= htmlspecialchars($pageImg) ?>"
                                        data-old-image-url="<?= htmlspecialchars($oldPageImg) ?>"
                                        style="border-radius: 6px;">
                                    <i class="fas fa-eye me-1"></i>Xem ghi chú lỗi
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal Đánh dấu lỗi trực quan -->
<div class="modal fade" id="annotateModal" tabindex="-1" aria-labelledby="annotateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: var(--shadow-lg);">
            <div class="modal-header bg-danger text-white py-3">
                <h5 class="modal-title fw-bold" id="annotateModalLabel">
                    <i class="fas fa-edit me-2"></i>
                    <?= $role === 'editor' ? 'Đánh dấu lỗi trực quan - Trang ' : 'Ghi chú lỗi trực quan - Trang ' ?>
                    <span id="modal-page-num"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Tranh vẽ bên trái -->
                    <div class="col-lg-8 bg-light p-4 d-flex flex-column align-items-center justify-content-center" style="min-height: 500px; max-height: 70vh; overflow: auto;">
                        <!-- Nút chuyển phiên bản bản vẽ cũ/mới -->
                        <div id="versionToggleContainer" class="d-none mb-3">
                            <div class="btn-group btn-group-sm shadow-sm" role="group">
                                <input type="radio" class="btn-check" name="version_toggle" id="btn-version-new" checked>
                                <label class="btn btn-outline-primary" for="btn-version-new"><i class="fas fa-image me-1"></i>Bản vẽ mới</label>

                                <input type="radio" class="btn-check" name="version_toggle" id="btn-version-old">
                                <label class="btn btn-outline-secondary" for="btn-version-old"><i class="fas fa-history me-1"></i>Bản vẽ cũ (Có lỗi)</label>
                            </div>
                        </div>
                        
                        <div id="annoImageWrapper" class="position-relative d-inline-block text-start shadow" style="border: 1px solid #cbd5e1; user-select: none;">
                            <img id="annoImage" src="" alt="Page for Annotating" class="img-fluid" style="display: block; max-height: 60vh; pointer-events: none;">
                            <!-- Overlay vẽ -->
                            <div id="annoOverlayContainer" class="position-absolute top-0 start-0 w-100 h-100" style="pointer-events: auto; cursor: default;"></div>
                        </div>
                    </div>
                    <!-- Form và danh sách ghi chú bên phải -->
                    <div class="col-lg-4 border-start d-flex flex-column" style="max-height: 70vh; background-color: #f8fafc;">
                        <?php if ($role === 'editor' && $submission['status'] === 'pending'): ?>
                            <div class="p-4 border-bottom bg-light">
                                <h6 class="fw-bold text-dark mb-2"><i class="fas fa-plus-circle me-1 text-danger"></i>Thêm ghi chú lỗi mới</h6>
                                <p class="text-muted" style="font-size: 0.78rem; line-height: 1.4;">Nhấn giữ và kéo chuột vẽ khung đỏ trên ảnh bên trái, sau đó nhập nội dung bên dưới.</p>
                                
                                <div id="no-selection-warning" class="alert alert-warning py-2 px-3 mb-0" style="font-size: 0.8rem; border-radius: 8px;">
                                    <i class="fas fa-info-circle me-1"></i>Vui lòng vẽ một khung lỗi trên ảnh để kích hoạt form nhập.
                                </div>

                                <form id="annoForm" style="display: none;">
                                    <input type="hidden" id="anno-x">
                                    <input type="hidden" id="anno-y">
                                    <input type="hidden" id="anno-w">
                                    <input type="hidden" id="anno-h">
                                    
                                    <div class="mb-3">
                                        <label for="anno-comment" class="form-label fw-bold text-slate-700" style="font-size: 0.825rem;">Nội dung ghi chú lỗi <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="anno-comment" rows="3" required placeholder="Ví dụ: Sai lời thoại nhân vật, thiếu đổ bóng nền, cần sửa lại nét vẽ..." style="border-radius: 8px; font-size: 0.88rem;"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-danger w-100 fw-bold py-2" style="border-radius: 8px;"><i class="fas fa-save me-1"></i>Lưu ghi chú lỗi</button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="p-4 border-bottom bg-light">
                                <h6 class="fw-bold text-dark mb-2"><i class="fas fa-info-circle me-1 text-primary"></i>Chế độ xem ghi chú</h6>
                                <p class="text-muted mb-0" style="font-size: 0.78rem; line-height: 1.4;">Rà chuột vào các khung đỏ trên trang truyện hoặc xem danh sách lỗi bên dưới để biết chi tiết sửa đổi.</p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="flex-grow-1 overflow-y-auto p-4 bg-white" style="min-height: 200px;">
                            <h6 class="fw-bold text-dark mb-3"><i class="fas fa-list me-1 text-muted"></i>Danh sách lỗi đã đánh dấu</h6>
                            <div id="anno-list" class="list-group list-group-flush">
                                <!-- Load bằng JS -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2 bg-light">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal" style="border-radius: 6px;">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    let activePageId = 0;
    let selectedBox = null;
    let isDrawing = false;
    let startX = 0, startY = 0;
    
    const STD_WIDTH = 800;
    const STD_HEIGHT = 1000;
    
    const isEditor = <?= json_encode($role === 'editor' && $submission['status'] === 'pending') ?>;

    const modal = new bootstrap.Modal(document.getElementById('annotateModal'));
    const modalPageNum = document.getElementById('modal-page-num');
    const annoImage = document.getElementById('annoImage');
    const overlayContainer = document.getElementById('annoOverlayContainer');
    if (isEditor && overlayContainer) {
        overlayContainer.style.cursor = 'crosshair';
    }
    const annoForm = document.getElementById('annoForm');
    const noSelectionWarning = document.getElementById('no-selection-warning');
    const annoList = document.getElementById('anno-list');
    
    document.querySelectorAll('.btn-annotate').forEach(button => {
        button.addEventListener('click', function() {
            activePageId = this.getAttribute('data-page-id');
            const pageNum = this.getAttribute('data-page-number');
            const imgUrl = this.getAttribute('data-image-url');
            const oldImgUrl = this.getAttribute('data-old-image-url');
            
            modalPageNum.textContent = pageNum;
            annoImage.src = imgUrl;
            
            resetDrawingState();
            
            const toggleContainer = document.getElementById('versionToggleContainer');
            const btnNew = document.getElementById('btn-version-new');
            const btnOld = document.getElementById('btn-version-old');
            
            if (btnNew && btnOld) {
                btnNew.checked = true;
                btnNew.onclick = function() {
                    annoImage.src = imgUrl;
                    if (overlayContainer) overlayContainer.style.pointerEvents = 'auto';
                };
                btnOld.onclick = function() {
                    annoImage.src = oldImgUrl;
                    if (overlayContainer) overlayContainer.style.pointerEvents = 'none'; // Chặn vẽ đè lỗi lên bản cũ
                };
            }
            
            if (oldImgUrl && oldImgUrl !== '') {
                if (toggleContainer) toggleContainer.classList.remove('d-none');
            } else {
                if (toggleContainer) toggleContainer.classList.add('d-none');
            }
            
            modal.show();
        });
    });

    document.getElementById('annotateModal').addEventListener('shown.bs.modal', function () {
        loadAnnotations();
    });

    if (isEditor && overlayContainer) {
        overlayContainer.addEventListener('mousedown', function(e) {
            isDrawing = true;
            const rect = overlayContainer.getBoundingClientRect();
            startX = e.clientX - rect.left;
            startY = e.clientY - rect.top;
            
            if (selectedBox && selectedBox.parentNode) {
                selectedBox.parentNode.removeChild(selectedBox);
            }
            
            selectedBox = document.createElement('div');
            selectedBox.style.position = 'absolute';
            selectedBox.style.border = '2px dashed #dc3545';
            selectedBox.style.backgroundColor = 'rgba(220, 53, 69, 0.15)';
            selectedBox.style.pointerEvents = 'none';
            selectedBox.style.left = startX + 'px';
            selectedBox.style.top = startY + 'px';
            
            overlayContainer.appendChild(selectedBox);
        });

        overlayContainer.addEventListener('mousemove', function(e) {
            if (!isDrawing) return;
            const rect = overlayContainer.getBoundingClientRect();
            const currentX = e.clientX - rect.left;
            const currentY = e.clientY - rect.top;
            
            const width = currentX - startX;
            const height = currentY - startY;
            
            selectedBox.style.width = Math.abs(width) + 'px';
            selectedBox.style.height = Math.abs(height) + 'px';
            selectedBox.style.left = (width < 0 ? currentX : startX) + 'px';
            selectedBox.style.top = (height < 0 ? currentY : startY) + 'px';
        });

        overlayContainer.addEventListener('mouseup', function(e) {
            if (!isDrawing) return;
            isDrawing = false;
            
            const rect = overlayContainer.getBoundingClientRect();
            const boxWidth = parseFloat(selectedBox.style.width) || 0;
            const boxHeight = parseFloat(selectedBox.style.height) || 0;
            const boxLeft = parseFloat(selectedBox.style.left) || 0;
            const boxTop = parseFloat(selectedBox.style.top) || 0;
            
            if (boxWidth < 10 || boxHeight < 10) {
                if (selectedBox && selectedBox.parentNode) {
                    selectedBox.parentNode.removeChild(selectedBox);
                }
                selectedBox = null;
                resetDrawingState();
                return;
            }
            
            const scaleX = STD_WIDTH / rect.width;
            const scaleY = STD_HEIGHT / rect.height;
            
            document.getElementById('anno-x').value = Math.round(boxLeft * scaleX);
            document.getElementById('anno-y').value = Math.round(boxTop * scaleY);
            document.getElementById('anno-w').value = Math.round(boxWidth * scaleX);
            document.getElementById('anno-h').value = Math.round(boxHeight * scaleY);
            
            if (noSelectionWarning) noSelectionWarning.style.display = 'none';
            if (annoForm) annoForm.style.display = 'block';
            document.getElementById('anno-comment').focus();
        });
    }

    if (annoForm) {
        annoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const commentInput = document.getElementById('anno-comment');
            
            const data = {
                page_id: activePageId,
                x: document.getElementById('anno-x').value,
                y: document.getElementById('anno-y').value,
                width: document.getElementById('anno-w').value,
                height: document.getElementById('anno-h').value,
                comments: commentInput.value
            };

            fetch('<?= BASE_PATH ?>/index.php?controller=review&action=save_annotation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    commentInput.value = '';
                    resetDrawingState();
                    loadAnnotations();
                } else {
                    alert('Lỗi: ' + res.error);
                }
            })
            .catch(err => {
                console.error(err);
                alert('Lỗi kết nối máy chủ');
            });
        });
    }

    function resetDrawingState() {
        if (selectedBox && selectedBox.parentNode) {
            selectedBox.parentNode.removeChild(selectedBox);
        }
        selectedBox = null;
        if (noSelectionWarning) noSelectionWarning.style.display = 'block';
        if (annoForm) annoForm.style.display = 'none';
    }

    function loadAnnotations() {
        fetch('<?= BASE_PATH ?>/index.php?controller=review&action=get_annotations&page_id=' + activePageId)
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                renderOverlayAnnotations(res.annotations);
                renderListAnnotations(res.annotations);
                
                // Cập nhật badge trên trang cha tương ứng ngay lập tức
                const badgeEl = document.getElementById('badge-page-' + activePageId);
                if (badgeEl) {
                    const count = res.annotations.length;
                    if (count > 0) {
                        badgeEl.className = 'position-absolute top-0 end-0 badge rounded-pill bg-danger m-2';
                        badgeEl.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i>${count} lỗi`;
                    } else {
                        badgeEl.className = 'position-absolute top-0 end-0 badge rounded-pill bg-success m-2';
                        badgeEl.innerHTML = '<i class="fas fa-check-circle me-1"></i>Không có lỗi';
                    }
                }
            }
        });
    }

    function renderOverlayAnnotations(annotations) {
        document.querySelectorAll('.editor-annotation-box').forEach(el => el.remove());
        
        const rect = overlayContainer.getBoundingClientRect();
        const scaleX = rect.width / STD_WIDTH;
        const scaleY = rect.height / STD_HEIGHT;
        
        annotations.forEach(ann => {
            const el = document.createElement('div');
            el.className = 'editor-annotation-box';
            el.style.position = 'absolute';
            el.style.border = '2px dashed #dc3545';
            el.style.backgroundColor = 'rgba(220, 53, 69, 0.08)';
            el.style.left = (ann.x * scaleX) + 'px';
            el.style.top = (ann.y * scaleY) + 'px';
            el.style.width = (ann.width * scaleX) + 'px';
            el.style.height = (ann.height * scaleY) + 'px';
            el.title = ann.comments;
            el.style.pointerEvents = 'auto';
            el.style.cursor = 'help';
            
            overlayContainer.appendChild(el);
        });
    }

    function renderListAnnotations(annotations) {
        annoList.innerHTML = '';
        if (annotations.length === 0) {
            annoList.innerHTML = '<p class="text-muted text-xs italic text-center py-3">Chưa có ghi chú lỗi nào.</p>';
            return;
        }

        annotations.forEach(ann => {
            const item = document.createElement('div');
            item.className = 'list-group-item px-0 py-2 border-bottom';
            item.innerHTML = `
                <div class="d-flex justify-content-between align-items-start">
                    <div style="flex-grow:1; min-width:0; padding-right:10px;">
                        <span class="fw-semibold text-danger text-xs d-block"><i class="fas fa-exclamation-triangle me-1"></i>Lỗi tại (${ann.x}, ${ann.y})</span>
                        <p class="mb-1 text-dark small" style="white-space: pre-wrap; font-size:0.825rem;">${ann.comments}</p>
                        <small class="text-muted" style="font-size:0.75rem;"><i class="fas fa-user-edit me-1"></i>Editor: ${ann.editor_name}</small>
                    </div>
                    ${isEditor ? `
                    <button class="btn btn-xs btn-link text-danger p-0" onclick="deleteAnnotation(${ann.annotation_id})">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    ` : ''}
                </div>
            `;
            annoList.appendChild(item);
        });
    }

    window.deleteAnnotation = function(id) {
        if (!confirm('Bạn có chắc chắn muốn xóa ghi chú lỗi này?')) return;
        
        fetch('<?= BASE_PATH ?>/index.php?controller=review&action=delete_annotation', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ annotation_id: id })
        })
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                loadAnnotations();
            } else {
                alert('Lỗi: ' + res.error);
            }
        });
});
</script>
<?php endif; ?>

<?php if ($isImage && !empty($submission['task_id'])): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const STD_WIDTH = 800;
    const STD_HEIGHT = 1000;
    
    const isMangakaReview = <?= json_encode($role === 'mangaka' && $submission['status'] === 'pending') ?>;
    const subOverlayContainer = document.getElementById('subAnnoOverlayContainer');
    const subAnnoForm = document.getElementById('subAnnoForm');
    const subNoSelectionWarning = document.getElementById('sub-no-selection-warning');
    const subAnnoList = document.getElementById('sub-anno-list');
    const submissionId = <?= json_encode($submission['submission_id']) ?>;

    if (isMangakaReview && subOverlayContainer) {
        subOverlayContainer.style.cursor = 'crosshair';
        
        let subIsDrawing = false;
        let subStartX = 0, subStartY = 0;
        let subSelectedBox = null;
        
        subOverlayContainer.addEventListener('mousedown', function(e) {
            subIsDrawing = true;
            const rect = subOverlayContainer.getBoundingClientRect();
            subStartX = e.clientX - rect.left;
            subStartY = e.clientY - rect.top;
            
            if (subSelectedBox && subSelectedBox.parentNode) {
                subSelectedBox.parentNode.removeChild(subSelectedBox);
            }
            
            subSelectedBox = document.createElement('div');
            subSelectedBox.style.position = 'absolute';
            subSelectedBox.style.border = '2px dashed #dc3545';
            subSelectedBox.style.backgroundColor = 'rgba(220, 53, 69, 0.15)';
            subSelectedBox.style.pointerEvents = 'none';
            subSelectedBox.style.left = subStartX + 'px';
            subSelectedBox.style.top = subStartY + 'px';
            
            subOverlayContainer.appendChild(subSelectedBox);
        });

        subOverlayContainer.addEventListener('mousemove', function(e) {
            if (!subIsDrawing) return;
            const rect = subOverlayContainer.getBoundingClientRect();
            const currentX = e.clientX - rect.left;
            const currentY = e.clientY - rect.top;
            
            const width = currentX - subStartX;
            const height = currentY - subStartY;
            
            subSelectedBox.style.width = Math.abs(width) + 'px';
            subSelectedBox.style.height = Math.abs(height) + 'px';
            subSelectedBox.style.left = (width < 0 ? currentX : subStartX) + 'px';
            subSelectedBox.style.top = (height < 0 ? currentY : subStartY) + 'px';
        });

        subOverlayContainer.addEventListener('mouseup', function(e) {
            if (!subIsDrawing) return;
            subIsDrawing = false;
            
            const rect = subOverlayContainer.getBoundingClientRect();
            const boxWidth = parseFloat(subSelectedBox.style.width) || 0;
            const boxHeight = parseFloat(subSelectedBox.style.height) || 0;
            const boxLeft = parseFloat(subSelectedBox.style.left) || 0;
            const boxTop = parseFloat(subSelectedBox.style.top) || 0;
            
            if (boxWidth < 10 || boxHeight < 10) {
                if (subSelectedBox && subSelectedBox.parentNode) {
                    subSelectedBox.parentNode.removeChild(subSelectedBox);
                }
                subSelectedBox = null;
                resetSubDrawingState();
                return;
            }
            
            const scaleX = STD_WIDTH / rect.width;
            const scaleY = STD_HEIGHT / rect.height;
            
            document.getElementById('sub-anno-x').value = Math.round(boxLeft * scaleX);
            document.getElementById('sub-anno-y').value = Math.round(boxTop * scaleY);
            document.getElementById('sub-anno-w').value = Math.round(boxWidth * scaleX);
            document.getElementById('sub-anno-h').value = Math.round(boxHeight * scaleY);
            
            if (subNoSelectionWarning) subNoSelectionWarning.style.display = 'none';
            if (subAnnoForm) subAnnoForm.style.display = 'block';
            document.getElementById('sub-anno-comment').focus();
        });
    }

    function resetSubDrawingState() {
        const boxes = document.querySelectorAll('#subAnnoOverlayContainer div');
        boxes.forEach(b => {
            if (b.style.borderStyle === 'dashed') {
                b.remove();
            }
        });
        if (subNoSelectionWarning) subNoSelectionWarning.style.display = 'block';
        if (subAnnoForm) subAnnoForm.style.display = 'none';
    }

    if (subAnnoForm) {
        subAnnoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const commentInput = document.getElementById('sub-anno-comment');
            
            const data = {
                submission_id: submissionId,
                x: document.getElementById('sub-anno-x').value,
                y: document.getElementById('sub-anno-y').value,
                width: document.getElementById('sub-anno-w').value,
                height: document.getElementById('sub-anno-h').value,
                comments: commentInput.value
            };

            fetch('<?= BASE_PATH ?>/index.php?controller=review&action=save_submission_annotation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    commentInput.value = '';
                    resetSubDrawingState();
                    loadSubAnnotations();
                } else {
                    alert('Lỗi: ' + res.error);
                }
            })
            .catch(err => {
                console.error(err);
                alert('Lỗi kết nối máy chủ');
            });
        });
    }

    function loadSubAnnotations() {
        if (!subOverlayContainer) return;
        fetch('<?= BASE_PATH ?>/index.php?controller=review&action=get_submission_annotations&submission_id=' + submissionId)
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                renderSubOverlayAnnotations(res.annotations);
                renderSubListAnnotations(res.annotations);
            }
        });
    }

    function renderSubOverlayAnnotations(annotations) {
        document.querySelectorAll('.sub-annotation-box').forEach(el => el.remove());
        
        const rect = subOverlayContainer.getBoundingClientRect();
        const scaleX = rect.width / STD_WIDTH;
        const scaleY = rect.height / STD_HEIGHT;
        
        annotations.forEach(ann => {
            const el = document.createElement('div');
            el.className = 'sub-annotation-box';
            el.style.position = 'absolute';
            el.style.border = '2px dashed #dc3545';
            el.style.backgroundColor = 'rgba(220, 53, 69, 0.08)';
            el.style.left = (ann.x * scaleX) + 'px';
            el.style.top = (ann.y * scaleY) + 'px';
            el.style.width = (ann.width * scaleX) + 'px';
            el.style.height = (ann.height * scaleY) + 'px';
            el.title = ann.comments;
            el.style.pointerEvents = 'auto';
            el.style.cursor = 'help';
            
            subOverlayContainer.appendChild(el);
        });
    }

    function renderSubListAnnotations(annotations) {
        if (!subAnnoList) return;
        subAnnoList.innerHTML = '';
        if (annotations.length === 0) {
            subAnnoList.innerHTML = '<p class="text-muted text-xs italic text-center py-3">Chưa có ghi chú sửa đổi nào trên bản vẽ.</p>';
            return;
        }

        annotations.forEach(ann => {
            const item = document.createElement('div');
            item.className = 'list-group-item px-3 py-2 border-bottom';
            item.innerHTML = `
                <div class="d-flex justify-content-between align-items-start">
                    <div style="flex-grow:1; min-width:0; padding-right:10px;">
                        <span class="fw-semibold text-danger text-xs d-block"><i class="fas fa-exclamation-triangle me-1"></i>Lỗi tại (${ann.x}, ${ann.y})</span>
                        <p class="mb-1 text-dark small" style="white-space: pre-wrap; font-size:0.825rem;">${ann.comments}</p>
                        <small class="text-muted" style="font-size:0.75rem;"><i class="fas fa-user-edit me-1"></i>Tác giả: ${ann.user_name}</small>
                    </div>
                    ${isMangakaReview ? `
                    <button class="btn btn-xs btn-link text-danger p-0" onclick="deleteSubAnnotation(${ann.annotation_id})">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    ` : ''}
                </div>
            `;
            subAnnoList.appendChild(item);
        });
    }

    window.deleteSubAnnotation = function(id) {
        if (!confirm('Bạn có chắc chắn muốn xóa ghi chú lỗi này?')) return;
        
        fetch('<?= BASE_PATH ?>/index.php?controller=review&action=delete_submission_annotation', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ annotation_id: id })
        })
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                loadSubAnnotations();
            } else {
                alert('Lỗi: ' + res.error);
            }
        });
    };

    // Load annotations initially and on resize
    loadSubAnnotations();
    window.addEventListener('resize', loadSubAnnotations);
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
