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
                <label for="cover_file" class="form-label">Tải ảnh bìa lên (từ thiết bị)</label>
                <input type="file" class="form-control" id="cover_file" name="cover_file" accept=".jpg,.jpeg,.png,.webp">
                <div class="form-text">Chọn file ảnh (jpg, jpeg, png, webp) để tải lên làm ảnh bìa.</div>
            </div>

            <div class="mb-3">
                <label for="cover_image" class="form-label">Hoặc nhập đường dẫn ảnh bìa (URL)</label>
                <input type="text" class="form-control" id="cover_image" name="cover_image" placeholder="https://example.com/image.jpg">
                <div class="form-text">Hoặc cung cấp đường dẫn trực tiếp đến ảnh bìa từ internet.</div>
            </div>

            <!-- Status and Publish Type will be managed by the Editorial Board -->
            
            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea class="form-control" id="description" name="description" rows="5" placeholder="Nhập tóm tắt hoặc mô tả ngắn cho bộ truyện..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Tạo Series</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
