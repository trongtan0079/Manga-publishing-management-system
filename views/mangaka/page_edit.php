<?php 
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
            
            <!-- Trường số thứ tự trang -->
            <div class="mb-3">
                <label for="page_number" class="form-label">Số trang (Page Number) <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="page_number" name="page_number" value="<?= htmlspecialchars($page['page_number']) ?>" min="1" required>
            </div>
            
            <!-- Khối hiển thị ảnh hiện tại và tùy chọn thay thế -->
            <div class="mb-3">
                <label class="form-label">Ảnh hiện tại</label>
                <div>
                    <!-- Nếu trang đã có ảnh thì hiển thị Thumbnail -->
                    <?php if (!empty($page['image_url'])): ?>
                        <img src="<?= htmlspecialchars($page['image_url']) ?>" alt="Current Page Image" class="img-thumbnail mb-2" style="max-height: 200px;">
                    <?php else: ?>
                        <p class="text-muted">Chưa có ảnh</p>
                    <?php endif; ?>
                </div>
                
                <!-- Input để tải lên ảnh mới -->
                <label for="image" class="form-label mt-2">Thay đổi file ảnh (Không bắt buộc)</label>
                <input class="form-control" type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">
                <div class="form-text">Để trống nếu không muốn thay đổi ảnh. Chỉ chấp nhận JPG, JPEG, PNG, WEBP. Tối đa 2MB. Việc thay đổi sẽ ghi đè lên đường dẫn ảnh cũ trong CSDL.</div>
            </div>

            <!-- Trường chọn trạng thái -->
            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="drafting" <?= $page['status'] === 'drafting' ? 'selected' : '' ?>>Bản nháp (Drafting)</option>
                    <option value="drawing" <?= $page['status'] === 'drawing' ? 'selected' : '' ?>>Đang vẽ (Drawing)</option>
                    <option value="reviewing" <?= $page['status'] === 'reviewing' ? 'selected' : '' ?>>Đang chờ duyệt (Reviewing)</option>
                    <option value="approved" <?= $page['status'] === 'approved' ? 'selected' : '' ?>>Đã duyệt (Approved)</option>
                    <option value="published" <?= $page['status'] === 'published' ? 'selected' : '' ?>>Đã xuất bản (Published)</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-warning">Cập nhật Trang</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
