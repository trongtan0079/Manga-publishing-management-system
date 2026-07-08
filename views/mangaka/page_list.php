<?php 
if (!defined('BASE_PATH')) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $pos = strpos($scriptName, '/views/');
    $projectUrl = ($pos !== false) ? substr($scriptName, 0, $pos) : '';
    header('Location: ' . $projectUrl . '/index.php');
    exit;
}
/**
 * View: Giao diện quản lý danh sách trang truyện (page_list.php)
 * Vai trò: Mangaka (Họa sĩ chính)
 * Chức năng: Hiển thị danh sách tất cả các trang vẽ (Page) thuộc các bộ truyện do Mangaka sáng tác.
 * 
 * @var array $pages Danh sách các trang vẽ
 */
$pageTitle = 'Danh sách Trang vẽ';
$current_page = 'pages';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
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

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1 text-dark fw-bold">Tất cả Trang truyện (Pages)</h2>
        <p class="text-muted text-xs mb-0">Quản lý và theo dõi danh sách toàn bộ trang vẽ phác thảo/hoàn thiện.</p>
    </div>
    <a href="<?= BASE_PATH ?>/index.php?controller=dashboard&action=mangaka" class="btn btn-outline-secondary shadow-sm">
        <i class="fas fa-arrow-left me-2"></i>Quay lại Dashboard
    </a>
</div>

<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 80px;">Ảnh</th>
                        <th>Số trang</th>
                        <th>Chương & Bộ truyện</th>
                        <th>Trạng thái</th>
                        <th>Cập nhật lần cuối</th>
                        <th class="text-end pe-4">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pages)): ?>
                        <?php foreach ($pages as $page): 
                            $imageUrl = $page['image_url'];
                            $resolvedImage = (strpos($imageUrl, 'http') === 0) ? $imageUrl : BASE_PATH . '/' . ltrim($imageUrl, '/');
                        ?>
                            <tr>
                                <td class="ps-4">
                                    <a href="<?= BASE_PATH ?>/index.php?controller=page&action=show&id=<?= $page['page_id'] ?>">
                                        <img src="<?= htmlspecialchars($resolvedImage) ?>" alt="Page <?= $page['page_number'] ?>" class="rounded border shadow-xs object-fit-cover" style="width: 50px; height: 70px;">
                                    </a>
                                </td>
                                <td class="fw-bold text-slate-800">Trang <?= htmlspecialchars($page['page_number']) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($page['series_title'] ?? '') ?></strong><br>
                                    <small class="text-muted">Chương <?= htmlspecialchars($page['chapter_number']) ?>: <?= htmlspecialchars($page['chapter_title'] ?? '') ?></small>
                                </td>
                                <td>
                                     <?= $this->getPageStatusBadge($page['status'], $page['chapter_status']) ?>
                                </td>
                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($page['updated_at']))) ?></td>
                                <td class="text-end pe-4">
                                    <a href="<?= BASE_PATH ?>/index.php?controller=page&action=show&id=<?= $page['page_id'] ?>" class="btn btn-sm btn-info text-white">
                                        <i class="fas fa-eye me-1"></i> Chi tiết & Giao việc
                                    </a>
                                    <?php if (!in_array($page['status'], ['approved', 'published'])): ?>
                                        <a href="<?= BASE_PATH ?>/index.php?controller=page&action=edit&id=<?= $page['page_id'] ?>" class="btn btn-sm btn-warning text-dark ms-1">
                                            <i class="fas fa-edit me-1"></i> Sửa
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-images fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">Chưa có trang vẽ nào được tải lên.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
