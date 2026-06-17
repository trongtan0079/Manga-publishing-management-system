<?php 
/**
 * @var array $series 
 */
include __DIR__ . '/../layouts/header.php'; 
?>

<!-- Thanh điều hướng cơ bản -->
<div class="mb-3 d-flex justify-content-between align-items-center">
    <a href="/index.php?controller=series&action=index" class="btn btn-secondary">&larr; Back to My Series</a>
    
    <div>
        <a href="/index.php?controller=series&action=edit&id=<?= $series['series_id'] ?>" class="btn btn-warning">
            Edit Series
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

        <!-- Khung chờ (Placeholder) cho chức năng Chapter Management -->
        <div class="card border-primary">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Chapters</h5>
                <button class="btn btn-sm btn-light" disabled>+ Add Chapter (Coming Soon)</button>
            </div>
            <div class="card-body text-center py-5">
                <p class="text-muted mb-0">
                    <em>Chapter Management module will be integrated here in the next phase.</em><br>
                    You will be able to add, edit, and arrange chapters for this series.
                </p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
