<?php
if (!defined('BASE_PATH')) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $pos = strpos($scriptName, '/views/');
    $projectUrl = ($pos !== false) ? substr($scriptName, 0, $pos) : '';
    header('Location: ' . $projectUrl . '/index.php');
    exit;
}
/**
 * View: Giao diện tạo đánh giá nhận xét cho bản thảo (review_create.php)
 * Vai trò: Editor (Biên tập viên) / Mangaka (Họa sĩ chính)
 * Chức năng: Cho phép người kiểm duyệt nhập ý kiến, cho điểm và quyết định phê duyệt (Approve) hoặc từ chối (Reject) bản thảo.
 * 
 * @var array $submission Thông tin bản thảo được chọn để đánh giá
 */
$pageTitle = 'Tạo Đánh giá (Review)';
$current_page = 'reviews';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="mb-4">
    <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=show&id=<?= $submission['submission_id'] ?>" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="fas fa-arrow-left me-1"></i> Quay lại chi tiết bản thảo
    </a>
    <h2 class="h3 mb-1">Đánh giá Bản thảo #<?= $submission['submission_id'] ?></h2>
    <p class="text-muted">Người gửi: <span class="fw-bold text-dark"><?= htmlspecialchars((string)($submission['sender_name'] ?? '')) ?></span></p>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($_SESSION['error']);
                                                        unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Cột bên trái: Hiển thị thông tin chi tiết của Bản thảo và File đính kèm -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-header bg-white text-dark py-3 border-bottom border-light">
                <h5 class="card-title mb-0"><i class="fas fa-file-alt me-2 text-primary"></i>Nội dung Bản thảo</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p class="mb-1 text-muted small fw-bold">Loại bản thảo</p>
                        <p class="fs-6">
                            <?php if (!empty($submission['task_id'])): ?>
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">Task Drawing</span>
                            <?php else: ?>
                                <?php if (isset($submission['chapter_status']) && ($submission['chapter_status'] === 'reviewing_final' || $submission['chapter_status'] === 'reviewing')): ?>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">Bản Hoàn Chỉnh (Final)</span>
                                <?php elseif (isset($submission['chapter_status']) && $submission['chapter_status'] === 'reviewing_draft'): ?>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">Bản Nháp (Draft Storyboard)</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">Full Chapter</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-muted small fw-bold">Mục tiêu</p>
                        <p class="fs-6 fw-medium">
                            <?php if (!empty($submission['task_id'])): ?>
                                <?= htmlspecialchars((string)($submission['task_title'] ?? 'Task #' . ($submission['task_id'] ?? ''))) ?>
                            <?php else: ?>
                                Ch.<?= htmlspecialchars((string)($submission['chapter_number'] ?? '')) ?> - <?= htmlspecialchars((string)($submission['chapter_title'] ?? '')) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <!-- Yêu cầu công việc ban đầu -->
                <?php if (!empty($submission['task_id'])): ?>
                <div class="mb-4 bg-slate-50 p-3 rounded border border-slate-200">
                    <p class="fw-bold text-primary mb-2"><i class="fas fa-clipboard-list me-2"></i>Yêu cầu công việc ban đầu</p>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <span class="text-muted small">Loại việc:</span>
                            <span class="badge bg-secondary-subtle text-secondary ms-1"><?= htmlspecialchars((string)($submission['task_type'] ?? 'Không rõ')) ?></span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <span class="text-muted small">Độ ưu tiên:</span>
                            <?php 
                            $priClass = 'secondary';
                            $priLabel = 'Bình thường';
                            if ($submission['task_priority'] == 'high') { $priClass = 'danger'; $priLabel = 'Cao'; }
                            elseif ($submission['task_priority'] == 'low') { $priClass = 'info'; $priLabel = 'Thấp'; }
                            ?>
                            <span class="badge bg-<?= $priClass ?>-subtle text-<?= $priClass ?> ms-1"><?= $priLabel ?></span>
                        </div>
                        <?php if (!empty($submission['task_due_date'])): ?>
                        <div class="col-12 mb-2">
                            <span class="text-muted small">Hạn chót:</span>
                            <span class="text-dark small ms-1"><?= date('d/m/Y H:i', strtotime($submission['task_due_date'])) ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="col-12 mt-2 pt-2 border-top border-slate-200">
                            <span class="text-muted small d-block mb-1">Mô tả / Yêu cầu chi tiết:</span>
                            <div class="text-dark bg-white p-3 rounded border border-slate-100 quill-content-render" style="font-size: 0.85rem; max-height: 250px; overflow-y: auto;">
                                <?= !empty($submission['task_description']) ? $submission['task_description'] : '<em>Không có mô tả chi tiết.</em>' ?>
                            </div>
                        </div>

                        <?php if (!empty($submission['page_image_url'])): ?>
                        <div class="col-12 mt-3 pt-3 border-top border-slate-200">
                            <span class="text-muted small d-block mb-2">Vị trí phân vùng trên bản nháp gốc:</span>
                            <?php 
                                $imageUrl = $submission['page_image_url'];
                                $resolvedImage = (strpos($imageUrl, 'http') === 0) ? $imageUrl : BASE_PATH . '/' . ltrim($imageUrl, '/');
                            ?>
                            <div class="text-center bg-white p-2 rounded border border-slate-100 overflow-hidden">
                                <div class="position-relative d-inline-block shadow-sm overflow-hidden" style="max-width: 100%; border: 1px solid #ddd;">
                                    <img src="<?= htmlspecialchars($resolvedImage) ?>" class="img-fluid" style="max-height: 350px; display: block;" alt="Page Reference">
                                    <?php if (!empty($regions)): ?>
                                        <?php foreach ($regions as $r): 
                                            $rl = ($r['x'] / 800) * 100;
                                            $rt = ($r['y'] / 1000) * 100;
                                            $rw = ($r['width'] / 800) * 100;
                                            $rh = ($r['height'] / 1000) * 100;
                                        ?>
                                            <div style="position: absolute; left: <?= $rl ?>%; top: <?= $rt ?>%; width: <?= $rw ?>%; height: <?= $rh ?>%; border: 3px solid #0d6efd; background-color: rgba(13, 110, 253, 0.2); pointer-events: none;" title="Phân vùng #<?= $r['region_id'] ?>"></div>
                                        <?php endforeach; ?>
                                    <?php elseif (isset($submission['region_x'])): 
                                        $l = ($submission['region_x'] / 800) * 100;
                                        $t = ($submission['region_y'] / 1000) * 100;
                                        $w = ($submission['region_width'] / 800) * 100;
                                        $h = ($submission['region_height'] / 1000) * 100;
                                    ?>
                                        <div style="position: absolute; left: <?= $l ?>%; top: <?= $t ?>%; width: <?= $w ?>%; height: <?= $h ?>%; border: 3px solid #0d6efd; background-color: rgba(13, 110, 253, 0.2); pointer-events: none;"></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Khu vực hiển thị tệp tin/hình ảnh đính kèm -->
                <?php if ($submission['file_url']): ?>
                    <div class="mb-4">
                        <p class="fw-bold text-muted mb-2 border-bottom pb-2">File đính kèm:</p>
                        <?php 
                            $isImage = false;
                            $filePath = __DIR__ . '/../../' . ltrim($submission['file_url'], '/');
                            if (file_exists($filePath)) {
                                $finfo = new finfo(FILEINFO_MIME_TYPE);
                                $mime = $finfo->file($filePath);
                                $isImage = (strpos($mime, 'image/') === 0);
                            } else {
                                $ext = strtolower(pathinfo($submission['file_url'], PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            }
                        ?>
                        <?php if ($isImage): ?>
                            <div class="text-center bg-light p-3 rounded border d-flex justify-content-center align-items-center">
                                <?php if (!empty($submission['task_id'])): ?>
                                    <div id="subAnnoWrapper" class="position-relative d-inline-block text-start shadow-sm" style="border: 1px solid #cbd5e1; user-select: none; max-width: 100%;">
                                        <img id="subAnnoImage" src="<?= htmlspecialchars((strpos((string)($submission['file_url'] ?? ''), 'http') === 0) ? $submission['file_url'] : BASE_PATH . '/' . ltrim((string)($submission['file_url'] ?? ''), '/')) ?>" 
                                             onerror="this.onerror=null; this.src='uploads/submissions/<?= htmlspecialchars(basename($submission['file_url'])) ?>';"
                                             class="img-fluid rounded" alt="Submission file" style="max-height: 500px; display: block;">
                                        <!-- Overlay hiển thị ghi chú lỗi -->
                                        <div id="subAnnoOverlayContainer" class="position-absolute top-0 start-0 w-100 h-100" style="pointer-events: none;"></div>
                                    </div>
                                <?php else: ?>
                                    <img src="<?= htmlspecialchars((strpos((string)($submission['file_url'] ?? ''), 'http') === 0) ? $submission['file_url'] : BASE_PATH . '/' . ltrim((string)($submission['file_url'] ?? ''), '/')) ?>" class="img-fluid rounded shadow-sm" alt="Submission file" style="max-height: 500px;">
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!empty($submission['task_id'])): ?>
                                <div class="mt-3 text-start">
                                    <h6 class="fw-bold text-dark mb-2 text-sm"><i class="fas fa-list me-1.5 text-muted"></i>Ghi chú lỗi đã đánh dấu trên bản vẽ:</h6>
                                    <div id="sub-anno-list" class="list-group list-group-flush border rounded bg-white overflow-hidden" style="max-height: 200px; overflow-y: auto;">
                                        <!-- Load bằng JS -->
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="<?= htmlspecialchars((strpos((string)($submission['file_url'] ?? ''), 'http') === 0) ? $submission['file_url'] : BASE_PATH . '/' . ltrim((string)($submission['file_url'] ?? ''), '/')) ?>" class="btn btn-outline-primary" target="_blank">
                                <i class="fas fa-download me-2"></i> Tải xuống bản thảo đính kèm
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Khu vực hiển thị ghi chú của người nộp bản thảo -->
                <?php if ($submission['notes']): ?>
                    <div>
                        <p class="fw-bold text-muted mb-2 border-bottom pb-2">Ghi chú từ người gửi:</p>
                        <div class="p-3 bg-light rounded text-dark">
                            <?= nl2br(htmlspecialchars((string)($submission['notes'] ?? ''))) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($annotatedPages)): ?>
                <!-- Các trang bản thảo bị đánh dấu lỗi (Từ Review trước) -->
                <div class="mb-4 bg-slate-50 p-3 rounded border border-slate-200">
                    <?php 
                    $hasUpdatedPages = false;
                    foreach ($annotatedPages as $ap) {
                        if ($this->isPageUpdatedAfterLatestAnnotation($ap['page'], $ap['annotations'])) {
                            $hasUpdatedPages = true;
                            break;
                        }
                    }
                    $titleText = $hasUpdatedPages ? 'Đối chiếu các trang đã sửa lỗi (Xem bản vẽ cũ có lỗi hoặc bản mới):' : 'Các trang bản thảo gốc có lỗi cần sửa:';
                    $titleColor = $hasUpdatedPages ? 'text-primary' : 'text-danger';
                    ?>
                    <h6 class="fw-bold <?= $titleColor ?> mb-4 border-bottom pb-2"><i class="fas fa-images me-2 <?= $titleColor ?>"></i><?= $titleText ?></h6>
                    
                    <div class="row g-4">
                        <?php foreach ($annotatedPages as $ap): 
                            $page = $ap['page'];
                            $annotations = $ap['annotations'];
                            
                            // Sử dụng ảnh cũ (old_image_url) làm ảnh nền để vẽ các khung lỗi nếu có
                            $imageUrl = !empty($page['old_image_url']) ? $page['old_image_url'] : ($page['image_url'] ?? '');
                            $resolvedImage = (strpos($imageUrl, 'http') === 0) ? $imageUrl : BASE_PATH . '/' . ltrim($imageUrl, '/');
                            $isUpdated = $this->isPageUpdatedAfterLatestAnnotation($page, $annotations);
                        ?>
                        <div class="col-12 col-xl-6">
                            <div class="card h-100 shadow-sm border-0 bg-light">
                                <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                                    <h6 class="fw-bold text-dark mb-0">Trang <?= htmlspecialchars($page['page_number']) ?></h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="position-relative d-inline-block shadow-sm mb-3" style="max-width: 100%; border: 1px solid #ddd; background-color: #fff;">
                                        <!-- Khung hiển thị Bản vẽ cũ có lỗi -->
                                        <div id="wrapper-old-<?= $page['page_id'] ?>">
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

                                        <!-- Khung hiển thị Bản vẽ mới đã sửa -->
                                        <?php if ($isUpdated): 
                                            $newPageImg = $this->resolvePageImageUrl($page['image_url']);
                                        ?>
                                            <div id="wrapper-new-<?= $page['page_id'] ?>" style="display: none;">
                                                <img src="<?= htmlspecialchars($newPageImg) ?>" class="img-fluid" style="max-height: 400px; display: block;" alt="New Page <?= $page['page_number'] ?>">
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($isUpdated): ?>
                                            <span class="position-absolute top-0 start-0 badge rounded-pill bg-warning text-dark m-2 shadow-sm" style="font-size: 0.8rem; padding: 0.35em 0.6em; z-index: 10; border: 1px solid rgba(0,0,0,0.15);">
                                                <i class="fas fa-sync-alt me-1"></i>Bản mới
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($isUpdated): ?>
                                        <div class="mb-3 text-center">
                                            <div class="btn-group btn-group-sm shadow-sm" role="group">
                                                <button type="button" class="btn btn-outline-secondary active fw-bold" id="btn-show-old-<?= $page['page_id'] ?>" onclick="togglePageView(<?= $page['page_id'] ?>, 'old')">
                                                    <i class="fas fa-history me-1"></i>Bản vẽ cũ (Có lỗi)
                                                </button>
                                                <button type="button" class="btn btn-outline-primary fw-bold" id="btn-show-new-<?= $page['page_id'] ?>" onclick="togglePageView(<?= $page['page_id'] ?>, 'new')">
                                                    <i class="fas fa-image me-1"></i>Bản vẽ mới đã sửa
                                                </button>
                                            </div>
                                        </div>
                                    <?php endif; ?>

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

                <script>
                function togglePageView(pageId, version) {
                    const oldWrapper = document.getElementById('wrapper-old-' + pageId);
                    const newWrapper = document.getElementById('wrapper-new-' + pageId);
                    const btnOld = document.getElementById('btn-show-old-' + pageId);
                    const btnNew = document.getElementById('btn-show-new-' + pageId);
                    
                    if (version === 'new') {
                        if (oldWrapper) oldWrapper.style.display = 'none';
                        if (newWrapper) newWrapper.style.display = 'block';
                        if (btnOld) btnOld.classList.remove('active');
                        if (btnNew) btnNew.classList.add('active');
                    } else {
                        if (oldWrapper) oldWrapper.style.display = 'block';
                        if (newWrapper) newWrapper.style.display = 'none';
                        if (btnOld) btnOld.classList.add('active');
                        if (btnNew) btnNew.classList.remove('active');
                    }
                }
                </script>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Cột bên phải: Form điền kết quả đánh giá (Được ghim cố định - sticky) -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-3 position-sticky" style="top: 20px;">
            <div class="card-header bg-white text-dark py-3 border-bottom border-light">
                <h5 class="card-title mb-0"><i class="fas fa-edit me-2 text-warning"></i>Form Đánh giá</h5>
            </div>
            <div class="card-body">
                <form action="<?= BASE_PATH ?>/index.php?controller=review&action=store" method="POST">
                    <input type="hidden" name="submission_id" value="<?= $submission['submission_id'] ?>">

                    <div class="mb-4">
                        <label for="comments" class="form-label fw-bold">Nhận xét (Comments) <span class="text-danger">*</span></label>
                        <textarea class="form-control bg-light" id="comments" name="comments" rows="6" required placeholder="Nhập nhận xét chi tiết..."></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="rating" class="form-label fw-bold">Điểm số (1-10)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-star text-warning"></i></span>
                            <input type="number" class="form-control" id="rating" name="rating" min="1" max="10" placeholder="Ví dụ: 8 (Tùy chọn)">
                        </div>
                    </div>

                    <div class="mb-4 p-3 border rounded bg-light">
                        <label class="form-label fw-bold d-block mb-3 border-bottom pb-2">Quyết định (Decision) <span class="text-danger">*</span></label>
                        <div class="d-flex flex-column gap-2">
                            <div class="form-check custom-radio">
                                <input class="form-check-input" type="radio" name="decision" id="decision_approve" value="approved" required>
                                <label class="form-check-label text-success fw-bold w-100" for="decision_approve">
                                    <i class="fas fa-check-circle me-2"></i> 
                                    <?php if (isset($submission['chapter_status']) && $submission['chapter_status'] === 'reviewing_draft'): ?>
                                        Approve (Duyệt kịch bản nháp & Bắt đầu vẽ)
                                    <?php elseif (isset($submission['chapter_status']) && ($submission['chapter_status'] === 'reviewing_final' || $submission['chapter_status'] === 'reviewing')): ?>
                                        Approve (Duyệt phát hành / xuất bản)
                                    <?php else: ?>
                                        Approve (Phê duyệt)
                                    <?php endif; ?>
                                </label>
                            </div>
                            <div class="form-check custom-radio mt-2">
                                <input class="form-check-input" type="radio" name="decision" id="decision_reject" value="rejected" required>
                                <label class="form-check-label text-danger fw-bold w-100" for="decision_reject">
                                    <i class="fas fa-times-circle me-2"></i> 
                                    <?php if (isset($submission['chapter_status']) && $submission['chapter_status'] === 'reviewing_draft'): ?>
                                        Reject (Yêu cầu chỉnh sửa kịch bản nháp)
                                    <?php elseif (isset($submission['chapter_status']) && ($submission['chapter_status'] === 'reviewing_final' || $submission['chapter_status'] === 'reviewing')): ?>
                                        Reject (Yêu cầu vẽ lại nét lỗi / Sửa hình vẽ)
                                    <?php else: ?>
                                        Reject (Yêu cầu sửa / Từ chối)
                                    <?php endif; ?>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">
                            <i class="fas fa-paper-plane me-2"></i> Gửi Đánh Giá
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-radio .form-check-input {
        width: 1.25em;
        height: 1.25em;
        margin-top: 0.15em;
    }

    .custom-radio .form-check-label {
        cursor: pointer;
        padding-left: 0.2rem;
    }
