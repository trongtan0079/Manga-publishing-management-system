<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="mb-3">
    <a href="/index.php?controller=series&action=show&id=<?= htmlspecialchars($series['series_id']) ?>" class="btn btn-secondary">&larr; Back to Series</a>
</div>

<div class="card border-primary mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Create New Chapter</h5>
    </div>
    <div class="card-body">
        <form action="/index.php?controller=chapter&action=store" method="POST">
            <input type="hidden" name="series_id" value="<?= htmlspecialchars($series['series_id']) ?>">
            
            <div class="mb-3">
                <label for="chapter_number" class="form-label">Chapter Number <span class="text-danger">*</span></label>
                <input type="number" step="any" min="0" class="form-control" id="chapter_number" name="chapter_number" required>
            </div>
            
            <div class="mb-3">
                <label for="title" class="form-label">Chapter Title</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="Optional">
            </div>
            
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                    <option value="scheduled">Scheduled</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Create Chapter</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
