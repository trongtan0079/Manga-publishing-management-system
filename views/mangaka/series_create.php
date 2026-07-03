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

<div class="card">
    <div class="card-header">
        <h4>Tạo Series Mới</h4>
    </div>
    <div class="card-body">
        <!-- Form thêm mới, action trỏ tới method store -->
        <form action="<?= BASE_PATH ?>/index.php?controller=series&action=store" method="POST">
            
            <div class="mb-3">
                <label for="title" class="form-label">Tên Series <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" required placeholder="Nhập tên bộ truyện">
            </div>

            <div class="mb-3">
                <label for="cover_image" class="form-label">Đường dẫn ảnh bìa (URL)</label>
                <input type="url" class="form-control" id="cover_image" name="cover_image" placeholder="https://example.com/image.jpg">
                <div class="form-text">Cung cấp đường dẫn trực tiếp đến ảnh bìa.</div>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                <select class="form-select" id="status" name="status" required>
                    <option value="planning" selected>Kế hoạch (Planning)</option>
                    <option value="ongoing">Đang xuất bản (Ongoing)</option>
                    <option value="completed">Hoàn thành (Completed)</option>
                    <option value="suspended">Tạm ngưng (Suspended)</option>
                    <option value="canceled">Đã hủy (Canceled)</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="publish_type" class="form-label">Lịch xuất bản <span class="text-danger">*</span></label>
                <select class="form-select" id="publish_type" name="publish_type" required>
                    <option value="weekly" selected>Hàng tuần (Weekly)</option>
                    <option value="monthly">Hàng tháng (Monthly)</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea class="form-control" id="description" name="description" rows="5" placeholder="Nhập tóm tắt hoặc mô tả ngắn cho bộ truyện..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Tạo Series</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
