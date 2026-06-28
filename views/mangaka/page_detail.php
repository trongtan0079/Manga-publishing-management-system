<?php 
/**
 * View: Chi tiết một trang truyện
 * Khai báo biến để Editor/IDE hiểu và không báo lỗi
 * @var array $page Thông tin trang hiện tại
 * @var array $chapter Thông tin chapter chứa trang này
 * @var array $series Thông tin bộ truyện
 */
$pageTitle = 'Chi tiết Trang ' . htmlspecialchars($page['page_number']);
$current_page = 'series';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<!-- Khối thanh điều hướng và nút hành động -->
<div class="mb-3 d-flex justify-content-between align-items-center">
    <!-- Nút quay lại danh sách trang của chapter -->
    <a href="<?= BASE_PATH ?>/index.php?controller=chapter&action=show&id=<?= htmlspecialchars($chapter['chapter_id']) ?>" class="btn btn-secondary">&larr; Quay lại Chapter</a>
    
    <div>
        <!-- Nút sửa trang hiện tại -->
        <a href="<?= BASE_PATH ?>/index.php?controller=page&action=edit&id=<?= $page['page_id'] ?>" class="btn btn-warning">Sửa trang</a>
        <!-- Form xóa trang, dùng onsubmit để hỏi lại trước khi xóa -->
        <form action="<?= BASE_PATH ?>/index.php?controller=page&action=delete&id=<?= $page['page_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa trang này?');">
            <button type="submit" class="btn btn-danger">Xóa</button>
        </form>
    </div>
</div>

<!-- Khối thông tin chung của trang -->
<div class="card mb-4">
    <div class="card-header">
        <h4 class="mb-0">
            Chi tiết Trang <?= htmlspecialchars($page['page_number']) ?>
        </h4>
        <small class="text-muted">Chapter <?= htmlspecialchars($chapter['chapter_number']) ?> - <?= htmlspecialchars($series['title']) ?></small>
    </div>
    <div class="card-body">
        <?php
        // Gán màu huy hiệu (badge) tùy theo trạng thái (status)
        $pBadge = 'bg-secondary';
        switch ($page['status']) {
            case 'drafting': $pBadge = 'bg-secondary'; break;
            case 'drawing': $pBadge = 'bg-primary'; break;
            case 'reviewing': $pBadge = 'bg-warning text-dark'; break;
            case 'approved': $pBadge = 'bg-info text-dark'; break;
            case 'published': $pBadge = 'bg-success'; break;
        }
        ?>
        <div class="row">
            <div class="col-md-4">
                <p><strong>Trạng thái:</strong> <span class="badge <?= $pBadge ?>"><?= ucfirst(htmlspecialchars($page['status'])) ?></span></p>
                <p><strong>Ngày tạo:</strong> <?= htmlspecialchars(date('d/m/Y H:i', strtotime($page['created_at']))) ?></p>
                <p><strong>Cập nhật lần cuối:</strong> <?= htmlspecialchars(date('d/m/Y H:i', strtotime($page['updated_at']))) ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Khối hiển thị hình ảnh chi tiết của trang truyện -->
<div class="card border-info">
    <div class="card-header bg-info text-dark">
        <h5 class="mb-0">Hình ảnh</h5>
    </div>
    <div class="card-body text-center bg-light">
        <!-- Kiểm tra xem có đường dẫn ảnh không -->
        <?php if (!empty($page['image_url'])): ?>
            <img src="<?= htmlspecialchars($page['image_url']) ?>" alt="Page <?= htmlspecialchars($page['page_number']) ?>" class="img-fluid border shadow-sm" style="max-width: 100%;">
        <?php else: ?>
            <p class="text-muted my-5">Trang này chưa có hình ảnh.</p>
        <?php endif; ?>
    </div>
</div>

<!-- 
  Khối Task Management (Quản lý Công việc)
  Được hiển thị ngay dưới nội dung chính của Trang truyện.
  Giúp Mangaka theo dõi và quản lý các công việc đang giao cho Assistant trên trang này.
-->
<div class="card border-primary mt-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Task Management</h5>
        <!-- Nút tạo Task mới, truyền sẵn page_id qua URL GET parameter -->
        <a href="<?= BASE_PATH ?>/index.php?controller=task&action=create&page_id=<?= $page['page_id'] ?>" class="btn btn-sm btn-light">Create Task</a>
    </div>
    <div class="card-body">
        <?php if (!empty($tasks)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Title (Công việc)</th>
                            <th>Assistant (Người phụ trách)</th>
                            <th>Priority (Độ ưu tiên)</th>
                            <th>Status (Trạng thái)</th>
                            <th>Due Date (Hạn chót)</th>
                            <th>Actions (Thao tác)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Duyệt qua các task thuộc trang này -->
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <!-- Tiêu đề task -->
                                <td><?= htmlspecialchars($task['title']) ?></td>
                                <!-- Tên người thực hiện -->
                                <td><?= htmlspecialchars($task['assistant_name']) ?></td>
                                <!-- Hiển thị mức độ ưu tiên với màu sắc (badge) tương ứng -->
                                <td>
                                    <?php 
                                    $pColor = 'secondary';
                                    if ($task['priority'] == 'high') $pColor = 'danger';
                                    elseif ($task['priority'] == 'medium') $pColor = 'warning';
                                    else $pColor = 'info';
                                    ?>
                                    <span class="badge bg-<?= $pColor ?>"><?= ucfirst($task['priority']) ?></span>
                                </td>
                                <!-- Hiển thị trạng thái tiến độ với màu sắc (badge) tương ứng -->
                                <td>
                                    <?php 
                                    $sColor = 'secondary';
                                    if ($task['status'] == 'completed') $sColor = 'success';
                                    elseif ($task['status'] == 'in_progress') $sColor = 'primary';
                                    else $sColor = 'warning text-dark';
                                    ?>
                                    <span class="badge bg-<?= $sColor ?>"><?= ucfirst(str_replace('_', ' ', $task['status'])) ?></span>
                                </td>
                                <!-- Hạn chót, định dạng d/m/Y -->
                                <td><?= $task['due_date'] ? htmlspecialchars(date('d/m/Y', strtotime($task['due_date']))) : '<span class="text-muted">None</span>' ?></td>
                                <!-- Các nút thao tác Edit và Delete dành cho Mangaka -->
                                <td>
                                    <!-- Nút Sửa chuyển hướng sang TaskController@edit -->
                                    <a href="<?= BASE_PATH ?>/index.php?controller=task&action=edit&id=<?= $task['task_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <!-- Nút Xóa thực hiện qua form POST để bảo mật -->
                                    <form action="<?= BASE_PATH ?>/index.php?controller=task&action=delete&id=<?= $task['task_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa task này?');">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- Thông báo khi chưa có task nào -->
            <p class="text-muted mb-0">Chưa có task nào được giao cho trang này.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
