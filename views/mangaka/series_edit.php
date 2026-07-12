<?php 
/**
 * View: Giao diện chỉnh sửa bộ truyện (series_edit.php)
 * Vai trò: Mangaka (Họa sĩ chính)
 * Chức năng: Cho phép tác giả cập nhật thông tin tên bộ truyện, mô tả của bộ truyện đang chọn.
 * 
 * @var array $series Thông tin bộ truyện hiện tại cần cập nhật
 */
$pageTitle = 'Chỉnh sửa Series: ' . htmlspecialchars($series['title']);
$current_page = 'series';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<!-- Nút quay lại -->
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
        <h4>Chỉnh sửa Series: <?= htmlspecialchars($series['title']) ?></h4>
    </div>
    <div class="card-body">
        <!-- Form cập nhật, action trỏ tới update với series_id tương ứng -->
        <form action="<?= BASE_PATH ?>/index.php?controller=series&action=update&id=<?= $series['series_id'] ?>" method="POST" enctype="multipart/form-data">
            
            <div class="mb-3">
                <label for="title" class="form-label">Tên Series <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($series['title']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">Tải ảnh bìa mới (từ thiết bị)</label>
                <div class="upload-dropzone border border-dashed rounded-3 p-4 text-center bg-light position-relative hover-shadow" 
                     style="border-color: #cbd5e1 !important; transition: all 0.2s ease-in-out; cursor: pointer;"
                     onclick="document.getElementById('cover_file').click();"
                     onmouseover="this.style.borderColor = 'var(--primary)'; this.style.backgroundColor = 'var(--primary-soft)';"
                     onmouseout="this.style.borderColor = '#cbd5e1'; this.style.backgroundColor = '#f8fafc';">
                    
                    <input type="file" id="cover_file" name="cover_file" accept=".jpg,.jpeg,.png,.webp" style="display: none;" onchange="updateFileName(this);">
                    
                    <div class="upload-icon mb-2">
                        <i class="fas fa-cloud-upload-alt fs-2 text-primary" style="transition: transform 0.2s;" id="uploadIcon"></i>
                    </div>
                    <div class="fw-bold text-slate-700" id="fileNamePlaceholder">Kéo thả ảnh mới vào đây hoặc click để chọn file</div>
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
                    placeholder.innerText = 'Kéo thả ảnh mới vào đây hoặc click để chọn file';
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
                <input type="text" class="form-control" id="cover_image" name="cover_image" value="<?= htmlspecialchars($series['cover_image'] ?? '') ?>">
                <div class="form-text">Có thể để trống nếu đã tải file ảnh bìa lên ở trên.</div>
                <?php if (!empty($series['cover_image'])): 
                    $coverUrl = $series['cover_image'];
                    $resolvedCover = (strpos($coverUrl, 'http') === 0) ? $coverUrl : BASE_PATH . '/' . ltrim($coverUrl, '/');
                ?>
                    <div class="mt-2">
                        <img src="<?= htmlspecialchars($resolvedCover) ?>" alt="Cover Preview" class="img-thumbnail" style="max-height: 150px;">
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="proposal_file" class="form-label fw-bold">Tài liệu đề xuất / Bản thảo sơ bộ (PDF, ZIP, DOCX, RAR)</label>
                <input class="form-control" type="file" id="proposal_file" name="proposal_file" accept=".pdf,.zip,.docx,.doc,.rar,.pptx">
                <div class="form-text text-muted">Đính kèm bản thảo nháp sơ bộ, tài liệu đề xuất giới thiệu nội dung mới nếu cần thay thế file cũ (Tối đa 20MB).</div>
                <?php if (!empty($series['proposal_file'])): ?>
                    <div class="mt-2">
                        <span class="text-success small fw-bold"><i class="fas fa-file-alt me-1"></i>Đã có file đề xuất:</span>
                        <a href="<?= BASE_PATH . htmlspecialchars($series['proposal_file']) ?>" class="btn btn-xs btn-outline-success ms-2 py-0 px-2" target="_blank" style="font-size: 11px;">
                            <i class="fas fa-download me-1"></i>Tải xuống file hiện tại
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label d-block fw-bold">Trạng thái hiện tại</label>
                <?php
                $statusClass = 'bg-secondary';
                $statusLabel = $series['status'];
                if ($series['status'] === 'ongoing') { $statusClass = 'bg-success'; $statusLabel = 'Đang triển khai'; }
                elseif ($series['status'] === 'planning') { $statusClass = 'bg-warning text-dark'; $statusLabel = 'Bản nháp / Chờ duyệt'; }
                elseif ($series['status'] === 'completed') { $statusClass = 'bg-info text-dark'; $statusLabel = 'Hoàn thành'; }
                elseif ($series['status'] === 'suspended') { $statusClass = 'bg-dark'; $statusLabel = 'Tạm ngưng'; }
                elseif ($series['status'] === 'canceled') { $statusClass = 'bg-danger'; $statusLabel = 'Đã hủy'; }
                ?>
                <span class="badge <?= $statusClass ?> px-3 py-2 fs-6"><?= htmlspecialchars($statusLabel) ?></span>
                <div class="form-text">Trạng thái này được phê duyệt và quản lý bởi Hội đồng Biên tập (Editorial Board).</div>
            </div>

            <div class="mb-3">
                <label class="form-label d-block fw-bold">Lịch xuất bản</label>
                <?php if ($series['status'] === 'planning'): ?>
                    <span class="badge bg-light text-dark border px-3 py-2 fs-6">Chưa quyết định (Chờ duyệt)</span>
                <?php else: ?>
                    <span class="badge bg-secondary px-3 py-2 fs-6"><?= (($series['publish_type'] ?? 'weekly') === 'weekly' ? 'Hàng tuần' : 'Hàng tháng') ?></span>
                <?php endif; ?>
                <div class="form-text">Lịch xuất bản do Hội đồng Biên tập quyết định khi phê duyệt tác phẩm.</div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label fw-bold text-slate-700">Mô tả bộ truyện</label>
                <!-- Hidden textarea to store the HTML content for backend submission -->
                <textarea id="description" name="description" style="display: none;"><?= htmlspecialchars($series['description'] ?? '') ?></textarea>
                
                <!-- Quill container -->
                <div id="quill-editor"></div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Quill Editor
                const quill = new Quill('#quill-editor', {
                    theme: 'snow',
                    placeholder: 'Nhập tóm tắt hoặc mô tả ngắn cho bộ truyện...',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline', 'strike'],        // text styling
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],     // lists
                            ['clean']                                         // clear formatting
                        ]
                    }
                });

                // Preload existing description HTML safely
                const oldContent = document.getElementById('description').value;
                if (oldContent) {
                    quill.root.innerHTML = oldContent;
                }

                // Sync Quill editor HTML with the hidden textarea on submit
                const form = document.querySelector('form');
                if (form) {
                    form.addEventListener('submit', function() {
                        const descriptionTextarea = document.getElementById('description');
                        if (quill.getText().trim().length > 0) {
                            descriptionTextarea.value = quill.root.innerHTML;
                        } else {
                            descriptionTextarea.value = '';
                        }
                    });
                }
            });
            </script>

            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
