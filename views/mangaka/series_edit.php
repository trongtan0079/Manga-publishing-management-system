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
                <label for="cover_file" class="form-label">Tải ảnh bìa mới (từ thiết bị)</label>
                <input type="file" class="form-control" id="cover_file" name="cover_file" accept=".jpg,.jpeg,.png,.webp">
                <div class="form-text">Chọn file ảnh mới nếu muốn thay đổi ảnh bìa hiện tại.</div>
            </div>

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
                <label class="form-label d-block fw-bold">Trạng thái hiện tại</label>
                <?php
                $statusClass = 'bg-secondary';
                if ($series['status'] === 'ongoing') $statusClass = 'bg-success';
                elseif ($series['status'] === 'planning') $statusClass = 'bg-warning text-dark';
                elseif ($series['status'] === 'completed') $statusClass = 'bg-info text-dark';
                elseif ($series['status'] === 'suspended') $statusClass = 'bg-dark';
                elseif ($series['status'] === 'canceled') $statusClass = 'bg-danger';
                ?>
                <span class="badge <?= $statusClass ?> px-3 py-2 fs-6"><?= ucfirst(htmlspecialchars($series['status'])) ?></span>
                <div class="form-text">Trạng thái này được phê duyệt và quản lý bởi Hội đồng Biên tập (Editorial Board).</div>
            </div>

            <div class="mb-3">
                <label class="form-label d-block fw-bold">Lịch xuất bản</label>
                <span class="badge bg-secondary px-3 py-2 fs-6"><?= ucfirst(htmlspecialchars($series['publish_type'] ?? 'Chưa quyết định')) ?></span>
                <div class="form-text">Lịch xuất bản do Hội đồng Biên tập quyết định khi phê duyệt tác phẩm.</div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea class="form-control" id="description" name="description" rows="5"><?= htmlspecialchars($series['description'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
