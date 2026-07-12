<?php 
if (!defined('BASE_PATH')) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $pos = strpos($scriptName, '/views/');
    $projectUrl = ($pos !== false) ? substr($scriptName, 0, $pos) : '';
    header('Location: ' . $projectUrl . '/index.php');
    exit;
}
/**
 * View: Giao diện quản lý danh sách chương truyện (chapter_list.php)
 * Vai trò: Mangaka (Họa sĩ chính)
 * Chức năng: Hiển thị danh sách tất cả các chương (Chapter) thuộc các bộ truyện do Mangaka sáng tác.
 * 
 * @var array $chapters Danh sách các chương
 */
$pageTitle = 'Danh sách Chapter';
$current_page = 'chapters';
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
        <h2 class="h3 mb-1 text-dark fw-bold">Tất cả Chương truyện (Chapters)</h2>
        <p class="text-muted text-xs mb-0">Quản lý nội dung và trạng thái các chương sáng tác của bạn.</p>
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
                        <th class="ps-4">Chương số</th>
                        <th>Tên Chapter</th>
                        <th>Thuộc bộ truyện</th>
                        <th>Tiến độ</th>
                        <th>Trạng thái</th>
                        <th>Cập nhật lần cuối</th>
                        <th class="text-end pe-4">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($chapters)): ?>
                        <?php foreach ($chapters as $chapter): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-primary">#<?= htmlspecialchars($chapter['chapter_number']) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($chapter['title'] ?? 'Chưa đặt tên') ?></strong>
                                    <?php if (!empty($chapter['is_final'])): ?>
                                        <span class="badge bg-danger text-white text-xs ms-1"><i class="fas fa-flag me-1"></i>Chương cuối</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><i class="fas fa-book me-1 text-muted"></i><?= htmlspecialchars($chapter['series_title'] ?? '') ?></span>
                                </td>
                                <td>
                                    <?php if ($chapter['total_tasks'] > 0): 
                                        $percent = round(($chapter['completed_tasks'] / $chapter['total_tasks']) * 100);
                                        $barClass = $percent === 100 ? 'bg-success' : 'bg-primary';
                                    ?>
                                        <div style="min-width: 120px; max-width: 160px;">
                                            <div class="progress" style="height: 6px; background-color: #e9ecef; border-radius: 3px; margin-bottom: 2px;">
                                                <div class="progress-bar <?= $barClass ?>" role="progressbar" style="width: <?= $percent ?>%; border-radius: 3px;" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <small class="text-muted" style="font-size: 0.72rem; font-weight: 500;"><?= $chapter['completed_tasks'] ?>/<?= $chapter['total_tasks'] ?> việc (<?= $percent ?>%)</small>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-light text-secondary border text-xs" style="font-weight: 500;">Tác giả tự vẽ</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $this->getStatusBadge($chapter['status']) ?>
                                </td>
                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($chapter['updated_at']))) ?></td>
                                <td class="text-end pe-4">
                                    <a href="<?= BASE_PATH ?>/index.php?controller=chapter&action=show&id=<?= $chapter['chapter_id'] ?>" class="btn btn-sm btn-info text-white">
                                        <i class="fas fa-eye me-1"></i> Xem chi tiết
                                    </a>
                                    <a href="<?= BASE_PATH ?>/index.php?controller=chapter&action=edit&id=<?= $chapter['chapter_id'] ?>" class="btn btn-sm btn-warning text-dark ms-1">
                                        <i class="fas fa-edit me-1"></i> Sửa
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-file-alt fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">Bạn chưa tạo chương truyện nào.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
