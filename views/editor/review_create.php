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
    <a href="<?= BASE_PATH ?>/index.php?controller=review&action=index" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
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
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">Full Chapter</span>
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

                <!-- Khu vực hiển thị tệp tin/hình ảnh đính kèm -->
                <?php if ($submission['file_url']): ?>
                    <div class="mb-4">
                        <p class="fw-bold text-muted mb-2 border-bottom pb-2">File đính kèm:</p>
                        <?php $ext = pathinfo($submission['file_url'], PATHINFO_EXTENSION); ?>
                        <?php if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                            <div class="text-center bg-light p-2 rounded border">
                                <img src="<?= BASE_PATH ?>/<?= htmlspecialchars((string)($submission['file_url'] ?? '')) ?>" class="img-fluid rounded shadow-sm" alt="Submission file" style="max-height: 500px;">
                            </div>
                        <?php else: ?>
                            <a href="<?= BASE_PATH ?>/<?= htmlspecialchars((string)($submission['file_url'] ?? '')) ?>" class="btn btn-outline-primary" target="_blank">
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
                                    <i class="fas fa-check-circle me-2"></i> Approve (Phê duyệt)
                                </label>
                            </div>
                            <div class="form-check custom-radio mt-2">
                                <input class="form-check-input" type="radio" name="decision" id="decision_reject" value="rejected" required>
                                <label class="form-check-label text-danger fw-bold w-100" for="decision_reject">
                                    <i class="fas fa-times-circle me-2"></i> Reject (Yêu cầu sửa/Từ chối)
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

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>