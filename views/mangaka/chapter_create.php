<?php 
/**
 * Khai báo biến để Editor/IDE hiểu và không báo lỗi gạch đỏ
 * @var array $series Thông tin bộ truyện
 */
$pageTitle = 'Tạo Chapter Mới';
$current_page = 'series';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="mb-3">
    <a href="<?= BASE_PATH ?>/index.php?controller=series&action=show&id=<?= htmlspecialchars($series['series_id']) ?>" class="btn btn-secondary">&larr; Quay lại Bộ truyện</a>
</div>

<?php require_once __DIR__ . '/../layouts/alerts.php'; ?>

<div class="card border-primary mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Tạo Chapter Mới</h5>
    </div>
    <div class="card-body">
        <form action="<?= BASE_PATH ?>/index.php?controller=chapter&action=store" method="POST">
            <input type="hidden" name="series_id" value="<?= htmlspecialchars($series['series_id']) ?>">
            
            <div class="mb-3">
                <label for="chapter_number" class="form-label">Số Chapter <span class="text-danger">*</span></label>
                <input type="number" step="any" min="0" class="form-control" id="chapter_number" name="chapter_number" required>
            </div>
            
            <div class="mb-3">
                <label for="title" class="form-label">Tên Chapter</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="Không bắt buộc">
            </div>
            
            <div class="mb-3">
                <label class="form-label d-block"><i class="fas fa-info-circle me-1 text-primary"></i> Trạng thái khởi tạo</label>
                <span class="badge bg-secondary p-2"><i class="fas fa-file-alt me-1"></i> Phác thảo Kịch bản (Storyboard)</span>
                <div class="form-text text-muted mt-2">Mặc định chương mới tạo sẽ ở trạng thái Phác thảo Kịch bản để Biên tập viên kiểm duyệt bố cục phân cảnh trước.</div>
            </div>
            
            <div class="mb-3">
                <label for="due_date" class="form-label">Hạn chót (Due Date)</label>
                <input type="datetime-local" class="form-control" id="due_date" name="due_date">
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_final" name="is_final" value="1">
                <label class="form-check-label fw-semibold text-danger" for="is_final">
                    <i class="fas fa-flag"></i> Đây là chương cuối cùng của bộ truyện (End Chapter)
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary">Tạo Chapter</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
