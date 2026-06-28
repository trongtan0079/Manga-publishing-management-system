<?php 
/**
 * @var array $seriesList 
 */
$pageTitle = 'Dự án Truyện';
$current_page = 'series';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<!-- Tiêu đề trang và Nút thêm mới -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-dark fw-bold">Dự án Truyện</h2>
    <a href="<?= BASE_PATH ?>/index.php?controller=series&action=create" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-2"></i>Tạo Truyện Mới</a>
</div>

<!-- Bảng hiển thị danh sách bộ truyện -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Tên Truyện</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-end pe-4">Hành động</th>
                    </tr>
                </thead>
        <tbody>
            <?php if (!empty($seriesList)): ?>
                <?php foreach ($seriesList as $series): ?>
                    <tr>
                        <td><?= htmlspecialchars($series['series_id']) ?></td>
                        <td>
                            <?php if (!empty($series['cover_image'])): ?>
                                <img src="<?= htmlspecialchars($series['cover_image']) ?>" alt="Cover" width="40" height="60" class="me-2 object-fit-cover rounded">
                            <?php endif; ?>
                            <strong><?= htmlspecialchars($series['title']) ?></strong>
                        </td>
                        <td>
                            <?php
                            $badgeClass = 'bg-secondary';
                            $statusLabel = $series['status'];
                            switch ($series['status']) {
                                case 'planning': $badgeClass = 'bg-info text-dark'; $statusLabel = 'Kế hoạch'; break;
                                case 'ongoing': $badgeClass = 'bg-primary'; $statusLabel = 'Đang xuất bản'; break;
                                case 'completed': $badgeClass = 'bg-success'; $statusLabel = 'Hoàn thành'; break;
                                case 'canceled': $badgeClass = 'bg-danger'; $statusLabel = 'Đã hủy'; break;
                                case 'suspended': $badgeClass = 'bg-warning text-dark'; $statusLabel = 'Tạm ngưng'; break;
                            }
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($statusLabel) ?></span>
                        </td>
                        <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($series['created_at']))) ?></td>
                        
                        <!-- Actions -->
                        <td class="text-end pe-4">
                            <div class="btn-group" role="group">
                                <a href="<?= BASE_PATH ?>/index.php?controller=series&action=show&id=<?= $series['series_id'] ?>" class="btn btn-sm btn-info text-white" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i> Xem
                                </a>
                                <a href="<?= BASE_PATH ?>/index.php?controller=series&action=edit&id=<?= $series['series_id'] ?>" class="btn btn-sm btn-warning text-dark" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <!-- Form Xóa (dùng POST để bảo mật) -->
                                <form action="<?= BASE_PATH ?>/index.php?controller=series&action=delete&id=<?= $series['series_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bộ truyện này không? Hành động này không thể hoàn tác.');">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa Truyện">
                                        <i class="fas fa-trash-alt"></i> Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                        <div class="mb-3"><i class="fas fa-folder-open fa-3x text-light"></i></div>
                        <p class="mb-0">Bạn chưa tạo bộ truyện nào. Nhấn <strong>"Tạo Truyện Mới"</strong> để bắt đầu!</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
