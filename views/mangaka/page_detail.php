<?php 
/**
 * View: Chi tiết một trang truyện
 * Khai báo biến để Editor/IDE hiểu và không báo lỗi
 * @var array $page Thông tin trang hiện tại
 * @var array $chapter Thông tin chapter chứa trang này
 * @var array $series Thông tin bộ truyện
 */
include __DIR__ . '/../layouts/header.php'; 
?>

<!-- Khối thanh điều hướng và nút hành động -->
<div class="mb-3 d-flex justify-content-between align-items-center">
    <!-- Nút quay lại danh sách trang của chapter -->
    <a href="/index.php?controller=chapter&action=show&id=<?= htmlspecialchars($chapter['chapter_id']) ?>" class="btn btn-secondary">&larr; Quay lại Chapter</a>
    
    <div>
        <!-- Nút sửa trang hiện tại -->
        <a href="/index.php?controller=page&action=edit&id=<?= $page['page_id'] ?>" class="btn btn-warning">Sửa trang</a>
        <!-- Form xóa trang, dùng onsubmit để hỏi lại trước khi xóa -->
        <form action="/index.php?controller=page&action=delete&id=<?= $page['page_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa trang này?');">
            <button type="submit" class="btn btn-danger">Xóa</button>
        </form>
    </div>
</div>

<!-- Khối thông tin chung của trang -->
<div class="card mb-4">
    <div class="card-header">
        <h4 class="mb-0">
            Chi tiết Trang <?= htmlspecialchars($page['page_number']) ?>
        </h4>
        <small class="text-muted">Chapter <?= htmlspecialchars($chapter['chapter_number']) ?> - <?= htmlspecialchars($series['title']) ?></small>
    </div>
    <div class="card-body">
        <?php
        // Gán màu huy hiệu (badge) tùy theo trạng thái (status)
        $pBadge = 'bg-secondary';
        switch ($page['status']) {
            case 'drafting': $pBadge = 'bg-secondary'; break;
            case 'drawing': $pBadge = 'bg-primary'; break;
            case 'reviewing': $pBadge = 'bg-warning text-dark'; break;
            case 'approved': $pBadge = 'bg-info text-dark'; break;
            case 'published': $pBadge = 'bg-success'; break;
        }
        ?>
        <div class="row">
            <div class="col-md-4">
                <p><strong>Trạng thái:</strong> <span class="badge <?= $pBadge ?>"><?= ucfirst(htmlspecialchars($page['status'])) ?></span></p>
                <p><strong>Ngày tạo:</strong> <?= htmlspecialchars(date('d/m/Y H:i', strtotime($page['created_at']))) ?></p>
                <p><strong>Cập nhật lần cuối:</strong> <?= htmlspecialchars(date('d/m/Y H:i', strtotime($page['updated_at']))) ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Khối hiển thị hình ảnh chi tiết của trang truyện -->
<div class="card border-info">
    <div class="card-header bg-info text-dark">
        <h5 class="mb-0">Hình ảnh</h5>
    </div>
    <div class="card-body text-center bg-light">
        <!-- Kiểm tra xem có đường dẫn ảnh không -->
        <?php if (!empty($page['image_url'])): ?>
            <img src="<?= htmlspecialchars($page['image_url']) ?>" alt="Page <?= htmlspecialchars($page['page_number']) ?>" class="img-fluid border shadow-sm" style="max-width: 100%;">
        <?php else: ?>
            <p class="text-muted my-5">Trang này chưa có hình ảnh.</p>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