</style>

<?php if (!empty($submission['task_id']) && !empty($submission['file_url']) && isset($isImage) && $isImage): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const STD_WIDTH = 800;
    const STD_HEIGHT = 1000;
    
    const subOverlayContainer = document.getElementById('subAnnoOverlayContainer');
    const subAnnoList = document.getElementById('sub-anno-list');
    const submissionId = <?= json_encode($submission['submission_id']) ?>;
    let currentSubAnnotations = [];

    function loadSubAnnotations() {
        if (!subOverlayContainer) return;
        fetch('<?= BASE_PATH ?>/index.php?controller=review&action=get_submission_annotations&submission_id=' + submissionId)
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                currentSubAnnotations = res.annotations;
                renderSubOverlayAnnotations(currentSubAnnotations);
                renderSubListAnnotations(currentSubAnnotations);
            }
        });
    }

    function renderSubOverlayAnnotations(annotations) {
        if (!subOverlayContainer) return;
        document.querySelectorAll('.sub-annotation-box').forEach(el => el.remove());
        
        const rect = subOverlayContainer.getBoundingClientRect();
        if (rect.width === 0 || rect.height === 0) return;
        
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
                </div>
            `;
            subAnnoList.appendChild(item);
        });
    }

    // Load annotations initially, on resize, and when image is loaded
    loadSubAnnotations();
    
    const subAnnoImg = document.getElementById('subAnnoImage');
    if (subAnnoImg) {
        if (subAnnoImg.complete) {
            renderSubOverlayAnnotations(currentSubAnnotations);
        } else {
            subAnnoImg.addEventListener('load', function() {
                renderSubOverlayAnnotations(currentSubAnnotations);
            });
        }
    }
    
    window.addEventListener('resize', function() {
        renderSubOverlayAnnotations(currentSubAnnotations);
    });
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>