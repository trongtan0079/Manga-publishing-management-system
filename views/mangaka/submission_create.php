<?php
/**
 * View: Giao diện nộp sản phẩm chương truyện (submission_create.php)
 * Vai trò: Mangaka (Họa sĩ chính)
 * Chức năng: Cho phép họa sĩ chính nộp toàn bộ chương truyện (Chapter) hoàn thiện lên Biên tập viên để xét duyệt và xuất bản.
 * 
 * @var array $chapters Danh sách các chương truyện thuộc quyền sở hữu của Mangaka này đang chờ nộp/duyệt
 */
$pageTitle = 'Nộp sản phẩm Chapter';
$current_page = 'submissions';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Nộp Chapter cho Editor</h2>
        <p class="text-muted text-xs mb-0">Nộp toàn bộ chương truyện (Chapter) đã hoàn thành cho Biên tập viên phụ trách đánh giá.</p>
    </div>
    <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=index" class="btn btn-outline-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left me-2"></i>Lịch sử nộp bài
    </a>
</div>

<div class="row">
    <div class="col-lg-8 col-md-10 mx-auto">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white text-dark py-3 border-bottom border-light">
                <h5 class="card-title mb-0"><i class="fas fa-book-open me-2 text-primary"></i>Thông tin nộp Chapter</h5>
            </div>
            <div class="card-body p-4">
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="<?= BASE_PATH ?>/index.php?controller=submission&action=store" method="POST" enctype="multipart/form-data">
                    
                    <!-- Chọn Chương truyện (Chapter) -->
                    <div class="mb-4">
                        <label for="chapter_id" class="form-label fw-bold text-dark"><i class="fas fa-layer-group me-2 text-muted"></i>Chọn Chương (Chapter) <span class="text-danger">*</span></label>
                        <select class="form-select" id="chapter_id" name="chapter_id" required>
                            <option value="" disabled selected>-- Chọn chương cần nộp --</option>
                            <?php if (!empty($chapters)): ?>
                                <?php foreach ($chapters as $c): ?>
                                    <option value="<?= $c['chapter_id'] ?>">
                                        <?= htmlspecialchars($c['series_title']) ?> - Ch.<?= htmlspecialchars($c['chapter_number']) ?>: <?= htmlspecialchars($c['title'] ?? 'Chưa đặt tên') ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>Không tìm thấy chương nào thuộc Series của bạn</option>
                            <?php endif; ?>
                        </select>
                        <div class="form-text text-muted">Chỉ hiển thị các chương truyện thuộc Series mà bạn làm tác giả.</div>
                    </div>

                    <!-- Upload File -->
                    <div class="mb-4">
                        <label for="file" class="form-label fw-bold text-dark"><i class="fas fa-file-archive me-2 text-muted"></i>Tải lên bản vẽ hoàn chỉnh <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" id="file" name="file" accept=".jpg,.jpeg,.png,.pdf,.zip" required>
                        <div class="form-text text-muted mt-2">
                            <span class="badge bg-light text-dark border me-1">Định dạng hỗ trợ:</span> <code>jpg, jpeg, png, pdf, zip</code>
                            <br>
                            <span class="badge bg-light text-dark border me-1">Dung lượng tối đa:</span> <code>20MB</code>
                        </div>
                    </div>

                    <!-- Ghi chú (Notes) -->
                    <div class="mb-4">
                        <label for="notes" class="form-label fw-bold text-dark"><i class="fas fa-comment-alt me-2 text-muted"></i>Ghi chú cho Biên tập viên (Editor)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Nhập lời nhắn hoặc ghi chú kèm theo bản thảo..."></textarea>
                    </div>

                    <!-- Nút bấm -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=index" class="btn btn-light px-4">Hủy</a>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm"><i class="fas fa-paper-plane me-2"></i>Nộp Bản Thảo</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
