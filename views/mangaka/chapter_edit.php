<?php 
/**
 * Khai báo biến để Editor/IDE hiểu và không báo lỗi gạch đỏ
 * @var array $series Thông tin bộ truyện
 * @var array $chapter Thông tin chapter đang chỉnh sửa
 */
$pageTitle = 'Chỉnh sửa Chapter';
$current_page = 'series';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="mb-3">
    <a href="<?= BASE_PATH ?>/index.php?controller=series&action=show&id=<?= htmlspecialchars($series['series_id']) ?>" class="btn btn-secondary">&larr; Quay lại Bộ truyện</a>
</div>

<div class="card border-warning mb-4">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">Chỉnh sửa Chapter</h5>
    </div>
    <div class="card-body">
        <form action="<?= BASE_PATH ?>/index.php?controller=chapter&action=update&id=<?= $chapter['chapter_id'] ?>" method="POST">
            
            <div class="mb-3">
                <label for="chapter_number" class="form-label">Số Chapter <span class="text-danger">*</span></label>
                <input type="number" step="any" min="0" class="form-control" id="chapter_number" name="chapter_number" value="<?= htmlspecialchars($chapter['chapter_number']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="title" class="form-label">Tên Chapter</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($chapter['title'] ?? '') ?>" placeholder="Không bắt buộc">
            </div>
            
            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="drafting" <?= $chapter['status'] === 'drafting' ? 'selected' : '' ?>>Bản nháp (Drafting)</option>
                    <option value="drawing" <?= $chapter['status'] === 'drawing' ? 'selected' : '' ?>>Đang vẽ (Drawing)</option>
                    <option value="reviewing" <?= $chapter['status'] === 'reviewing' ? 'selected' : '' ?>>Đang chờ duyệt (Reviewing)</option>
                    <?php if ($chapter['status'] === 'approved' || $chapter['status'] === 'published'): ?>
                        <option value="approved" <?= $chapter['status'] === 'approved' ? 'selected' : '' ?> disabled>Đã duyệt (Approved)</option>
                        <option value="published" <?= $chapter['status'] === 'published' ? 'selected' : '' ?> disabled>Đã xuất bản (Published)</option>
                    <?php endif; ?>
                </select>
                <div id="status-warning-container"></div>
            </div>

            <div class="mb-3">
                <label for="due_date" class="form-label">Hạn chót (Due Date)</label>
                <?php 
                $dueDateVal = '';
                if (!empty($chapter['due_date'])) {
                    $dueDateVal = date('Y-m-d\TH:i', strtotime($chapter['due_date']));
                }
                ?>
                <input type="datetime-local" class="form-control" id="due_date" name="due_date" value="<?= $dueDateVal ?>">
            </div>
            
            <button type="submit" class="btn btn-warning">Lưu thay đổi</button>
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
                    <div>Các phân công công việc vẽ cho Trợ lý sẽ được <strong>kích hoạt hiển thị</strong> và <strong>gửi thông báo ngay lập tức</strong> (nếu trước đó bị tạm giữ ở Bản nháp).</div>
                </div>
            `;
        } else if (status === 'reviewing') {
            warningHtml = `
                <div class="alert alert-info border-0 py-2 px-3 mt-2 d-flex align-items-start gap-2" style="font-size: 0.8rem; border-radius: 8px; background-color: #f0f9ff; color: #0369a1;">
                    <i class="fas fa-paper-plane mt-1 flex-shrink-0"></i>
                    <div>Nộp toàn bộ bản thảo chương truyện lên Biên tập viên để thực hiện <strong>kiểm duyệt chất lượng và nội dung</strong>.</div>
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
