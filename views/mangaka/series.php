<?php 
/**
 * @var array $seriesList 
 */
include __DIR__ . '/../layouts/header.php'; 
?>

<!-- Tiêu đề trang và Nút thêm mới -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>My Manga Series</h2>
    <a href="/index.php?controller=series&action=create" class="btn btn-primary">Create New Series</a>
</div>

<!-- Bảng hiển thị danh sách bộ truyện -->
<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
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
                            switch ($series['status']) {
                                case 'planning': $badgeClass = 'bg-info text-dark'; break;
                                case 'ongoing': $badgeClass = 'bg-primary'; break;
                                case 'completed': $badgeClass = 'bg-success'; break;
                                case 'canceled': $badgeClass = 'bg-danger'; break;
                                case 'suspended': $badgeClass = 'bg-warning text-dark'; break;
                            }
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= ucfirst(htmlspecialchars($series['status'])) ?></span>
                        </td>
                        <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($series['created_at']))) ?></td>
                        
                        <!-- Actions -->
                        <td>
                            <div class="btn-group" role="group">
                                <a href="/index.php?controller=series&action=show&id=<?= $series['series_id'] ?>" class="btn btn-sm btn-info text-white" title="View Detail">
                                    View
                                </a>
                                <a href="/index.php?controller=series&action=edit&id=<?= $series['series_id'] ?>" class="btn btn-sm btn-warning" title="Edit Series">
                                    Edit
                                </a>
                                <!-- Form Xóa (dùng POST để bảo mật) -->
                                <form action="/index.php?controller=series&action=delete&id=<?= $series['series_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this series? This action cannot be undone.');">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete Series">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        You haven't created any series yet. Click "Create New Series" to start!
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
