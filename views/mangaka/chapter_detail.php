<?php 
/**
 * Khai báo biến để Editor/IDE hiểu và không báo lỗi gạch đỏ
 * @var array $series Thông tin bộ truyện
 * @var array $chapter Thông tin chi tiết chapter
 */
$pageTitle = 'Chi tiết Chapter ' . htmlspecialchars($chapter['chapter_number']);
$current_page = 'series';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="mb-4 d-flex justify-content-between align-items-center">
    <a href="<?= BASE_PATH ?>/index.php?controller=series&action=show&id=<?= htmlspecialchars($series['series_id']) ?>" class="btn btn-outline-secondary shadow-sm"><i class="fas fa-arrow-left me-2"></i>Quay lại Truyện</a>
    
    <div>
        <a href="<?= BASE_PATH ?>/index.php?controller=chapter&action=edit&id=<?= $chapter['chapter_id'] ?>" class="btn btn-warning shadow-sm text-dark"><i class="fas fa-edit me-2"></i>Sửa Chapter</a>
        <form action="<?= BASE_PATH ?>/index.php?controller=chapter&action=delete&id=<?= $chapter['chapter_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa chapter này?');">
            <button type="submit" class="btn btn-danger shadow-sm"><i class="fas fa-trash-alt me-2"></i>Xóa</button>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h4 class="mb-0">
            Chapter <?= htmlspecialchars($chapter['chapter_number']) ?>
            <?php if (!empty($chapter['title'])): ?>
                : <?= htmlspecialchars($chapter['title']) ?>
            <?php endif; ?>
        </h4>
    </div>
    <div class="card-body">
        <?php
        $cBadge = 'bg-secondary';
        switch ($chapter['status']) {
            case 'drafting': $cBadge = 'bg-secondary'; break;
            case 'drawing': $cBadge = 'bg-primary'; break;
            case 'reviewing': $cBadge = 'bg-warning text-dark'; break;
            case 'approved': $cBadge = 'bg-info text-dark'; break;
            case 'published': $cBadge = 'bg-success'; break;
        }
        ?>
        <p><strong>Trạng thái:</strong> <span class="badge <?= $cBadge ?>"><?= ucfirst(htmlspecialchars($chapter['status'])) ?></span></p>
        <p><strong>Ngày tạo:</strong> <?= htmlspecialchars(date('d/m/Y H:i', strtotime($chapter['created_at']))) ?></p>
        <p><strong>Cập nhật lần cuối:</strong> <?= htmlspecialchars(date('d/m/Y H:i', strtotime($chapter['updated_at']))) ?></p>
    </div>
</div>

<div class="card border-info">
    <div class="card-header bg-info text-dark d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Trang / Hình ảnh</h5>
        <a href="<?= BASE_PATH ?>/index.php?controller=page&action=create&chapter_id=<?= $chapter['chapter_id'] ?>" class="btn btn-sm btn-light">+ Thêm trang</a>
    </div>
    <div class="card-body">
        <?php if (empty($pages)): ?>
            <div class="text-center py-5">
                <p class="text-muted mb-0">
                    <em>Chưa có trang truyện nào được thêm vào.</em><br>
                    Hãy nhấn "+ Thêm trang" để tải lên hình ảnh cho chapter này.
                </p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 100px;">Trang #</th>
                            <th scope="col">Ảnh thu nhỏ</th>
                            <th scope="col">Trạng thái</th>
                            <th scope="col">Cập nhật lần cuối</th>
                            <th scope="col" style="width: 250px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pages as $page): ?>
                            <?php
                            $pBadge = 'bg-secondary';
                            switch ($page['status']) {
                                case 'drafting': $pBadge = 'bg-secondary'; break;
                                case 'drawing': $pBadge = 'bg-primary'; break;
                                case 'reviewing': $pBadge = 'bg-warning text-dark'; break;
                                case 'approved': $pBadge = 'bg-info text-dark'; break;
                                case 'published': $pBadge = 'bg-success'; break;
                            }
                            ?>
                            <tr>
                                <td class="text-center fs-5 fw-bold"><?= htmlspecialchars($page['page_number']) ?></td>
                                <td>
                                    <?php if (!empty($page['image_url'])): ?>
                                        <img src="<?= htmlspecialchars($page['image_url']) ?>" alt="Trang <?= htmlspecialchars($page['page_number']) ?>" class="img-thumbnail" style="max-height: 100px;">
                                    <?php else: ?>
                                        <span class="text-muted">Chưa có ảnh</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge <?= $pBadge ?>"><?= ucfirst(htmlspecialchars($page['status'])) ?></span></td>
                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($page['updated_at']))) ?></td>
                                <td>
                                    <a href="<?= BASE_PATH ?>/index.php?controller=page&action=show&id=<?= $page['page_id'] ?>" class="btn btn-sm btn-info text-white">Xem</a>
                                    <a href="<?= BASE_PATH ?>/index.php?controller=page&action=edit&id=<?= $page['page_id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                                    <form action="<?= BASE_PATH ?>/index.php?controller=page&action=delete&id=<?= $page['page_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa trang này?');">
                                        <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
