<?php 
if (!defined('BASE_PATH')) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $pos = strpos($scriptName, '/views/');
    $projectUrl = ($pos !== false) ? substr($scriptName, 0, $pos) : '';
    header('Location: ' . $projectUrl . '/index.php');
    exit;
}
/**
 * View: Chỉnh sửa trang truyện
 * Khai báo biến để Editor/IDE hiểu và không báo lỗi
 * @var array $page Thông tin chi tiết của trang hiện tại
 * @var array $chapter Thông tin chapter chứa trang này
 * @var array $series Thông tin bộ truyện
 */
$pageTitle = 'Chỉnh sửa Trang ' . htmlspecialchars($page['page_number']);
$current_page = 'series';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
$isLocked = ($this->isChapterLocked($chapter) || in_array($page['status'], ['approved', 'published']));
?>

<!-- Nút quay lại trang chi tiết Chapter -->
<div class="mb-3">
    <a href="<?= BASE_PATH ?>/index.php?controller=chapter&action=show&id=<?= htmlspecialchars($chapter['chapter_id']) ?>" class="btn btn-secondary">&larr; Quay lại Chapter</a>
</div>

<div class="card">
    <div class="card-header">
        <h4>Chỉnh sửa Trang <?= htmlspecialchars($page['page_number']) ?> (Chapter <?= htmlspecialchars($chapter['chapter_number']) ?>)</h4>
    </div>
    <div class="card-body">
        <!-- Form upload cần thuộc tính enctype="multipart/form-data" để xử lý file thay thế nếu có -->
        <form action="<?= BASE_PATH ?>/index.php?controller=page&action=update&id=<?= $page['page_id'] ?>" method="POST" enctype="multipart/form-data">
            <?php if ($isLocked): ?>
                <div class="alert alert-warning border-0 py-2.5 px-3 mb-4 d-flex align-items-center gap-2" style="font-size: 0.85rem; border-radius: 8px; background-color: #fffbeb; color: #b45309;">
                    <i class="fas fa-lock fs-6"></i>
                    <div><strong>Lưu ý:</strong> Trang vẽ này hoặc chương truyện chứa nó đã được duyệt hoặc xuất bản. Biểu mẫu chỉnh sửa đã bị khóa để bảo toàn dữ liệu.</div>
                </div>
            <?php endif; ?>
            
            <!-- Trường số thứ tự trang -->
            <div class="mb-3">
                <label for="page_number" class="form-label fw-bold">Số trang <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="page_number" name="page_number" value="<?= htmlspecialchars($page['page_number']) ?>" min="1" required <?= $isLocked ? 'disabled' : '' ?>>
                <div class="form-text text-muted">Số trang phải lớn hơn 0 và không được trùng với các trang khác trong chapter.</div>
                <div class="text-danger mt-1 d-none" id="page-number-warning" style="font-size: 0.8rem; font-weight: 500;">
                    <i class="fas fa-exclamation-circle me-1"></i>Số trang này đã tồn tại trong chapter này. Vui lòng chọn số khác!
                </div>
            </div>
            
            <!-- Khối hiển thị ảnh hiện tại và tùy chọn thay thế -->
            <div class="mb-3">
                <label class="form-label">Ảnh hiện tại</label>
                <div>
                    <!-- Nếu trang đã có ảnh thì hiển thị Thumbnail -->
                    <?php if (!empty($page['image_url'])): ?>
                        <img src="<?= BASE_PATH . '/' . ltrim($page['image_url'], '/') ?>" alt="Current Page Image" class="img-thumbnail mb-2" style="max-height: 200px;">
                    <?php else: ?>
                        <p class="text-muted">Chưa có ảnh</p>
                    <?php endif; ?>
                </div>
                
                <!-- Input để tải lên ảnh mới -->
                <label for="image" class="form-label mt-2 fw-bold">Thay đổi file ảnh (Không bắt buộc)</label>
                <div class="upload-dropzone position-relative d-flex flex-column align-items-center justify-content-center border border-dashed rounded-3 p-4 bg-light text-center" id="dropzone" style="cursor: pointer; transition: background-color 0.2s, border-color 0.2s; border-width: 2px !important; border-color: #cbd5e1 !important; min-height: 140px; <?= $isLocked ? 'pointer-events: none; opacity: 0.6;' : '' ?>">
                    <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp" <?= $isLocked ? 'disabled' : '' ?> class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer; z-index: 2;">
                    <div class="upload-icon-wrapper mb-2" style="width: 40px; height: 40px; background: rgba(79, 70, 229, 0.08); color: #4f46e5; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                        <i class="fas fa-image fs-5"></i>
                    </div>
                    <h6 class="fw-semibold mb-1" id="upload-status-text" style="font-size: 0.85rem;">Kéo thả ảnh vào đây hoặc click để thay thế</h6>
                    <p class="text-xs text-muted mb-0" style="font-size: 0.7rem;">Định dạng: JPG, JPEG, PNG, WEBP (Tối đa 10MB)</p>
                </div>
            </div>

            <!-- Trường chọn trạng thái -->
            <div class="mb-3">
                <label for="status" class="form-label fw-bold">Trạng thái</label>
                <select class="form-select" id="status" name="status" <?= $isLocked ? 'disabled' : '' ?>>
                    <option value="drafting" <?= $page['status'] === 'drafting' ? 'selected' : '' ?>>Phác thảo Kịch bản (Storyboard)</option>
                    <option value="drawing" <?= $page['status'] === 'drawing' ? 'selected' : '' ?>>Đang vẽ Chi tiết (Drawing)</option>
                    <?php if (in_array($page['status'], ['reviewing_draft', 'reviewing_final', 'approved', 'published'])): ?>
                        <?php if ($page['status'] === 'reviewing_draft'): ?>
                            <option value="reviewing_draft" selected disabled>Chờ duyệt Kịch bản</option>
                        <?php elseif ($page['status'] === 'reviewing_final'): ?>
                            <option value="reviewing_final" selected disabled>Chờ duyệt Bản vẽ</option>
                        <?php endif; ?>
                        <option value="approved" <?= $page['status'] === 'approved' ? 'selected' : '' ?> disabled>Đã duyệt phát hành (Approved)</option>
                        <option value="published" <?= $page['status'] === 'published' ? 'selected' : '' ?> disabled>Đã xuất bản (Published)</option>
                    <?php endif; ?>
                </select>
                <?php if (!$isLocked && ($chapter['status'] === 'drafting' || $chapter['status'] === 'reviewing_draft')): ?>
                    <div class="form-text text-warning mt-2" style="font-size: 0.8rem; font-weight: 500;">
                        <i class="fas fa-exclamation-triangle me-1"></i> <strong>Lưu ý:</strong> Vì chương truyện đang ở trạng thái Kịch bản thô (Drafting/Reviewing), các trợ lý sẽ chưa nhận được thông báo và các công việc (Tasks) trên trang này sẽ được ẩn tạm thời cho đến khi kịch bản được Editor duyệt thông qua.
                    </div>
                    <div class="form-text text-info mt-1" style="font-size: 0.78rem;">
                        <i class="fas fa-info-circle me-1"></i> <strong>Quan trọng:</strong> Nếu chương truyện này vừa bị Editor từ chối duyệt kịch bản và yêu cầu chỉnh sửa, vui lòng đảm bảo bạn đã tải lên file ảnh kịch bản mới đã được sửa đổi trước khi đặt trạng thái trang là <em>"Đang vẽ (Drawing)"</em>, nhằm tránh việc trợ lý vẽ nhầm trên bản kịch bản cũ sau khi chương được duyệt lại.
                    </div>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn btn-warning px-4" <?= $isLocked ? 'disabled' : '' ?>><i class="fas fa-save me-1"></i>Cập nhật Trang</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('image');
    const dropzone = document.getElementById('dropzone');
    const statusText = document.getElementById('upload-status-text');
    const iconWrapper = dropzone.querySelector('.upload-icon-wrapper');
    
    // Validate số trang trùng lặp thời gian thực
    const pageInput = document.getElementById('page_number');
    const pageWarning = document.getElementById('page-number-warning');
    const submitBtn = document.querySelector('button[type="submit"]');
    const existingPages = <?= json_encode(array_map('intval', $existingPageNumbers ?? [])) ?>;

    if (pageInput && pageWarning && !pageInput.disabled) {
        pageInput.addEventListener('input', function() {
            const val = parseInt(pageInput.value, 10);
            if (existingPages.includes(val)) {
                pageInput.classList.add('is-invalid');
                pageWarning.classList.remove('d-none');
                if (submitBtn) submitBtn.disabled = true;
            } else {
                pageInput.classList.remove('is-invalid');
                pageWarning.classList.add('d-none');
                if (submitBtn) submitBtn.disabled = false;
            }
        });
    }

    if (fileInput && dropzone && !fileInput.disabled) {
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
                statusText.textContent = "Kéo thả ảnh vào đây hoặc click để thay thế";
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
