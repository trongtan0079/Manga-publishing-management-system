<?php
if (!defined('BASE_PATH')) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $pos = strpos($scriptName, '/views/');
    $projectUrl = ($pos !== false) ? substr($scriptName, 0, $pos) : '';
    header('Location: ' . $projectUrl . '/index.php');
    exit;
}
/**
 * View: Danh sách hồ sơ & bảo vệ Series (dossiers.php)
 * Vai trò: Editor (Biên tập viên)
 * Chức năng: Liệt kê các bộ truyện được phân công gán chuyên trách, cho phép truy cập hồ sơ chi tiết để theo dõi số liệu và viết biện hộ.
 */
$pageTitle = 'Hồ sơ & Số liệu bảo vệ Series';
$current_page = 'dossiers';
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
        <h2 class="h3 mb-1">Hồ sơ & Số liệu bảo vệ Series</h2>
        <p class="text-muted text-xs mb-0">Quản lý hồ sơ, theo dõi số liệu xếp hạng độc giả và viết biện hộ để bảo vệ tác phẩm trước Hội đồng Biên tập.</p>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white py-3 border-bottom border-light">
        <h5 class="card-title mb-0"><i class="fas fa-folder-open text-primary me-2"></i>Dự án Truyện tranh đang phụ trách</h5>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($seriesList)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Ảnh bìa</th>
                            <th>Tên bộ truyện</th>
                            <th>Tác giả</th>
                            <th>Lịch phát hành</th>
                            <th>Trạng thái</th>
                            <th>Xếp hạng gần nhất</th>
                            <th>Hồ sơ bảo vệ</th>
                            <th class="text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($seriesList as $s): ?>
                            <tr>
                                <td class="ps-4">
                                    <?php if (!empty($s['cover_image'])): 
                                        $coverUrl = (strpos($s['cover_image'], 'http') === 0) ? $s['cover_image'] : BASE_PATH . '/' . ltrim($s['cover_image'], '/');
                                    ?>
                                        <img src="<?= htmlspecialchars($coverUrl) ?>" alt="Cover" class="rounded border shadow-xs" style="width: 50px; height: 65px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="rounded bg-light border d-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 65px; font-size: 10px;">No Cover</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong class="text-dark d-block"><?= htmlspecialchars($s['title']) ?></strong>
                                    <small class="text-muted">Đã ra: <?= $s['finished_chapters'] ?> / <?= $s['total_chapters'] ?> chương</small>
                                </td>
                                <td><?= htmlspecialchars($s['mangaka_name']) ?></td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= $s['publish_type'] === 'weekly' ? 'Hàng tuần' : 'Hàng tháng' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                        $statusClass = 'secondary';
                                        $statusText = $s['status'];
                                        if ($s['status'] === 'ongoing') { $statusClass = 'success'; $statusText = 'Đang phát hành'; }
                                        elseif ($s['status'] === 'planning') { $statusClass = 'warning text-dark'; $statusText = 'Bản nháp'; }
                                        elseif ($s['status'] === 'completed') { $statusClass = 'info text-dark'; $statusText = 'Hoàn thành'; }
                                        elseif ($s['status'] === 'suspended') { $statusClass = 'dark'; $statusText = 'Tạm ngưng'; }
                                        elseif ($s['status'] === 'canceled') { $statusClass = 'danger'; $statusText = 'Đã hủy'; }
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($s['latest_ranking'])): ?>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-warning text-dark me-2">#<?= $s['latest_ranking']['rank_position'] ?></span>
                                            <span class="text-xs text-muted">(<?= $s['latest_ranking']['score'] ?>đ)</span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">Chưa xếp hạng</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($s['dossier_notes'])): ?>
                                        <span class="text-success small"><i class="fas fa-check-circle me-1"></i>Đã có biện hộ</span>
                                    <?php else: ?>
                                        <span class="text-warning small"><i class="fas fa-exclamation-circle me-1"></i>Trống (Chưa bảo vệ)</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?= BASE_PATH ?>/index.php?controller=series&action=dossierDetail&id=<?= $s['series_id'] ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-folder-open me-1"></i>Mở hồ sơ
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5 px-3">
                <i class="fas fa-folder-open fa-3x text-muted mb-3" style="opacity: 0.35;"></i>
                <h5 class="fw-bold text-dark">Chưa có dự án truyện tranh nào</h5>
                <p class="text-muted small">Bạn chưa được gán chuyên trách bộ truyện nào đang trong giai đoạn phát hành.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
