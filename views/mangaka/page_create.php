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

<style>
    /* Premium Dropzone Styling */
    .upload-dropzone {
        border: 2px dashed var(--slate-300) !important;
        background-color: var(--slate-50);
        border-radius: 16px;
        min-height: 200px;
        cursor: pointer;
        transition: all var(--transition);
        position: relative;
        overflow: hidden;
    }
    .upload-dropzone:hover {
        border-color: var(--primary) !important;
        background-color: rgba(99, 102, 241, 0.02);
    }
    .upload-dropzone.dragover {
        border-color: var(--primary) !important;
        background-color: rgba(99, 102, 241, 0.05);
        transform: scale(1.01);
    }
    .preview-thumbnail-container {
        position: relative;
        max-width: 180px;
        margin: 0 auto;
    }
    .btn-remove-preview {
        position: absolute;
        top: -8px;
        right: -8px;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 10;
        padding: 0;
        border: none;
    }
</style>

<!-- Nút quay lại trang chi tiết Chapter -->
<div class="mb-4">
    <a href="<?= BASE_PATH ?>/index.php?controller=chapter&action=show&id=<?= htmlspecialchars($chapter['chapter_id']) ?>" class="btn btn-secondary border d-inline-flex align-items-center gap-2" style="font-size: 0.85rem; font-weight: 600; border-radius: var(--radius);">
        <i class="fas fa-arrow-left"></i> Quay lại Chapter <?= htmlspecialchars($chapter['chapter_number']) ?>
    </a>
</div>

