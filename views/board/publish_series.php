<?php
/**
 * View: Giao diện duyệt và xuất bản các bộ truyện (publish_series.php)
 * Vai trò: Board (Hội đồng/Ban giám đốc)
 * Chức năng: Cho phép Hội đồng xem xét và duyệt các bộ truyện (Series) để đưa vào xuất bản hoặc đổi trạng thái hoạt động.
 * 
 * @var array $seriesList Danh sách các bộ truyện cần duyệt hoặc thay đổi trạng thái
 */
$pageTitle = 'Duyệt Series (Publish Series)';
$current_page = 'publish_series';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1 text-dark fw-bold">Duyệt & Xuất bản Series</h2>
        <p class="text-muted text-xs mb-0">Hội đồng xem xét và duyệt các Series chờ phát hành hoặc thay đổi trạng thái xuất bản.</p>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Tên Truyện</th>
                        <th>Tác giả</th>
                        <th>Trạng thái hiện tại</th>
                        <th>Lịch xuất bản</th>
                        <th>Ngày tạo</th>
                        <th class="text-end pe-4">Cập nhật Trạng thái & Lịch phát hành</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($seriesList)): ?>
                        <?php foreach ($seriesList as $series): ?>
                            <tr>
                                <td class="ps-4"><?= htmlspecialchars($series['series_id']) ?></td>
                                <td>
                                    <?php if (!empty($series['cover_image'])): 
                                        $coverUrl = $series['cover_image'];
                                        $resolvedCover = (strpos($coverUrl, 'http') === 0) ? $coverUrl : BASE_PATH . '/' . ltrim($coverUrl, '/');
                                    ?>
                                        <img src="<?= htmlspecialchars($resolvedCover) ?>" alt="Cover" width="40" height="60" class="me-2 object-fit-cover rounded">
                                    <?php endif; ?>
                                    <strong><?= htmlspecialchars($series['title']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($series['mangaka_name'] ?? 'Không rõ') ?></td>
                                <td>
                                    <?php
                                    $badgeClass = 'bg-secondary';
                                    $statusLabel = $series['status'];
                                    switch ($series['status']) {
                                        case 'planning': $badgeClass = 'bg-info text-dark'; $statusLabel = 'Kế hoạch (Chờ duyệt)'; break;
                                        case 'ongoing': $badgeClass = 'bg-primary'; $statusLabel = 'Đang xuất bản'; break;
                                        case 'completed': $badgeClass = 'bg-success'; $statusLabel = 'Hoàn thành'; break;
                                        case 'canceled': $badgeClass = 'bg-danger'; $statusLabel = 'Đã hủy'; break;
                                        case 'suspended': $badgeClass = 'bg-warning text-dark'; $statusLabel = 'Tạm ngưng'; break;
                                    }
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($statusLabel) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= htmlspecialchars(($series['publish_type'] ?? 'weekly') === 'weekly' ? 'Hàng tuần' : 'Hàng tháng') ?></span>
                                </td>
                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($series['created_at']))) ?></td>
                                <td class="text-end pe-4">
                                    <form action="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=series&action=updateStatus&id=<?= $series['series_id'] ?>" method="POST" class="d-flex justify-content-end align-items-center gap-2">
                                        <select name="status" class="form-select form-select-sm w-auto" title="Trạng thái">
                                            <option value="planning" <?= $series['status'] == 'planning' ? 'selected' : '' ?>>Kế hoạch (Chờ duyệt)</option>
                                            <option value="ongoing" <?= $series['status'] == 'ongoing' ? 'selected' : '' ?>>Đang xuất bản (Ongoing)</option>
                                            <option value="completed" <?= $series['status'] == 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                                            <option value="canceled" <?= $series['status'] == 'canceled' ? 'selected' : '' ?>>Đã hủy (Canceled)</option>
                                            <option value="suspended" <?= $series['status'] == 'suspended' ? 'selected' : '' ?>>Tạm ngưng (Suspended)</option>
                                        </select>
                                        <select name="publish_type" class="form-select form-select-sm w-auto" title="Lịch xuất bản">
                                            <option value="weekly" <?= ($series['publish_type'] ?? 'weekly') == 'weekly' ? 'selected' : '' ?>>Hàng tuần</option>
                                            <option value="monthly" <?= ($series['publish_type'] ?? 'weekly') == 'monthly' ? 'selected' : '' ?>>Hàng tháng</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary" title="Cập nhật">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <div class="mb-3"><i class="fas fa-folder-open fa-3x text-light"></i></div>
                                <p class="mb-0">Hiện không có Series nào cần duyệt xuất bản.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
