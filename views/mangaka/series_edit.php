<?php 
/**
 * @var array $series 
 */
include __DIR__ . '/../layouts/header.php'; 
?>

<!-- Nút quay lại -->
<div class="mb-3">
    <a href="/index.php?controller=series&action=index" class="btn btn-secondary">&larr; Back to My Series</a>
</div>

<div class="card">
    <div class="card-header">
        <h4>Edit Series: <?= htmlspecialchars($series['title']) ?></h4>
    </div>
    <div class="card-body">
        <!-- Form cập nhật, action trỏ tới update với series_id tương ứng -->
        <form action="/index.php?controller=series&action=update&id=<?= $series['series_id'] ?>" method="POST">
            
            <div class="mb-3">
                <label for="title" class="form-label">Series Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($series['title']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="cover_image" class="form-label">Cover Image URL</label>
                <input type="url" class="form-control" id="cover_image" name="cover_image" value="<?= htmlspecialchars($series['cover_image'] ?? '') ?>">
                <?php if (!empty($series['cover_image'])): ?>
                    <div class="mt-2">
                        <img src="<?= htmlspecialchars($series['cover_image']) ?>" alt="Cover Preview" class="img-thumbnail" style="max-height: 150px;">
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                <select class="form-select" id="status" name="status" required>
                    <option value="planning" <?= ($series['status'] === 'planning') ? 'selected' : '' ?>>Planning</option>
                    <option value="ongoing" <?= ($series['status'] === 'ongoing') ? 'selected' : '' ?>>Ongoing</option>
                    <option value="completed" <?= ($series['status'] === 'completed') ? 'selected' : '' ?>>Completed</option>
                    <option value="suspended" <?= ($series['status'] === 'suspended') ? 'selected' : '' ?>>Suspended</option>
                    <option value="canceled" <?= ($series['status'] === 'canceled') ? 'selected' : '' ?>>Canceled</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5"><?= htmlspecialchars($series['description'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
