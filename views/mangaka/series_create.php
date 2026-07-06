<?php 
/**
 * View: Giao diện tạo mới bộ truyện (series_create.php)
 * Vai trò: Mangaka (Họa sĩ chính)
 * Chức năng: Cung cấp biểu mẫu (form) nhập thông tin chi tiết (tên truyện, mô tả) để tạo một dự án truyện mới.
 */
$pageTitle = 'Tạo Series Mới';
$current_page = 'series';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<!-- Nút quay lại trang danh sách -->
<div class="mb-3">
    <a href="<?= BASE_PATH ?>/index.php?controller=series&action=index" class="btn btn-secondary">&larr; Quay lại Danh sách Truyện</a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h4>Tạo Series Mới</h4>
    </div>
    <div class="card-body">
        <!-- Form thêm mới, action trỏ tới method store -->
        <form action="<?= BASE_PATH ?>/index.php?controller=series&action=store" method="POST" enctype="multipart/form-data">
            
            <div class="mb-3">
                <label for="title" class="form-label">Tên Series <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" required placeholder="Nhập tên bộ truyện">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">Tải ảnh bìa lên (từ thiết bị)</label>
                <div class="upload-dropzone border border-dashed rounded-3 p-4 text-center bg-light position-relative hover-shadow" 
                     style="border-color: #cbd5e1 !important; transition: all 0.2s ease-in-out; cursor: pointer;"
                     onclick="document.getElementById('cover_file').click();"
                     onmouseover="this.style.borderColor = 'var(--primary)'; this.style.backgroundColor = 'var(--primary-soft)';"
                     onmouseout="this.style.borderColor = '#cbd5e1'; this.style.backgroundColor = '#f8fafc';">
                    
                    <input type="file" id="cover_file" name="cover_file" accept=".jpg,.jpeg,.png,.webp" style="display: none;" onchange="updateFileName(this);">
                    
                    <div class="upload-icon mb-2">
                        <i class="fas fa-cloud-upload-alt fs-2 text-primary" style="transition: transform 0.2s;" id="uploadIcon"></i>
                    </div>
                    <div class="fw-bold text-slate-700" id="fileNamePlaceholder">Kéo thả ảnh vào đây hoặc click để chọn file</div>
                    <div class="text-xs text-muted mt-1" style="font-size: 0.75rem;">Hỗ trợ: JPG, JPEG, PNG, WEBP (Tối đa 10MB)</div>
                </div>
            </div>

            <script>
            function updateFileName(input) {
                const placeholder = document.getElementById('fileNamePlaceholder');
                const icon = document.getElementById('uploadIcon');
                if (input.files && input.files.length > 0) {
                    placeholder.innerText = input.files[0].name;
                    placeholder.classList.remove('text-slate-700');
                    placeholder.classList.add('text-success', 'fw-bold');
                    icon.className = 'fas fa-check-circle fs-2 text-success';
                } else {
                    placeholder.innerText = 'Kéo thả ảnh vào đây hoặc click để chọn file';
                    placeholder.classList.remove('text-success');
                    placeholder.classList.add('text-slate-700');
                    icon.className = 'fas fa-cloud-upload-alt fs-2 text-primary';
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                const dropzone = document.querySelector('.upload-dropzone');
                if (dropzone) {
                    const fileInput = document.getElementById('cover_file');
                    
                    dropzone.addEventListener('dragover', function(e) {
                        e.preventDefault();
                        this.style.borderColor = 'var(--primary)';
                        this.style.backgroundColor = 'var(--primary-soft)';
                    });
                    
                    dropzone.addEventListener('dragleave', function(e) {
                        e.preventDefault();
                        this.style.borderColor = '#cbd5e1';
                        this.style.backgroundColor = '#f8fafc';
                    });
                    
                    dropzone.addEventListener('drop', function(e) {
                        e.preventDefault();
                        this.style.borderColor = '#cbd5e1';
                        this.style.backgroundColor = '#f8fafc';
                        
                        if (e.dataTransfer.files.length > 0) {
                            fileInput.files = e.dataTransfer.files;
                            updateFileName(fileInput);
                        }
                    });
                }
            });
            </script>

            <div class="mb-3">
                <label for="cover_image" class="form-label">Hoặc nhập đường dẫn ảnh bìa (URL)</label>
                <input type="text" class="form-control" id="cover_image" name="cover_image" placeholder="https://example.com/image.jpg">
                <div class="form-text">Hoặc cung cấp đường dẫn trực tiếp đến ảnh bìa từ internet.</div>
            </div>

            <!-- Status and Publish Type will be managed by the Editorial Board -->
            
            <div class="mb-3">
                <label for="description" class="form-label fw-bold text-slate-700">Mô tả bộ truyện</label>
                <div class="border rounded-3 overflow-hidden shadow-sm hover-shadow" style="transition: all 0.2s; border-color: #cbd5e1 !important;">
                    <div class="d-flex gap-2 p-2 border-bottom bg-light align-items-center" style="background-color: #f8fafc !important; border-color: #e2e8f0 !important;">
                        <button type="button" class="btn btn-sm btn-white border shadow-sm py-1 px-2 d-flex align-items-center justify-content-center" onclick="insertFormatting('description', '**')" title="In đậm (Bold)" style="height: 28px; width: 28px; background-color: #ffffff;"><i class="fas fa-bold text-slate-700 small"></i></button>
                        <button type="button" class="btn btn-sm btn-white border shadow-sm py-1 px-2 d-flex align-items-center justify-content-center" onclick="insertFormatting('description', '*')" title="In nghiêng (Italic)" style="height: 28px; width: 28px; background-color: #ffffff;"><i class="fas fa-italic text-slate-700 small"></i></button>
                        <button type="button" class="btn btn-sm btn-white border shadow-sm py-1 px-2 d-flex align-items-center justify-content-center" onclick="insertFormatting('description', '~~')" title="Gạch ngang (Strikethrough)" style="height: 28px; width: 28px; background-color: #ffffff;"><i class="fas fa-strikethrough text-slate-700 small"></i></button>
                        <button type="button" class="btn btn-sm btn-white border shadow-sm py-1 px-2 d-flex align-items-center justify-content-center" onclick="insertList('description')" title="Danh sách (Bullet list)" style="height: 28px; width: 28px; background-color: #ffffff;"><i class="fas fa-list text-slate-700 small"></i></button>
                    </div>
                    <textarea class="form-control border-0 rounded-0" id="description" name="description" rows="5" style="box-shadow: none !important; resize: vertical; min-height: 120px;" placeholder="Nhập tóm tắt hoặc mô tả ngắn cho bộ truyện..."></textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Tạo Series</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
