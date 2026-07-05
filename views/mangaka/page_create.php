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
                <label for="image" class="form-label">File ảnh <span class="text-danger">*</span></label>
                <input class="form-control" type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp" required>
                <div class="form-text">Chỉ chấp nhận các định dạng ảnh: JPG, JPEG, PNG, WEBP. Dung lượng tối đa 10MB.</div>
            </div>

            <!-- Trường chọn trạng thái trang -->
            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="drafting" selected>Bản nháp (Drafting)</option>
                    <option value="drawing">Đang vẽ (Drawing)</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Lưu Trang</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