<div class="card border-0 shadow-sm" style="border-top: 4px solid var(--primary) !important; border-radius: var(--radius-md);">
    <div class="card-header bg-white border-bottom pt-3 pb-3">
        <h4 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 1.2rem;">
            <i class="fas fa-file-image text-primary"></i> Thêm Trang Mới cho Chapter <?= htmlspecialchars($chapter['chapter_number']) ?>
        </h4>
    </div>
    <div class="card-body p-4">
        <!-- Form upload cần thuộc tính enctype="multipart/form-data" để xử lý file -->
        <form action="<?= BASE_PATH ?>/index.php?controller=page&action=store" method="POST" enctype="multipart/form-data">
            
            <!-- Truyền ẩn chapter_id để controller biết trang thuộc chapter nào -->
            <input type="hidden" name="chapter_id" value="<?= htmlspecialchars($chapter['chapter_id']) ?>">
            
            <div class="row g-4 mb-4">
                <!-- Trường nhập số thứ tự trang -->
                <div class="col-md-6">
                    <label for="page_number" class="form-label fw-bold text-dark"><i class="fas fa-list-ol text-muted me-1"></i> Số thứ tự trang <span class="text-danger">*</span></label>
                    <input type="number" class="form-control py-2.5" id="page_number" name="page_number" min="1" placeholder="Ví dụ: 1" required style="border-radius: var(--radius);">
                    <div class="form-text text-muted mt-1.5" style="font-size: 0.775rem;"><i class="fas fa-info-circle me-1"></i> Số trang phải lớn hơn 0 và không trùng với các trang hiện tại.</div>
                </div>

                <!-- Trường chọn trạng thái trang -->
                <div class="col-md-6">
                    <label for="status" class="form-label fw-bold text-dark"><i class="fas fa-tasks text-muted me-1"></i> Trạng thái khởi tạo</label>
                    <select class="form-select py-2.5" id="status" name="status" style="border-radius: var(--radius);">
                        <option value="drafting" selected>Bản nháp (Drafting)</option>
                        <option value="drawing">Đang vẽ (Drawing)</option>
                        <option value="reviewing">Đang chờ duyệt (Reviewing)</option>
                        <option value="approved">Đã duyệt (Approved)</option>
                        <option value="published">Đã xuất bản (Published)</option>
                    </select>
                    <div class="form-text text-muted mt-1.5" style="font-size: 0.775rem;"><i class="fas fa-info-circle me-1"></i> Trạng thái ban đầu của trang truyện trong quy trình.</div>
                </div>
            </div>
            
            <!-- Trường chọn file ảnh dạng Drag & Drop Dropzone -->
            <div class="mb-4">
                <label class="form-label fw-bold text-dark"><i class="fas fa-image text-muted me-1"></i> Tải lên hình ảnh trang truyện <span class="text-danger">*</span></label>
                
                <div class="upload-dropzone p-4 text-center d-flex flex-column align-items-center justify-content-center" id="dropzone">
                    <!-- Input file ẩn bao phủ vùng dropzone -->
                    <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer; z-index: 2;" required>
                    
                    <!-- Trạng thái mặc định chưa chọn file -->
                    <div id="dropzone-default" class="d-flex flex-column align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: rgba(99, 102, 241, 0.08); color: var(--primary);">
                            <i class="fas fa-cloud-upload-alt fs-4"></i>
                        </div>
                        <div>
                            <span class="fw-bold text-dark d-block" style="font-size: 0.95rem;">Kéo và thả file ảnh vào đây</span>
                            <span class="text-muted text-sm">hoặc click để chọn từ thiết bị của bạn</span>
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.775rem;"><i class="fas fa-info-circle me-1"></i> Định dạng hỗ trợ: JPG, JPEG, PNG, WEBP. Tối đa 10MB.</div>
                    </div>
                    
                    <!-- Trạng thái xem trước khi đã chọn file -->
                    <div id="dropzone-preview" class="d-none w-100 flex-column align-items-center gap-3" style="position: relative; z-index: 3;">
                        <div class="preview-thumbnail-container">
                            <img id="image-preview" src="#" alt="Preview" class="img-thumbnail shadow-sm" style="max-height: 160px; object-fit: contain; border-radius: 8px;">
                            <button type="button" class="btn btn-danger btn-remove-preview" id="btn-remove-file" title="Xóa tệp chọn lại">
                                <i class="fas fa-times" style="font-size: 0.75rem;"></i>
                            </button>
                        </div>
                        <div>
                            <span class="fw-bold text-dark text-truncate d-block px-3" id="file-name" style="font-size: 0.85rem; max-width: 320px;">Tên file</span>
                            <small class="text-success fw-bold d-block mt-1" style="font-size: 0.75rem;"><i class="fas fa-check-circle me-1"></i> Sẵn sàng tải lên</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary px-5 py-2.5 shadow-sm" style="border-radius: var(--radius); font-weight: 700; background: linear-gradient(135deg, var(--primary) 0%, #1e1b4b 100%); border: none;">
                <i class="fas fa-save me-2"></i> Lưu Trang Truyện
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const fileInput = document.getElementById("image");
    const dropzone = document.getElementById("dropzone");
    const defaultState = document.getElementById("dropzone-default");
    const previewState = document.getElementById("dropzone-preview");
    const previewImage = document.getElementById("image-preview");
    const fileNameText = document.getElementById("file-name");
    const btnRemove = document.getElementById("btn-remove-file");
    
    // Đổi style khi drag file lên vùng dropzone
    fileInput.addEventListener("dragenter", () => {
        dropzone.classList.add("dragover");
    });
    fileInput.addEventListener("dragleave", () => {
        dropzone.classList.remove("dragover");
    });
    fileInput.addEventListener("drop", () => {
        dropzone.classList.remove("dragover");
    });
    
    // Lắng nghe sự kiện chọn file
    fileInput.addEventListener("change", function(e) {
        const file = e.target.files[0];
        if (file) {
            // Kiểm tra định dạng ảnh
            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert("Định dạng file không hợp lệ! Vui lòng chọn tệp ảnh JPG, PNG hoặc WEBP.");
                fileInput.value = "";
                return;
            }
            // Kiểm tra dung lượng
            if (file.size > 10 * 1024 * 1024) {
                alert("Dung lượng file vượt quá 10MB! Vui lòng chọn file nhỏ hơn.");
                fileInput.value = "";
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(event) {
                previewImage.src = event.target.result;
                fileNameText.textContent = file.name;
                
                defaultState.classList.add("d-none");
                previewState.classList.remove("d-none");
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Nút xóa file đã chọn để chọn lại
    btnRemove.addEventListener("click", function(e) {
        e.preventDefault();
        e.stopPropagation();
        fileInput.value = ""; // Reset input file
        defaultState.classList.remove("d-none");
        previewState.classList.add("d-none");
        previewImage.src = "#";
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
