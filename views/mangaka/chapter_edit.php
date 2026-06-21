<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="mb-3">
    <a href="/index.php?controller=series&action=show&id=<?= htmlspecialchars($series['series_id']) ?>" class="btn btn-secondary">&larr; Back to Series</a>
</div>

<div class="card border-warning mb-4">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">Edit Chapter</h5>
    </div>
    <div class="card-body">
        <form action="/index.php?controller=chapter&action=update&id=<?= $chapter['chapter_id'] ?>" method="POST">
            
            <div class="mb-3">
                <label for="chapter_number" class="form-label">Chapter Number <span class="text-danger">*</span></label>
                <input type="number" step="any" min="0" class="form-control" id="chapter_number" name="chapter_number" value="<?= htmlspecialchars($chapter['chapter_number']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="title" class="form-label">Chapter Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($chapter['title']) ?>" placeholder="Optional">
            </div>
            
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="draft" <?= $chapter['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="published" <?= $chapter['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="scheduled" <?= $chapter['status'] === 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-warning">Save Changes</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
