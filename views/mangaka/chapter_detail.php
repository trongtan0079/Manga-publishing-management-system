<?php 
/**
 * Khai báo biến để Editor/IDE hiểu và không báo lỗi gạch đỏ
 * @var array $series Thông tin bộ truyện
 * @var array $chapter Thông tin chi tiết chapter
 */
include __DIR__ . '/../layouts/header.php'; 
?>

<div class="mb-3 d-flex justify-content-between align-items-center">
    <a href="/index.php?controller=series&action=show&id=<?= htmlspecialchars($series['series_id']) ?>" class="btn btn-secondary">&larr; Back to Series</a>
    
    <div>
        <a href="/index.php?controller=chapter&action=edit&id=<?= $chapter['chapter_id'] ?>" class="btn btn-warning">Edit Chapter</a>
        <form action="/index.php?controller=chapter&action=delete&id=<?= $chapter['chapter_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa chapter này?');">
            <button type="submit" class="btn btn-danger">Delete</button>
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
        <p><strong>Status:</strong> <span class="badge <?= $cBadge ?>"><?= ucfirst(htmlspecialchars($chapter['status'])) ?></span></p>
        <p><strong>Created At:</strong> <?= htmlspecialchars(date('d/m/Y H:i', strtotime($chapter['created_at']))) ?></p>
        <p><strong>Last Updated:</strong> <?= htmlspecialchars(date('d/m/Y H:i', strtotime($chapter['updated_at']))) ?></p>
    </div>
</div>

<div class="card border-info">
    <div class="card-header bg-info text-dark d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Pages / Images</h5>
        <button class="btn btn-sm btn-light" disabled>+ Add Page (Coming Soon)</button>
    </div>
    <div class="card-body text-center py-5">
        <p class="text-muted mb-0">
            <em>Page Management module will be integrated here in the next phase.</em><br>
            You will be able to upload and manage images for this chapter.
        </p>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
