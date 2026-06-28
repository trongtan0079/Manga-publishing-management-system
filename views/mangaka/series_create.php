<?php 
$pageTitle = 'Tạo Series Mới';
$current_page = 'series';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<!-- Nút quay lại trang danh sách -->
<div class="mb-3">
    <a href="<?= BASE_PATH ?>/index.php?controller=series&action=index" class="btn btn-secondary">&larr; Back to My Series</a>
</div>

<div class="card">
    <div class="card-header">
        <h4>Create New Series</h4>
    </div>
    <div class="card-body">
        <!-- Form thêm mới, action trỏ tới method store -->
        <form action="<?= BASE_PATH ?>/index.php?controller=series&action=store" method="POST">
            
            <div class="mb-3">
                <label for="title" class="form-label">Series Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" required placeholder="Enter manga title">
            </div>

            <div class="mb-3">
                <label for="cover_image" class="form-label">Cover Image URL</label>
                <input type="url" class="form-control" id="cover_image" name="cover_image" placeholder="https://example.com/image.jpg">
                <div class="form-text">Provide a direct link to the cover image.</div>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                <select class="form-select" id="status" name="status" required>
                    <option value="planning" selected>Planning</option>
                    <option value="ongoing">Ongoing</option>
                    <option value="completed">Completed</option>
                    <option value="suspended">Suspended</option>
                    <option value="canceled">Canceled</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5" placeholder="Write a short synopsis or description for the manga..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Create Series</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
