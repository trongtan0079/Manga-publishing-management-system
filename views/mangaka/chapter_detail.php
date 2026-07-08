<?php 
if (!defined('BASE_PATH')) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $pos = strpos($scriptName, '/views/');
    $projectUrl = ($pos !== false) ? substr($scriptName, 0, $pos) : '';
    header('Location: ' . $projectUrl . '/index.php');
    exit;
}
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
$isLocked = $this->isChapterLocked($chapter);
?>

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

<div class="mb-4 d-flex justify-content-between align-items-center">
    <a href="<?= BASE_PATH ?>/index.php?controller=series&action=show&id=<?= htmlspecialchars($series['series_id']) ?>" class="btn btn-outline-secondary shadow-sm"><i class="fas fa-arrow-left me-2"></i>Quay lại Truyện</a>
    
    <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka'): ?>
    <div>
        <?php if (!$isLocked && !$this->isSeriesLocked($series)): ?>
            <?php if ($chapter['status'] === 'drafting'): ?>
                <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=create&chapter_id=<?= $chapter['chapter_id'] ?>&type=draft" class="btn btn-success shadow-sm me-1"><i class="fas fa-paper-plane me-1"></i>Nộp duyệt Bản nháp</a>
            <?php elseif ($chapter['status'] === 'drawing'): ?>
                <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=create&chapter_id=<?= $chapter['chapter_id'] ?>&type=final" class="btn btn-primary shadow-sm me-1"><i class="fas fa-check-double me-1"></i>Nộp duyệt Bản hoàn chỉnh</a>
            <?php endif; ?>
        <a href="<?= BASE_PATH ?>/index.php?controller=chapter&action=edit&id=<?= $chapter['chapter_id'] ?>" class="btn btn-warning shadow-sm text-dark"><i class="fas fa-edit me-2"></i>Sửa Chapter</a>
        <form action="<?= BASE_PATH ?>/index.php?controller=chapter&action=delete&id=<?= $chapter['chapter_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa chapter này?');">
            <button type="submit" class="btn btn-danger shadow-sm"><i class="fas fa-trash-alt me-2"></i>Xóa</button>
        </form>
        <?php else: ?>
            <?php 
                $lockMsg = 'Chương đã được duyệt / phát hành (Khóa)';
                if ($this->isSeriesLocked($series)) {
                    if ($series['status'] === 'suspended') $lockMsg = 'Bộ truyện đang tạm ngưng (Khóa)';
                    elseif ($series['status'] === 'canceled') $lockMsg = 'Bộ truyện đã hủy (Khóa)';
                    elseif ($series['status'] === 'completed') $lockMsg = 'Bộ truyện đã hoàn thành (Khóa)';
                } elseif ($chapter['status'] === 'reviewing_draft') {
                    $lockMsg = 'Chờ duyệt Kịch bản (Khóa)';
                } elseif ($chapter['status'] === 'reviewing_final' || $chapter['status'] === 'reviewing') {
                    $lockMsg = 'Chờ duyệt Bản vẽ (Khóa)';
                }
            ?>
            <span class="badge bg-warning text-dark p-2 border border-warning"><i class="fas fa-lock me-1"></i><?= $lockMsg ?></span>
        <?php endif; ?>
    </div>
    <?php endif; ?>
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
        <p><strong>Trạng thái:</strong> <?= $this->getStatusBadge($chapter['status']) ?></p>
        <p><strong>Hạn chót (Deadline):</strong> <?= !empty($chapter['due_date']) ? htmlspecialchars(date('d/m/Y H:i', strtotime($chapter['due_date']))) : '<span class="text-muted">Chưa thiết lập</span>' ?></p>
        <p><strong>Ngày tạo:</strong> <?= htmlspecialchars(date('d/m/Y H:i', strtotime($chapter['created_at']))) ?></p>
        <p><strong>Cập nhật lần cuối:</strong> <?= htmlspecialchars(date('d/m/Y H:i', strtotime($chapter['updated_at']))) ?></p>
    </div>
</div>

<div class="card border-info">
    <div class="card-header bg-info text-dark d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Trang / Hình ảnh</h5>
        <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka' && !$isLocked && !$this->isSeriesLocked($series)): ?>
        <a href="<?= BASE_PATH ?>/index.php?controller=page&action=create&chapter_id=<?= $chapter['chapter_id'] ?>" class="btn btn-sm btn-light">+ Thêm trang</a>
        <?php endif; ?>
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
                        <?php foreach ($pages as $page): 
                            $isUpdatedAfterAnnotation = $this->isPageUpdatedAfterAnnotation($page);
                        ?>
                            
                            <tr>
                                <td class="text-center fs-5 fw-bold"><?= htmlspecialchars($page['page_number']) ?></td>
                                <td>
                                    <div class="position-relative d-inline-block">
                                        <?php if (!empty($page['image_url'])): 
                                            $resolvedImage = $this->resolvePageImageUrl($page['image_url']);
                                        ?>
                                            <img src="<?= htmlspecialchars($resolvedImage) ?>" alt="Trang <?= htmlspecialchars($page['page_number']) ?>" class="img-thumbnail <?= !empty($page['annotation_count']) ? 'border-danger border-2' : '' ?>" style="max-height: 100px;">
                                        <?php else: ?>
                                            <div class="bg-light border text-muted d-flex align-items-center justify-content-center <?= !empty($page['annotation_count']) ? 'border-danger border-2' : '' ?>" style="height: 100px; width: 70px; font-size: 0.8rem;">Trống</div>
                                        <?php endif; ?>
                                          
                                        <?php if (!empty($page['annotation_count'])): ?>
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm border border-white" title="Có <?= $page['annotation_count'] ?> lỗi cần sửa do Editor đánh dấu">
                                                <i class="fas fa-exclamation"></i> <?= $page['annotation_count'] ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?= $this->getStatusBadge($page['status']) ?>
                                    <?php if ($isUpdatedAfterAnnotation): ?>
                                        <span class="badge bg-warning text-dark ms-1" style="font-size: 0.75rem;" title="Đã tải lên ảnh mới sau khi Editor báo lỗi. Đang chờ duyệt.">
                                            <i class="fas fa-sync-alt me-1"></i>Bản mới
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($page['updated_at']))) ?></td>
                                <td>
                                    <a href="<?= BASE_PATH ?>/index.php?controller=page&action=show&id=<?= $page['page_id'] ?>" class="btn btn-sm btn-info text-white">Xem</a>
                                    <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka' && !$isLocked && $page['status'] !== 'approved' && $page['status'] !== 'published' && !$this->isSeriesLocked($series)): ?>
                                    <a href="<?= BASE_PATH ?>/index.php?controller=page&action=edit&id=<?= $page['page_id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                                    <form action="<?= BASE_PATH ?>/index.php?controller=page&action=delete&id=<?= $page['page_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa trang này?');">
                                        <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                    </form>
                                    <?php endif; ?>
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
