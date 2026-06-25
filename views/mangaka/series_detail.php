<?php 
/**
 * @var array $series 
 */
$pageTitle = 'Chi tiết Truyện: ' . htmlspecialchars($series['title']);
$current_page = 'series';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<!-- Thanh điều hướng cơ bản -->
<div class="mb-4 d-flex justify-content-between align-items-center">
    <a href="/index.php?controller=series&action=index" class="btn btn-outline-secondary shadow-sm"><i class="fas fa-arrow-left me-2"></i>Quay lại</a>
    
    <div>
        <a href="/index.php?controller=series&action=edit&id=<?= $series['series_id'] ?>" class="btn btn-warning shadow-sm text-dark">
            <i class="fas fa-edit me-2"></i>Sửa Truyện
        </a>
    </div>
</div>

<div class="row">
    <!-- Cột trái: Ảnh bìa và Thông tin cơ bản -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <?php if (!empty($series['cover_image'])): ?>
                <img src="<?= htmlspecialchars($series['cover_image']) ?>" class="card-img-top object-fit-cover" alt="Cover Image" style="max-height: 400px;">
            <?php else: ?>
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center text-muted" style="height: 300px;">
                    No Cover Image
                </div>
            <?php endif; ?>
            
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($series['title']) ?></h5>
                
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
                <p class="card-text">
                    <strong>Status:</strong> 
                    <span class="badge <?= $badgeClass ?>"><?= ucfirst(htmlspecialchars($series['status'])) ?></span>
                </p>
                <p class="card-text">
                    <strong>Created At:</strong> <br>
                    <?= htmlspecialchars(date('d/m/Y H:i', strtotime($series['created_at']))) ?>
                </p>
                <p class="card-text">
                    <strong>Last Updated:</strong> <br>
                    <?= htmlspecialchars(date('d/m/Y H:i', strtotime($series['updated_at']))) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Cột phải: Mô tả và Danh sách Chapters (sau này) -->
    <div class="col-md-8 mb-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Description / Synopsis</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($series['description'])): ?>
                    <p class="card-text" style="white-space: pre-wrap;"><?= htmlspecialchars($series['description'] ?? '') ?></p>
                <?php else: ?>
                    <p class="text-muted fst-italic">No description provided.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Chapter Management -->
        <div class="card border-primary">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Chapters</h5>
                <a href="/index.php?controller=chapter&action=create&series_id=<?= $series['series_id'] ?>" class="btn btn-sm btn-light">+ Add Chapter</a>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($chapters)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Updated At</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($chapters as $chapter): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($chapter['chapter_number']) ?></td>
                                        <td><?= htmlspecialchars($chapter['title'] ?? '') ?></td>
                                        <td>
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
                                            <span class="badge <?= $cBadge ?>"><?= ucfirst(htmlspecialchars($chapter['status'])) ?></span>
                                        </td>
                                        <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($chapter['updated_at']))) ?></td>
                                        <td class="text-end">
                                            <a href="/index.php?controller=chapter&action=show&id=<?= $chapter['chapter_id'] ?>" class="btn btn-sm btn-info text-white">View</a>
                                            <a href="/index.php?controller=chapter&action=edit&id=<?= $chapter['chapter_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <form action="/index.php?controller=chapter&action=delete&id=<?= $chapter['chapter_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa chapter này?');">
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <p class="text-muted mb-0">Chưa có chapter nào. Hãy tạo chapter đầu tiên!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
