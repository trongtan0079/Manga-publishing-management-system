<?php 
/**
 * View: Tạo trang truyện mới
 * Khai báo biến để Editor/IDE hiểu và không báo lỗi gạch đỏ
 * @var array $chapter Thông tin chapter hiện tại
 * @var array $series Thông tin bộ truyện hiện tại
 */
$pageTitle = 'Thêm Trang Mới';
$current_page = 'series';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<!-- Nút quay lại trang chi tiết Chapter -->
<div class="mb-3">
    <a href="<?= BASE_PATH ?>/index.php?controller=chapter&action=show&id=<?= htmlspecialchars($chapter['chapter_id']) ?>" class="btn btn-secondary">&larr; Quay lại Chapter</a>
</div>

<div class="card">
    <div class="card-header">
        <h4>Thêm Trang Mới cho Chapter <?= htmlspecialchars($chapter['chapter_number']) ?></h4>
    </div>
    <div class="card-body">
        <!-- Form upload cần thuộc tính enctype="multipart/form-data" để xử lý file -->
        <form action="<?= BASE_PATH ?>/index.php?controller=page&action=store" method="POST" enctype="multipart/form-data">
            
            <!-- Truyền ẩn chapter_id để controller biết trang thuộc chapter nào -->
            <input type="hidden" name="chapter_id" value="<?= htmlspecialchars($chapter['chapter_id']) ?>">
            
            <!-- Trường nhập số thứ tự trang -->
            <div class="mb-3">
                <label for="page_number" class="form-label">Số trang <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="page_number" name="page_number" min="1" required>
                <div class="form-text">Số trang phải lớn hơn 0 và không được trùng với các trang đã có trong chapter.</div>
            </div>
            
            <!-- Trường chọn file ảnh -->
            <div class="mb-3">
                <label for="image" class="form-label fw-bold">File ảnh <span class="text-danger">*</span></label>
                <div class="upload-dropzone position-relative d-flex flex-column align-items-center justify-content-center border border-dashed rounded-3 p-4 bg-light text-center" id="dropzone" style="cursor: pointer; transition: background-color 0.2s, border-color 0.2s; border-width: 2px !important; border-color: #cbd5e1 !important; min-height: 140px;">
                    <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp" required class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer; z-index: 2;">
                    <div class="upload-icon-wrapper mb-2" style="width: 40px; height: 40px; background: rgba(79, 70, 229, 0.08); color: #4f46e5; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                        <i class="fas fa-image fs-5"></i>
                    </div>
                    <h6 class="fw-semibold mb-1" id="upload-status-text" style="font-size: 0.85rem;">Kéo thả ảnh vào đây hoặc click để chọn</h6>
                    <p class="text-xs text-muted mb-0" style="font-size: 0.7rem;">Định dạng: JPG, JPEG, PNG, WEBP (Tối đa 10MB)</p>
                </div>
            </div>

            <!-- Trường chọn trạng thái trang -->
            <div class="mb-3">
                <label for="status" class="form-label fw-bold">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="drafting" selected>Bản nháp (Drafting)</option>
                    <option value="drawing">Đang vẽ (Drawing)</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i>Lưu Trang</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('image');
    const dropzone = document.getElementById('dropzone');
    const statusText = document.getElementById('upload-status-text');
    const iconWrapper = dropzone.querySelector('.upload-icon-wrapper');

    if (fileInput && dropzone) {
        fileInput.addEventListener('change', function() {
            if (fileInput.files && fileInput.files[0]) {
                const file = fileInput.files[0];
                statusText.innerHTML = `<span class="text-success"><i class="fas fa-check-circle me-1"></i><strong>${file.name}</strong></span> <span class="text-muted" style="font-size: 0.75rem;">(${(file.size / (1024 * 1024)).toFixed(2)} MB)</span>`;
                dropzone.style.borderColor = "#10b981"; 
                dropzone.style.backgroundColor = "rgba(16, 185, 129, 0.02)";
                iconWrapper.style.backgroundColor = "rgba(16, 185, 129, 0.1)";
                iconWrapper.style.color = "#10b981";
                iconWrapper.innerHTML = '<i class="fas fa-check fs-5"></i>';
            } else {
                statusText.textContent = "Kéo thả ảnh vào đây hoặc click để chọn";
                dropzone.style.borderColor = "#cbd5e1";
                dropzone.style.backgroundColor = "#f8fafc";
                iconWrapper.style.backgroundColor = "rgba(79, 70, 229, 0.08)";
                iconWrapper.style.color = "#4f46e5";
                iconWrapper.innerHTML = '<i class="fas fa-image fs-5"></i>';
            }
        });

        // Highlights on drag
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropzone.style.borderColor = "#4f46e5";
                dropzone.style.backgroundColor = "rgba(79, 70, 229, 0.04)";
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, () => {
                if (!fileInput.files || !fileInput.files[0]) {
                    dropzone.style.borderColor = "#cbd5e1";
                    dropzone.style.backgroundColor = "#f8fafc";
                }
            }, false);
        });
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
