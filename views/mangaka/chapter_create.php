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
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="drafting">Bản nháp (Drafting)</option>
                    <option value="drawing">Đang vẽ (Drawing)</option>
                </select>
                <div id="status-warning-container"></div>
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

<script>
document.addEventListener("DOMContentLoaded", function() {
    const statusSelect = document.getElementById("status");
    const warningContainer = document.getElementById("status-warning-container");
    
    function updateStatusWarning() {
        const status = statusSelect.value;
        let warningHtml = "";
        
        if (status === 'drafting') {
            warningHtml = `
                <div class="alert alert-warning border-0 py-2 px-3 mt-2 d-flex align-items-start gap-2" style="font-size: 0.8rem; border-radius: 8px; background-color: #fffbeb; color: #b45309;">
                    <i class="fas fa-exclamation-triangle mt-1 flex-shrink-0"></i>
                    <div><strong>Lưu ý:</strong> Trợ lý sẽ tạm thời <strong>không nhìn thấy công việc</strong> và <strong>không nhận thông báo</strong> giao việc khi chương ở trạng thái Nháp.</div>
                </div>
            `;
        } else if (status === 'drawing') {
            warningHtml = `
                <div class="alert alert-success border-0 py-2 px-3 mt-2 d-flex align-items-start gap-2" style="font-size: 0.8rem; border-radius: 8px; background-color: #f0fdf4; color: #15803d;">
                    <i class="fas fa-check-circle mt-1 flex-shrink-0"></i>
                    <div>Các phân công công việc vẽ cho Trợ lý sẽ được <strong>kích hoạt hiển thị</strong> và <strong>gửi thông báo ngay lập tức</strong>.</div>
                </div>
            `;
        }
        
        warningContainer.innerHTML = warningHtml;
    }
    
    statusSelect.addEventListener("change", updateStatusWarning);
    updateStatusWarning();
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
